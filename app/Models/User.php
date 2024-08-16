<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    // La tabla asociada al modelo
    protected $table = 'segur';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'usuarioid',
        'clave',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        // Si la clave primaria es 'usuarioid'
        return (string) $this->usuarioid;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */

    public function getJWTCustomClaims()
    {
        return [
            'usuarioid' => $this->usuarioId,
            'name' => $this->nombre,
            'document' => $this->cedula,
            // Agrega aquí otros datos que quieras incluir en el token
        ];
    }

    /**
     * Sobrescribir el método getAuthIdentifierName para usar 'usuarioid'
     */
    public function getAuthIdentifierName()
    {
        return 'usuarioid';
    }

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public function validateForPassportPasswordGrant($password){

        return strtoupper(md5($password)) === $this->clave;
    }

    
}
