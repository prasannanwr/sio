<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\DeliveryManLocation;
use App\Models\ShippingCost;
use App\Models\User;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use App\Models\City;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Vehicle::with(['deliveryMan', 'shippingCost', 'originCity.country', 'destinationCity.country'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%")
                    ->orWhereHas('shippingCost', function ($query) use ($search) {
                        $query->where('country_name', 'like', "%{$search}%")
                            ->orWhere('country_iso_2', 'like', "%{$search}%")
                            ->orWhere('weight', 'like', "%{$search}%")
                            ->orWhere('cost', 'like', "%{$search}%");
                    })
                    ->orWhereHas('deliveryMan', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('delivery_man_id')) {
            $query->where('delivery_man_id', $request->delivery_man_id);
        }

        $vehicles = $query->paginate(15);
        $vehicleTypes = Vehicle::types();
        $deliveryMen = $this->deliveryMen();

        return view('admin.vehicles.index', compact('vehicles', 'vehicleTypes', 'deliveryMen'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.vehicles.create', [
            'vehicle' => new Vehicle(),
            'vehicleTypes' => Vehicle::types(),
            'statuses' => Vehicle::statuses(),
            'deliveryMen' => $this->deliveryMen(),
            'selectedShippingCost' => $this->selectedShippingCost(session()->getOldInput('shipping_cost_id')),
            'shippingCostSearchUrl' => route('vehicles.shipping-costs.search'),
            'locationSearchUrl' => route('vehicles.locations.search'),
            'selectedOriginLocation' => $this->selectedLocation(session()->getOldInput('origin_location')),
            'selectedDestinationLocation' => $this->selectedLocation(session()->getOldInput('destination_location')),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request);
        $data['documents'] = $this->storeDocuments($request);

        $originCity = City::findOrFail($request->origin_location);

        $destinationCity = City::findOrFail($request->destination_location);

        /*
        |--------------------------------------------------------------------------
        | Calculate Distance
        |--------------------------------------------------------------------------
        */

        try {
            $distanceKm = $this->calculateDistance(
                $originCity->latitude,
                $originCity->longitude,
                $destinationCity->latitude,
                $destinationCity->longitude
            );
            $data['distance_km'] = $distanceKm['distance_km'];
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['distance_km' => $e->getMessage()]);
        }

        $createdDeliveryMan = false;

        DB::transaction(function () use ($request, &$data, &$createdDeliveryMan) {
            if ($request->boolean('create_delivery_man')) {
                $data['delivery_man_id'] = $this->createDeliveryMan($request)->id;
                $createdDeliveryMan = true;
            }

            Vehicle::create($data);
        });

        $this->saveDeliveryManLocation($data['delivery_man_id'] ?? null, $request);
        $this->saveDeliveryManPhone($data['delivery_man_id'] ?? null, $request);

        return redirect()
            ->route('vehicles.index')
            ->with([
                'message' => $createdDeliveryMan
                    ? 'Vehicle created and delivery man assigned successfully.'
                    : 'Vehicle created successfully.',
                'message_type' => 'success',
            ]);
    }

    public function calculateDistance($startLat, $startLng, $endLat, $endLng)
    {
        $apiKey = env('OPENROUTE_API_KEY');

        if (! $apiKey) {
            throw new Exception('OPENROUTE_API_KEY is not configured.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(20)->post(
                'https://api.openrouteservice.org/v2/directions/driving-car',
                [
                    'coordinates' => [
                        [(float) $startLng, (float) $startLat],
                        [(float) $endLng, (float) $endLat],
                    ],
                ]
            );
        } catch (\Throwable $e) {
            throw new Exception('Distance calculation request failed: ' . $e->getMessage());
        }

        if (! $response->successful()) {
            throw new Exception('Distance calculation failed (OpenRouteService HTTP ' . $response->status() . '). Please try again later.');
        }

        $summary = $response->json('routes.0.summary');

        if (! isset($summary['distance'])) {
            throw new Exception('Distance calculation did not return a route for the selected cities.');
        }

        return [
            'distance_km' => round(((float) $summary['distance']) / 1000, 2),
            'duration_minutes' => isset($summary['duration']) ? round(((float) $summary['duration']) / 60, 2) : null,
        ];
    }

    public function show(Vehicle $vehicle)
    {
        return redirect()->route('vehicles.edit', $vehicle);
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeAdmin();

        $vehicle->load(['deliveryMan', 'shippingCost']);
        $selectedShippingCostId = session()->getOldInput('shipping_cost_id', $vehicle->shipping_cost_id);

        return view('admin.vehicles.edit', [
            'vehicle' => $vehicle,
            'vehicleTypes' => Vehicle::types(),
            'statuses' => Vehicle::statuses(),
            'deliveryMen' => $this->deliveryMen(),
            'selectedShippingCost' => $this->selectedShippingCost($selectedShippingCostId) ?: $vehicle->shippingCost,
            'shippingCostSearchUrl' => route('vehicles.shipping-costs.search'),
            'locationSearchUrl' => route('vehicles.locations.search'),
            'selectedOriginLocation' => $this->selectedLocation(session()->getOldInput('origin_location', $vehicle->origin_location)),
            'selectedDestinationLocation' => $this->selectedLocation(session()->getOldInput('destination_location', $vehicle->destination_location)),
            'currentLocation' => $vehicle->delivery_man_id
                ? DeliveryManLocation::where('delivery_man_id', $vehicle->delivery_man_id)->first()
                : null,
        ]);
    }

    public function searchShippingCosts(Request $request)
    {
        $this->authorizeAdmin();

        if (! Schema::hasTable('shipping_costs')) {
            return response()->json(['results' => []]);
        }

        $term = trim((string) $request->input('q', $request->input('term', '')));

        $query = ShippingCost::query()
            ->select('id', 'weight', 'country_name', 'country_iso_2', 'cost');

        if ($term !== '') {
            $query->where(function ($query) use ($term) {
                $query->where('country_name', 'like', "%{$term}%")
                    ->orWhere('country_iso_2', 'like', "%{$term}%")
                    ->orWhere('weight', 'like', "%{$term}%")
                    ->orWhere('cost', 'like', "%{$term}%");
            });
        }

        $results = $query
            ->orderBy('country_name')
            ->orderBy('weight')
            ->limit(25)
            ->get()
            ->map(function (ShippingCost $shippingCost) {
                return [
                    'id' => $shippingCost->id,
                    'text' => $shippingCost->display_label,
                ];
            });

        return response()->json(['results' => $results]);
    }

    public function searchVehicleLocation(Request $request)
    {
        $this->authorizeAdmin();

        $term = trim($request->get('q', ''));

        $query = DB::table('cities')
            ->join('countries', 'cities.country_id', '=', 'countries.id')
            ->select(
                'cities.id',
                'cities.name as city_name',
                'countries.name as country_name'
            );

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('cities.name', 'LIKE', "%{$term}%")
                ->orWhere('countries.name', 'LIKE', "%{$term}%");
            });
        }

        $results = $query
            ->orderBy('cities.name')
            ->limit(25)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->city_name . ', ' . $item->country_name,
                ];
            });

        return response()->json([
            'results' => $results
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeAdmin();

        $data = $this->validatedData($request, $vehicle);
        $data['documents'] = $this->updatedDocuments($request, $vehicle);

        $vehicle->update($data);

        $this->saveDeliveryManLocation($vehicle->delivery_man_id, $request);
        $this->saveDeliveryManPhone($vehicle->delivery_man_id, $request);

        return redirect()
            ->route('vehicles.index')
            ->with(['message' => 'Vehicle updated successfully.', 'message_type' => 'success']);
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeAdmin();

        foreach ($vehicle->documents ?: [] as $document) {
            $this->deleteDocument($document['path'] ?? null);
        }

        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with(['message' => 'Vehicle deleted successfully.', 'message_type' => 'success']);
    }

    protected function validatedData(Request $request, ?Vehicle $vehicle = null): array
    {
        $vehicleId = $vehicle ? $vehicle->id : null;

        // Clean the delivery man phone before validating uniqueness against the stored value.
        if ($request->filled('delivery_man_phone')) {
            $request->merge(['delivery_man_phone' => $this->cleanPhone($request->input('delivery_man_phone'))]);
        }

        $ignoreDeliveryManId = $vehicle ? $vehicle->delivery_man_id : $request->input('delivery_man_id');

        $rules = [
            'type' => ['required', Rule::in(Vehicle::types())],
            'brand' => ['required', 'string', 'max:120'],
            'model' => ['required', 'string', 'max:120'],
            'plate_number' => [
                'required',
                'string',
                'max:60',
                Rule::unique('vehicles', 'plate_number')->ignore($vehicleId),
            ],
            'color' => ['required', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . ((int) date('Y') + 1)],
            'delivery_man_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('user_type', ['dispatcher', 'courier']);
                }),
            ],
            'shipping_cost_id' => $this->shippingCostRules(),
            'origin_location' => $this->locationRules(),
            'destination_location' => $this->locationRules(),
            'weight_range' => ['nullable', Rule::in(array_map(fn ($i) => (string) $i, range(1, 50)))],
            'rate_per_km_per_kg' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            // Delivery man can set the price in EUR and/or Naira (either or both).
            'rate_eur' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'rate_naira' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'status' => ['required', Rule::in(Vehicle::statuses())],
            'documents' => $vehicle ? ['nullable', 'array', 'max:5'] : ['required', 'array', 'min:2', 'max:5'],
            'documents.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'remove_documents' => ['nullable', 'array'],
            'remove_documents.*' => ['string'],
            // Admin-set current GPS location for the assigned delivery man (optional, both required together).
            'current_latitude' => ['nullable', 'required_with:current_longitude', 'numeric', 'between:-90,90'],
            'current_longitude' => ['nullable', 'required_with:current_latitude', 'numeric', 'between:-180,180'],
            'delivery_man_phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($ignoreDeliveryManId)],
        ];

        if (! $vehicle) {
            $rules = array_merge($rules, [
                'create_delivery_man' => ['nullable', 'boolean'],
                'new_delivery_man' => ['nullable', 'array'],
                'new_delivery_man.name' => ['required_if:create_delivery_man,1', 'nullable', 'string', 'max:120'],
                'new_delivery_man.email' => [
                    'required_if:create_delivery_man,1',
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
                'new_delivery_man.phone' => [
                    'required_if:create_delivery_man,1',
                    'nullable',
                    'string',
                    'max:30',
                    Rule::unique('users', 'phone'),
                ],
                'new_delivery_man.password' => ['required_if:create_delivery_man,1', 'nullable', 'string', 'min:8', 'confirmed'],
            ]);
        }

        $data = $request->validate($rules);

        unset($data['documents'], $data['cargo_documents'], $data['remove_documents'], $data['create_delivery_man'], $data['new_delivery_man'], $data['current_latitude'], $data['current_longitude'], $data['delivery_man_phone']);

        return $data;
    }

    protected function createDeliveryMan(Request $request): User
    {
        $deliveryMan = $request->input('new_delivery_man', []);
        $deliveryMan['phone'] = $this->cleanPhone($deliveryMan['phone'] ?? null);
        $now = now();

        // $user = User::create([
        //     'name' => $deliveryMan['name'],
        //     'email' => $deliveryMan['email'],
        //     'phone' => $deliveryMan['phone'],
        //     'phone_verified_at' => $now,
        //     'password' => Hash::make($deliveryMan['password']),
        //     'user_type' => 'courier',
        //     'country' => config('auth_api.defaults.country_id', 1),
        //     'blocked' => false,
        // ]);

        // Courier::create([
        //     'user_id' => $user->id,
        //     'name' => $deliveryMan['name'],
        //     'phone' => $deliveryMan['phone'],
        //     'vehicle_type' => $request->input('type'),
        //     'status' => 'active',
        // ]);

        $user = User::create([
            'name' => $deliveryMan['name'],
            'email' => $deliveryMan['email'],
            'phone' => $deliveryMan['phone'],
            'phone_verified_at' => $now,
            'password' => Hash::make($deliveryMan['password']),
            'user_type' => 'courier',
            'country' => config('auth_api.defaults.country_id', 1),
            'blocked' => false,
        ]);

        Courier::create([
            'user_id' => $user->id,
            'name' => $deliveryMan['name'],
            'phone' => $deliveryMan['phone'],

            'vehicle_type' => $request->input('type'),

            'origin_location' => $request->input('origin_location'),

            'destination_location' => $request->input('destination_location'),

            'weight_range' => $request->input('weight_range'),

            'rate_per_km_per_kg' => $request->input('rate_per_km_per_kg'),

            'rate_eur' => $request->input('rate_eur'),

            'rate_naira' => $request->input('rate_naira'),

            'status' => 'active',
        ]);

        return $user;
    }

    protected function storeDocuments(Request $request): array
    {
        if (! $request->hasFile('documents')) {
            return [];
        }

        $files = $request->file('documents');
        $files = is_array($files) ? $files : [$files];
        $documents = [];
        $directory = public_path('uploads/vehicles');

        File::ensureDirectoryExists($directory);

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'file';
            $fileName = time() . '_' . Str::random(8) . '_' . (Str::slug($baseName) ?: 'document') . '.' . $extension;
            $mime = $file->getClientMimeType();
            $size = $file->getSize();

            $file->move($directory, $fileName);

            $documents[] = [
                'name' => $originalName,
                'path' => 'uploads/vehicles/' . $fileName,
                'category' => 'vehicle_photo',
                'mime' => $mime,
                'size' => $size,
                'uploaded_at' => now()->toIso8601String(),
            ];
        }

        return $documents;
    }

    protected function storeCargoDocuments(Request $request): array
    {
        $documents = [];
        $directory = public_path('uploads/vehicles');

        File::ensureDirectoryExists($directory);

        foreach ($this->cargoDocumentFields() as $field => $label) {
            $input = 'cargo_documents.' . $field;

            if (! $request->hasFile($input)) {
                continue;
            }

            $file = $request->file($input);
            $originalName = $file->getClientOriginalName();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'file';
            $fileName = time() . '_' . Str::random(8) . '_' . (Str::slug($field . '-' . $baseName) ?: $field) . '.' . $extension;

            $file->move($directory, $fileName);

            $documents[] = [
                'name' => $originalName,
                'path' => 'uploads/vehicles/' . $fileName,
                'category' => 'cargo_document',
                'document_type' => $field,
                'label' => $label,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_at' => now()->toIso8601String(),
            ];
        }

        return $documents;
    }

    protected function updatedDocuments(Request $request, Vehicle $vehicle): array
    {
        $removeDocuments = $request->input('remove_documents', []);
        $newCargoDocuments = $this->storeCargoDocuments($request);
        $newCargoDocumentTypes = collect($newCargoDocuments)->pluck('document_type')->filter()->all();
        $documents = [];

        foreach ($vehicle->documents ?: [] as $document) {
            $path = $document['path'] ?? null;

            if ($path && in_array($path, $removeDocuments, true)) {
                $this->deleteDocument($path);
                continue;
            }

            if (($document['category'] ?? null) === 'cargo_document'
                && in_array($document['document_type'] ?? null, $newCargoDocumentTypes, true)) {
                $this->deleteDocument($path);
                continue;
            }

            $documents[] = $document;
        }

        return array_merge($documents, $this->storeDocuments($request), $newCargoDocuments);
    }

    protected function deleteDocument(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, 'uploads/vehicles/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function cargoDocumentFields(): array
    {
        return [
            'invoice_document' => 'Invoice',
            'packing_list_document' => 'Packing List',
            'id_document' => 'ID',
            'permit_document' => 'Permit',
        ];
    }

    protected function deliveryMen()
    {
        return User::whereIn('user_type', ['dispatcher', 'courier'])
            ->where('blocked', 0)
            ->orderBy('name')
            ->get();
    }

    protected function selectedShippingCost($shippingCostId): ?ShippingCost
    {
        if (! $shippingCostId || ! Schema::hasTable('shipping_costs')) {
            return null;
        }

        return ShippingCost::find($shippingCostId);
    }

    protected function selectedLocation($cityId): ?object
    {
        if (! $cityId || ! Schema::hasTable('cities') || ! Schema::hasTable('countries')) {
            return null;
        }

        $location = DB::table('cities')
            ->join('countries', 'cities.country_id', '=', 'countries.id')
            ->select('cities.id', 'cities.name as city_name', 'countries.name as country_name')
            ->where('cities.id', $cityId)
            ->first();

        if (! $location) {
            return null;
        }

        $location->display_label = $location->city_name . ', ' . $location->country_name;

        return $location;
    }

    protected function shippingCostRules(): array
    {
        $rules = ['nullable', 'integer'];

        if (Schema::hasTable('shipping_costs')) {
            $rules[] = Rule::exists('shipping_costs', 'id');
        }

        return $rules;
    }

    protected function locationRules(): array
    {
        $rules = ['nullable', 'integer'];

        if (Schema::hasTable('cities')) {
            $rules[] = Rule::exists('cities', 'id');
        }

        return $rules;
    }

    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }

    /**
     * Persist an admin-set current GPS location for the delivery man (same store the app GPS pings use).
     */
    protected function saveDeliveryManLocation(?int $deliveryManId, Request $request): void
    {
        if (! $deliveryManId) {
            return;
        }

        $lat = $request->input('current_latitude');
        $lng = $request->input('current_longitude');

        if ($lat === null || $lat === '' || $lng === null || $lng === '') {
            return;
        }

        DeliveryManLocation::updateOrCreate(
            ['delivery_man_id' => $deliveryManId],
            ['latitude' => $lat, 'longitude' => $lng, 'recorded_at' => now()]
        );
    }

    /**
     * Persist an admin-set phone number for the delivery man (value is already cleaned in validatedData).
     */
    protected function saveDeliveryManPhone(?int $deliveryManId, Request $request): void
    {
        if (! $deliveryManId) {
            return;
        }

        $phone = $request->input('delivery_man_phone');

        if ($phone === null || $phone === '') {
            return;
        }

        User::where('id', $deliveryManId)->update(['phone' => $phone]);
        Courier::where('user_id', $deliveryManId)->update(['phone' => $phone]);
    }

    /**
     * Normalize a phone number for storage: strip the leading "+", all whitespace, and leading zeros.
     */
    protected function cleanPhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $phone = preg_replace('/\s+/', '', trim($phone)); // strip spaces/whitespace
        $phone = ltrim($phone, '+');                       // strip leading +
        $phone = ltrim($phone, '0');                       // strip leading zeros

        return $phone === '' ? null : $phone;
    }
}
