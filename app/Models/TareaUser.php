<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modelo TareaUser — Tabla pivote entre Tarea y Usuario.
 *
 * Tabla: tarea_user
 * Extiende Pivot (no Model) para que Laravel lo reconozca como pivote.
 * Tiene su propia PK (tarea_user_id) y columnas extra de la asignación.
 *
 * Columna 'estado_asignacion': Pendiente | En Progreso | Completada
 */
class TareaUser extends Pivot
{
    protected $table = 'tarea_user';
    protected $primaryKey = 'tarea_user_id';
    public $timestamps = false; // Las fechas se manejan manualmente
    public $incrementing = true;

    protected $fillable = [
        'tarea_id',
        'user_id',
        'estado_asignacion',
        'fecha_asignacion',
        'fecha_completado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_completado' => 'date',
    ];
}