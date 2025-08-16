<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Inscription extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prenom',
        'nom',
        'numero_telephone',
        'mail', // This will be used as email for authentication
        'username',
        'mot_de_passe', // This needs to be mapped to 'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe', // Map to password
        'remember_token',
    ];

    /**
     * Get the attribute that should be used for authentication.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        // We can authenticate by email or username
        return 'username';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * Get the e-mail address of the user.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->mail;
    }

    /**
     * Get the username of the user.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
