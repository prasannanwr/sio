<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class SmtpConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'port' => 'integer',
        'is_active' => 'boolean',
        'password' => 'encrypted',
    ];

    protected $hidden = ['password'];

    public static function active(): ?self
    {
        return static::where('is_active', true)->latest('updated_at')->first();
    }

    public function applyToMailConfig(): void
    {
        $encryption = $this->encryption ?: null;

        Config::set('mail.default', $this->mailer ?: 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $this->host);
        Config::set('mail.mailers.smtp.port', $this->port);
        Config::set('mail.mailers.smtp.username', $this->username);
        Config::set('mail.mailers.smtp.password', $this->password);
        Config::set('mail.mailers.smtp.encryption', $encryption);
        Config::set('mail.from.address', $this->from_address);
        Config::set('mail.from.name', $this->from_name);
    }

    public static function applyActiveIfAny(): ?self
    {
        $active = static::active();
        if ($active) {
            $active->applyToMailConfig();
        }
        return $active;
    }
}
