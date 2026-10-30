<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Faq::with('category')->orderBy('sort_order')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('faq_category_id')) {
            $query->where('faq_category_id', $request->faq_category_id);
        }

        return view('admin.faqs.index', [
            'faqs' => $query->paginate(15)->withQueryString(),
            'categories' => FaqCategory::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.faqs.create', [
            'faq' => new Faq(['is_active' => true, 'is_popular' => false, 'sort_order' => 0]),
            'categories' => FaqCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        Faq::create($this->validatedData($request));

        return redirect()->route('faqs.index')
            ->with(['message' => 'FAQ created successfully.', 'message_type' => 'success']);
    }

    public function edit(Faq $faq)
    {
        $this->authorizeAdmin();

        return view('admin.faqs.edit', [
            'faq' => $faq,
            'categories' => FaqCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Faq $faq)
    {
        $this->authorizeAdmin();

        $faq->update($this->validatedData($request));

        return redirect()->route('faqs.index')
            ->with(['message' => 'FAQ updated successfully.', 'message_type' => 'success']);
    }

    public function destroy(Faq $faq)
    {
        $this->authorizeAdmin();

        $faq->delete();

        return redirect()->route('faqs.index')
            ->with(['message' => 'FAQ deleted successfully.', 'message_type' => 'success']);
    }

    protected function validatedData(Request $request): array
    {
        $data = $request->validate([
            'faq_category_id' => ['nullable', 'integer', 'exists:faq_categories,id'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'is_popular' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }
}
