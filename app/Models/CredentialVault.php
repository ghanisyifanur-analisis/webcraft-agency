<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CredentialVault extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'service_label', // cPanel, Domain Registrar, WP-Admin, Database
        'login_url',
        'username',
        'encrypted_password',
        'is_unlocked',
    ];

    protected $casts = [
        'is_unlocked' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Set encrypted password using Laravel Crypt (AES-256-CBC)
     */
    public function setPlainPasswordAttribute(string $value): void
    {
        $this->attributes['encrypted_password'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt password securely when unlocked
     */
    public function getDecryptedPasswordAttribute(): ?string
    {
        try {
            return Crypt::decryptString($this->encrypted_password);
        } catch (\Exception $e) {
            return null;
        }
    }
}
