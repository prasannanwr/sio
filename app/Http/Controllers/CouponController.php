<?php

namespace App\Http\Controllers;

use App\Constraints\PreferredCurrency;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Coupon::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $coupons = $query->paginate(15)->withQueryString();

        return view('admin.coupons.index', [
            'coupons' => $coupons,
            'currencies' => PreferredCurrency::values(),
        ]);
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.coupons.create', [
            'coupon' => new Coupon(['is_active' => true, 'discount_type' => Coupon::TYPE_PERCENTAGE]),
            'discountTypes' => Coupon::discountTypes(),
            'currencies' => PreferredCurrency::values(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request);
        Coupon::create($data);

        return redirect()
            ->route('coupons.index')
            ->with(['message' => 'Coupon created successfully.', 'message_type' => 'success']);
    }

    public function edit(Coupon $coupon)
    {
        $this->authorizeAdmin();

        return view('admin.coupons.edit', [
            'coupon' => $coupon,
            'discountTypes' => Coupon::discountTypes(),
            'currencies' => PreferredCurrency::values(),
        ]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request, $coupon);
        $coupon->update($data);

        return redirect()
            ->route('coupons.index')
            ->with(['message' => 'Coupon updated successfully.', 'message_type' => 'success']);
    }

    public function destroy(Coupon $coupon)
    {
        $this->authorizeAdmin();

        $coupon->delete();

        return redirect()
            ->route('coupons.index')
            ->with(['message' => 'Coupon deleted successfully.', 'message_type' => 'success']);
    }

    protected function validatedData(Request $request, ?Coupon $coupon = null): array
    {
        if ($request->filled('code')) {
            $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);
        }

        if ($request->filled('currency')) {
            $request->merge(['currency' => strtoupper(trim((string) $request->input('currency')))]);
        }

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:60', Rule::unique('coupons', 'code')->ignore(optional($coupon)->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', Rule::in(Coupon::discountTypes())],
            'discount_value' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'currency' => ['required', Rule::in(PreferredCurrency::values())],
            'min_order_value' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Percentage discounts cannot exceed 100%.
        $validator->after(function ($validator) use ($request) {
            if ($request->input('discount_type') === Coupon::TYPE_PERCENTAGE
                && (float) $request->input('discount_value') > 100) {
                $validator->errors()->add('discount_value', 'A percentage discount cannot exceed 100.');
            }
        });

        $data = $validator->validate();
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }
}
