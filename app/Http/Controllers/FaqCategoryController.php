<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = FaqCategory::withCount('faqs')->orderBy('sort_order')->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return view('admin.faq_categories.index', [
            'categories' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.faq_categories.create', [
            'category' => new FaqCategory(['is_active' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        FaqCategory::create($this->validatedData($request));

        return redirect()->route('faq-categories.index')
            ->with(['message' => 'FAQ category created successfully.', 'message_type' => 'success']);
    }

    public function edit(FaqCategory $faqCategory)
    {
        $this->authorizeAdmin();

        return view('admin.faq_categories.edit', ['category' => $faqCategory]);
    }

    public function update(Request $request, FaqCategory $faqCategory)
    {
        $this->authorizeAdmin();

        $faqCategory->update($this->validatedData($request));

        return redirect()->route('faq-categories.index')
            ->with(['message' => 'FAQ category updated successfully.', 'message_type' => 'success']);
    }

    public function destroy(FaqCategory $faqCategory)
    {
        $this->authorizeAdmin();

        $faqCategory->delete();

        return redirect()->route('faq-categories.index')
            ->with(['message' => 'FAQ category deleted successfully.', 'message_type' => 'success']);
    }

    protected function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'icon' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }
}
