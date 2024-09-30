<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadMovi extends Model
{
    use HasFactory;
    protected $table = 'HeadMovi';
    protected $primaryKey = 'movimientoid';
    public $timestamps = false;

    protected $fillable = ['numero', 'documentoid', 'fecha','almacenid','usuarioid','consemovim','fechadigit'];
}
