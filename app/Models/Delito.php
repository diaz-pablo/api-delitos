<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'delitos';

    protected $fillable = ['user_id', 'tipo_delito_id', 'fecha_ocurrencia', 'latitud', 'longitud', 'created_at', 'updated_at', 'deleted_at'];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function tipo_delito(): BelongsTo
    {
        return $this->belongsTo(TipoDelito::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
