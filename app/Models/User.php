<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PhpParser\Node\Expr\Cast\Object_;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
  use HasFactory, Notifiable;
  // La tabla asociada al modelo
  protected $table = 'operario';
  protected $primaryKey = 'OperarioId';

  public $timestamps = false;
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'TerceroId',
    'ValorHora',
    'Estado',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'Clave',
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
    // Si la clave primaria es 'OperarioId'
    //$this->getKey()
    return (string) $this->OperarioId;
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */

  public function getJWTCustomClaims()
  {
    return [
      'operarioid'  => $this->OperarioId,
      'warehouseId' => $this->AlmacenId,
      'document'    => $this->TerceroId,
      'name'        => $this->thirdData->nombre,

      // Agregar aquí otros datos que se quieran incluir en el token
    ];
  }

  /**
   * Sobrescribir el método getAuthIdentifierName para usar 'operarioid'
   */
  public function getAuthIdentifierName()
  {
    return 'operarioid';
  }

  public function getAuthPassword()
  {
    return $this->Clave;
  }

  public function validateForPassportPasswordGrant($password)
  {

    return strtoupper(md5($password)) === $this->Clave;
  }

  public function thirdData(): HasOne
  {
    return $this->HasOne(Tercero::class, 'TerceroID', 'TerceroId');
  }

  public function toArray()
  {
    $array = parent::toArray();
    $dataThird = $this->thirdData;

    $serializeData = [
      'id'              => $array['OperarioId'],
      'thirdId'         => $array['TerceroId'],
      'state'           => $array['Estado'],
      'type'            => $array['Tipo'],
      'warehouseId'     => $array['AlmacenId'],
      'name'            => $dataThird->nombre,
      'photo'           => $dataThird->foto,
      'typeDocument'    => $dataThird->tipodocuid,
      'cityId'          => $dataThird->ciudadid,
      'phone'           => $dataThird->telefono,
      'cellPhone'       => $dataThird->celular,
      'email'       => $dataThird->email,
    ];

    return $serializeData;
  }


}
