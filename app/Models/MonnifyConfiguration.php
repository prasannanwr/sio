<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class MonnifyConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_key',
        'secret_key',
        'contract_code',
        'base_url',
        'currency',
        'webhook_secret',
        'redirect_url',
        'source_account_number',
        'is_active',
    ];

    protected $casts = [
        'secret_key' => 'encrypted',
        'webhook_secret' => 'encrypted',
        'is_active' => 'boolean',
    ];

    public static function active(): ?self
    {
        return static::where('is_active', true)->latest('updated_at')->first();
    }

    /**
     * Override the runtime Monnify config with this stored configuration.
     */
    public function applyToConfig(): void
    {
        Config::set('monnify.api_key', $this->api_key);
        Config::set('monnify.secret_key', $this->secret_key);
        Config::set('monnify.contract_code', $this->contract_code);
        Config::set('monnify.base_url', rtrim((string) $this->base_url, '/') ?: 'https://sandbox.monnify.com');
        Config::set('monnify.currency', $this->currency ?: 'NGN');
        Config::set('monnify.webhook_secret', $this->webhook_secret ?: $this->secret_key);

        if ($this->redirect_url) {
            Config::set('monnify.redirect_url', $this->redirect_url);
        }

        if ($this->source_account_number) {
            Config::set('monnify.disbursement.source_account_number', $this->source_account_number);
        }
    }

    public static function applyActiveIfAny(): ?self
    {
        $active = static::active();

        if ($active) {
            $active->applyToConfig();
        }

        return $active;
    }
}
