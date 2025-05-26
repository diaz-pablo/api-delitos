<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDelito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_delitos';

    protected $fillable = ['descripcion', 'created_at', 'updated_at', 'deleted_at'];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function delitos(): HasMany
    {
        return $this->hasMany(Delito::class);
    }
}
