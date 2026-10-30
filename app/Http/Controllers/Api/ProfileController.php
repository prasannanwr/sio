<?php

namespace App\Http\Controllers\Api;

use App\Constraints\PreferredCurrency;
use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Profile retrieved successfully.',
            'data' => $this->profilePayload($request->user()),
        ]);
    }

    /**
     * PUT/PATCH /api/profile — update name parts, email, phone.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($request->has('preferred_currency')) {
            $request->merge(['preferred_currency' => strtoupper(trim((string) $request->input('preferred_currency')))]);
        }

        $validated = $request->validate([
            'first_name' => ['sometimes', 'nullable', 'string', 'max:120'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:120'],
            'email' => ['sometimes', 'required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:40'],
            'preferred_currency' => ['sometimes', 'required', Rule::in(PreferredCurrency::values())],
        ]);

        foreach (['first_name', 'last_name', 'email', 'phone', 'preferred_currency'] as $field) {
            if (array_key_exists($field, $validated)) {
                $user->{$field} = $validated[$field];
            }
        }

        // Keep the single `name` field in sync so the rest of the system stays consistent.
        $fullName = trim(sprintf('%s %s', (string) $user->first_name, (string) $user->last_name));
        if ($fullName !== '') {
            $user->name = $fullName;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'data' => $this->profilePayload($user->fresh()),
        ]);
    }

    /**
     * PUT/PATCH /api/profile/preferred-currency — toggle the user's default currency workspace (EUR or NGN).
     */
    public function updatePreferredCurrency(Request $request): JsonResponse
    {
        $request->merge(['currency' => strtoupper(trim((string) $request->input('currency')))]);

        $validated = $request->validate([
            'currency' => ['required', Rule::in(PreferredCurrency::values())],
        ]);

        $user = $request->user();
        $user->preferred_currency = $validated['currency'];
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Preferred currency updated successfully.',
            'data' => [
                'preferred_currency' => $user->preferred_currency,
            ],
        ]);
    }

    /**
     * GET /api/profile/addresses
     */
    public function addresses(Request $request): JsonResponse
    {
        $addresses = $request->user()->shippingAddresses()
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Shipping addresses retrieved successfully.',
            'total' => $addresses->count(),
            'data' => $addresses->map(fn (ShippingAddress $a) => $this->addressPayload($a))->values(),
        ]);
    }

    /**
     * POST /api/profile/addresses
     */
    public function storeAddress(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $this->validateAddress($request);

        // First address is always the default; otherwise honour the requested flag.
        $makeDefault = $request->boolean('is_default') || $user->shippingAddresses()->count() === 0;

        $address = DB::transaction(function () use ($user, $validated, $makeDefault) {
            if ($makeDefault) {
                $user->shippingAddresses()->update(['is_default' => false]);
            }

            return $user->shippingAddresses()->create(array_merge($validated, [
                'is_default' => $makeDefault,
            ]));
        });

        return response()->json([
            'status' => true,
            'message' => 'Shipping address added successfully.',
            'data' => $this->addressPayload($address),
        ], 201);
    }

    /**
     * PUT/PATCH /api/profile/addresses/{id}
     */
    public function updateAddress(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $address = $user->shippingAddresses()->find($id);

        if (! $address) {
            return $this->addressNotFound();
        }

        $validated = $this->validateAddress($request, true);
        $makeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($user, $address, $validated, $makeDefault, $request) {
            if ($makeDefault) {
                $user->shippingAddresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
                $validated['is_default'] = true;
            } elseif ($request->has('is_default')) {
                // Explicitly unsetting default is only allowed if it leaves the user with another default.
                $validated['is_default'] = $address->is_default;
            }

            $address->fill($validated)->save();
        });

        return response()->json([
            'status' => true,
            'message' => 'Shipping address updated successfully.',
            'data' => $this->addressPayload($address->fresh()),
        ]);
    }

    /**
     * DELETE /api/profile/addresses/{id}
     */
    public function destroyAddress(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $address = $user->shippingAddresses()->find($id);

        if (! $address) {
            return $this->addressNotFound();
        }

        DB::transaction(function () use ($user, $address) {
            $wasDefault = $address->is_default;
            $address->delete();

            // Promote the most recent remaining address to default.
            if ($wasDefault) {
                $next = $user->shippingAddresses()->orderByDesc('id')->first();
                if ($next) {
                    $next->update(['is_default' => true]);
                }
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Shipping address deleted successfully.',
        ]);
    }

    /**
     * POST /api/profile/addresses/{id}/default
     */
    public function setDefaultAddress(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $address = $user->shippingAddresses()->find($id);

        if (! $address) {
            return $this->addressNotFound();
        }

        DB::transaction(function () use ($user, $address) {
            $user->shippingAddresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Default shipping address updated successfully.',
            'data' => $this->addressPayload($address->fresh()),
        ]);
    }

    protected function validateAddress(Request $request, bool $partial = false): array
    {
        $rule = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'street_address' => [$rule, 'string', 'max:255'],
            'city' => [$rule, 'string', 'max:120'],
            'state' => ['sometimes', 'nullable', 'string', 'max:120'],
            'zip_code' => ['sometimes', 'nullable', 'string', 'max:30'],
            'country' => [$rule, 'string', 'max:120'],
            'is_default' => ['sometimes', 'boolean'],
        ]);
    }

    protected function profilePayload(User $user): array
    {
        [$firstName, $lastName] = $this->nameParts($user);
        $default = $user->shippingAddresses->firstWhere('is_default', true);

        return [
            'id' => $user->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'preferred_currency' => $user->preferred_currency ?: PreferredCurrency::DEFAULT,
            'default_shipping_address' => $default ? $this->addressPayload($default) : null,
            'shipping_addresses' => $user->shippingAddresses
                ->sortByDesc('is_default')
                ->map(fn (ShippingAddress $a) => $this->addressPayload($a))
                ->values(),
        ];
    }

    protected function addressPayload(ShippingAddress $address): array
    {
        return [
            'id' => $address->id,
            'street_address' => $address->street_address,
            'city' => $address->city,
            'state' => $address->state,
            'zip_code' => $address->zip_code,
            'country' => $address->country,
            'is_default' => (bool) $address->is_default,
            'created_at' => optional($address->created_at)->toIso8601String(),
            'updated_at' => optional($address->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Use the explicit columns when set, otherwise derive parts from the legacy single `name`.
     */
    protected function nameParts(User $user): array
    {
        if ($user->first_name || $user->last_name) {
            return [$user->first_name, $user->last_name];
        }

        $parts = preg_split('/\s+/', trim((string) $user->name), 2);

        return [$parts[0] ?? null, $parts[1] ?? null];
    }

    protected function addressNotFound(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Shipping address was not found.',
        ], 404);
    }
}
