<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CargoDocumentController extends Controller
{
    protected const DOCUMENT_FIELDS = [
        'invoice_document' => 'Invoice',
        'packing_list_document' => 'Packing List',
        'id_document' => 'ID',
        'permit_document' => 'Permit',
    ];

    public function show(Request $request, string $trackingId): JsonResponse
    {
        $order = $this->findOrder($trackingId);

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Cargo order was not found.',
            ], 404);
        }

        if (! $this->canAccessOrder($request, $order)) {
            return $this->forbiddenResponse();
        }

        return response()->json([
            'status' => true,
            'message' => 'Cargo documents retrieved successfully.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'documents' => $this->documentPayload($order),
            ],
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        if ($request->filled('tracking_id')) {
            $request->merge(['tracking_id' => trim((string) $request->input('tracking_id'))]);
        }

        $validator = Validator::make($request->all(), array_merge([
            'tracking_id' => ['required', 'string', 'max:120'],
        ], $this->documentValidationRules()));

        $validator->after(function ($validator) use ($request) {
            if (! $this->hasUploadedDocument($request)) {
                $validator->errors()->add('documents', 'At least one cargo document file is required.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = $this->findOrder($request->input('tracking_id'));

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Cargo order was not found.',
            ], 404);
        }

        if (! $this->canAccessOrder($request, $order)) {
            return $this->forbiddenResponse();
        }

        $updates = $this->storeDocuments($request, $order);
        $order->forceFill($updates)->save();

        return response()->json([
            'status' => true,
            'message' => 'Cargo documents uploaded successfully.',
            'data' => [
                'tracking_id' => $order->tracking_id,
                'documents' => $this->documentPayload($order->fresh()),
            ],
        ]);
    }

    protected function findOrder(string $trackingId): ?Order
    {
        return Order::where('tracking_id', trim($trackingId))->first();
    }

    protected function canAccessOrder(Request $request, Order $order): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        if (in_array($user->user_type, ['admin', 'logistics'], true)) {
            return true;
        }

        if ((int) $order->dispatcher_id === (int) $user->id) {
            return true;
        }

        // The customer (normal user) who booked the cargo owns the order and may upload its documents.
        return $order->customer && (int) $order->customer->user_id === (int) $user->id;
    }

    protected function forbiddenResponse(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'You are not allowed to upload documents for this cargo order.',
        ], 403);
    }

    protected function documentValidationRules(): array
    {
        return collect(array_keys(self::DOCUMENT_FIELDS))
            ->mapWithKeys(fn (string $field) => [
                $field => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ])
            ->all();
    }

    protected function hasUploadedDocument(Request $request): bool
    {
        foreach (array_keys(self::DOCUMENT_FIELDS) as $field) {
            if ($request->hasFile($field)) {
                return true;
            }
        }

        return false;
    }

    protected function storeDocuments(Request $request, Order $order): array
    {
        $updates = [];
        $directory = public_path('uploads/cargo-documents');

        File::ensureDirectoryExists($directory);

        foreach (array_keys(self::DOCUMENT_FIELDS) as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $this->deleteDocument($order->{$field});

            $file = $request->file($field);
            $originalName = $file->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'file';
            $fileName = now()->format('YmdHis') . '_' . Str::random(8) . '_' . Str::slug($order->tracking_id . '-' . $field . '-' . $baseName) . '.' . $extension;

            $file->move($directory, $fileName);
            $updates[$field] = 'uploads/cargo-documents/' . $fileName;
        }

        return $updates;
    }

    protected function deleteDocument(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, 'uploads/cargo-documents/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function documentPayload(Order $order): array
    {
        return collect(self::DOCUMENT_FIELDS)
            ->mapWithKeys(function (string $label, string $field) use ($order) {
                $path = $order->{$field};

                return [
                    $field => [
                        'label' => $label,
                        'path' => $path,
                        'url' => $path ? asset($path) : null,
                    ],
                ];
            })
            ->all();
    }
}
