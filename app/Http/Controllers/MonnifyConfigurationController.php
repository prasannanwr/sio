<?php

namespace App\Http\Controllers;

use App\Models\MonnifyConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonnifyConfigurationController extends Controller
{
    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }

    public function index()
    {
        $this->authorizeAdmin();

        $configurations = MonnifyConfiguration::latest()->paginate(15);

        return view('admin.monnify_configurations.index', compact('configurations'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.monnify_configurations.create', [
            'configuration' => new MonnifyConfiguration([
                'base_url' => 'https://sandbox.monnify.com',
                'currency' => 'NGN',
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validated($request);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        DB::transaction(function () use ($data) {
            if ($data['is_active']) {
                MonnifyConfiguration::where('is_active', true)->update(['is_active' => false]);
            }
            MonnifyConfiguration::create($data);
        });

        return redirect()
            ->route('monnify-configurations.index')
            ->with(['message' => 'Monnify configuration created successfully.', 'message_type' => 'success']);
    }

    public function edit(MonnifyConfiguration $monnifyConfiguration)
    {
        $this->authorizeAdmin();

        return view('admin.monnify_configurations.edit', ['configuration' => $monnifyConfiguration]);
    }

    public function update(Request $request, MonnifyConfiguration $monnifyConfiguration)
    {
        $this->authorizeAdmin();

        $data = $this->validated($request, $monnifyConfiguration);

        // Keep existing secrets when left blank on update.
        foreach (['secret_key', 'webhook_secret'] as $secret) {
            if (blank($data[$secret] ?? null)) {
                unset($data[$secret]);
            }
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        DB::transaction(function () use ($data, $monnifyConfiguration) {
            if ($data['is_active']) {
                MonnifyConfiguration::where('is_active', true)
                    ->where('id', '!=', $monnifyConfiguration->id)
                    ->update(['is_active' => false]);
            }
            $monnifyConfiguration->update($data);
        });

        return redirect()
            ->route('monnify-configurations.index')
            ->with(['message' => 'Monnify configuration updated successfully.', 'message_type' => 'success']);
    }

    public function destroy(MonnifyConfiguration $monnifyConfiguration)
    {
        $this->authorizeAdmin();

        $monnifyConfiguration->delete();

        return redirect()
            ->route('monnify-configurations.index')
            ->with(['message' => 'Monnify configuration deleted successfully.', 'message_type' => 'success']);
    }

    public function activate(MonnifyConfiguration $monnifyConfiguration)
    {
        $this->authorizeAdmin();

        DB::transaction(function () use ($monnifyConfiguration) {
            MonnifyConfiguration::where('is_active', true)->update(['is_active' => false]);
            $monnifyConfiguration->update(['is_active' => true]);
        });

        return redirect()
            ->route('monnify-configurations.index')
            ->with(['message' => "Monnify configuration \"{$monnifyConfiguration->name}\" is now active.", 'message_type' => 'success']);
    }

    protected function validated(Request $request, ?MonnifyConfiguration $existing = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'api_key' => ['required', 'string', 'max:191'],
            'secret_key' => [$existing ? 'nullable' : 'required', 'string', 'max:191'],
            'contract_code' => ['required', 'string', 'max:60'],
            'base_url' => ['required', 'url', 'max:191'],
            'currency' => ['required', 'string', 'max:5'],
            'webhook_secret' => ['nullable', 'string', 'max:191'],
            'redirect_url' => ['nullable', 'url', 'max:191'],
            'source_account_number' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
