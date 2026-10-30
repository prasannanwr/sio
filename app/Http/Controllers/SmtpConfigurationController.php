<?php

namespace App\Http\Controllers;

use App\Models\SmtpConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SmtpConfigurationController extends Controller
{
    protected function authorizeAdmin(): void
    {
        AdminController::canAdmin();
    }

    public function index()
    {
        $this->authorizeAdmin();

        $configurations = SmtpConfiguration::latest()->paginate(15);

        return view('admin.smtp_configurations.index', compact('configurations'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.smtp_configurations.create', [
            'configuration' => new SmtpConfiguration([
                'mailer' => 'smtp',
                'port' => 465,
                'encryption' => 'ssl',
            ]),
            'encryptions' => $this->encryptionOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $this->validated($request);
        $data = $this->sanitize($data);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        DB::transaction(function () use ($data) {
            if ($data['is_active']) {
                SmtpConfiguration::where('is_active', true)->update(['is_active' => false]);
            }
            SmtpConfiguration::create($data);
        });

        return redirect()
            ->route('smtp-configurations.index')
            ->with(['message' => 'SMTP configuration created successfully.', 'message_type' => 'success']);
    }

    public function edit(SmtpConfiguration $smtpConfiguration)
    {
        $this->authorizeAdmin();

        return view('admin.smtp_configurations.edit', [
            'configuration' => $smtpConfiguration,
            'encryptions' => $this->encryptionOptions(),
        ]);
    }

    public function update(Request $request, SmtpConfiguration $smtpConfiguration)
    {
        $this->authorizeAdmin();

        $data = $this->validated($request, $smtpConfiguration);
        $data = $this->sanitize($data);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        DB::transaction(function () use ($data, $smtpConfiguration) {
            if ($data['is_active']) {
                SmtpConfiguration::where('is_active', true)
                    ->where('id', '!=', $smtpConfiguration->id)
                    ->update(['is_active' => false]);
            }
            $smtpConfiguration->update($data);
        });

        return redirect()
            ->route('smtp-configurations.index')
            ->with(['message' => 'SMTP configuration updated successfully.', 'message_type' => 'success']);
    }

    public function destroy(SmtpConfiguration $smtpConfiguration)
    {
        $this->authorizeAdmin();

        $smtpConfiguration->delete();

        return redirect()
            ->route('smtp-configurations.index')
            ->with(['message' => 'SMTP configuration deleted.', 'message_type' => 'success']);
    }

    public function activate(SmtpConfiguration $smtpConfiguration)
    {
        $this->authorizeAdmin();

        DB::transaction(function () use ($smtpConfiguration) {
            SmtpConfiguration::where('is_active', true)->update(['is_active' => false]);
            $smtpConfiguration->update(['is_active' => true]);
        });

        return redirect()
            ->route('smtp-configurations.index')
            ->with([
                'message' => "SMTP configuration \"{$smtpConfiguration->name}\" is now active.",
                'message_type' => 'success',
            ]);
    }

    public function sendTest(Request $request, SmtpConfiguration $smtpConfiguration)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            $smtpConfiguration->applyToMailConfig();

            Mail::raw(
                "This is a test email sent from the SMTP configuration \"{$smtpConfiguration->name}\".",
                function ($message) use ($data, $smtpConfiguration) {
                    $message->to($data['test_email'])
                        ->subject("SMTP test — {$smtpConfiguration->name}");
                }
            );
        } catch (\Throwable $e) {
            Log::error('SMTP test email failed', [
                'configuration_id' => $smtpConfiguration->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with([
                'message' => 'Test email failed: ' . $e->getMessage(),
                'message_type' => 'danger',
            ]);
        }

        return back()->with([
            'message' => "Test email sent to {$data['test_email']}.",
            'message_type' => 'success',
        ]);
    }

    protected function validated(Request $request, ?SmtpConfiguration $existing = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'mailer' => ['required', 'string', 'max:50'],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => [$existing ? 'nullable' : 'required', 'string', 'max:500'],
            'encryption' => ['nullable', 'in:ssl,tls'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    protected function encryptionOptions(): array
    {
        return [
            '' => 'None',
            'ssl' => 'SSL',
            'tls' => 'TLS',
        ];
    }

    protected function sanitize(array $data): array
    {
        $invisibles = '/[\x{200B}\x{200C}\x{200D}\x{FEFF}\x{00A0}]+/u';
        $hostStripAll = '/[\x{200B}\x{200C}\x{200D}\x{FEFF}\x{00A0}\s]+/u';

        foreach (['name', 'mailer', 'username', 'encryption', 'from_address', 'from_name'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = preg_replace($invisibles, '', trim($data[$field]));
            }
        }

        if (isset($data['host']) && is_string($data['host'])) {
            $data['host'] = preg_replace($hostStripAll, '', $data['host']);
        }

        return $data;
    }
}
