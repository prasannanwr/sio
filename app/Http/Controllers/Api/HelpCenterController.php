<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelpCenterController extends Controller
{
    /**
     * GET /api/help-center/categories — active categories for the Help Center tiles.
     */
    public function categories(): JsonResponse
    {
        $categories = FaqCategory::query()
            ->where('is_active', true)
            ->withCount(['faqs' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Help center categories retrieved successfully.',
            'total' => $categories->count(),
            'data' => $categories->map(fn (FaqCategory $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'icon' => $c->icon,
                'faqs_count' => $c->faqs_count,
            ])->values(),
        ]);
    }

    /**
     * GET /api/help-center/faqs?category_id=&popular=1&search= — active FAQs.
     */
    public function faqs(Request $request): JsonResponse
    {
        $query = Faq::query()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('sort_order')
            ->orderByDesc('id');

        if ($request->filled('category_id')) {
            $query->where('faq_category_id', $request->query('category_id'));
        }

        if ($request->boolean('popular')) {
            $query->where('is_popular', true);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $faqs = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Help center FAQs retrieved successfully.',
            'total' => $faqs->count(),
            'data' => $faqs->map(fn (Faq $f) => $this->faqPayload($f))->values(),
        ]);
    }

    protected function faqPayload(Faq $faq): array
    {
        return [
            'id' => $faq->id,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'is_popular' => (bool) $faq->is_popular,
            'category_id' => $faq->faq_category_id,
            'category_name' => optional($faq->category)->name,
        ];
    }
}
