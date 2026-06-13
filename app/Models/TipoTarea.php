<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo TipoTarea — Categorías de tareas de mantenimiento.
 * Ej: Riego Calibrado, Podado, Fertilización, Inspección de Sensores.
 *
 * Tabla: tipo_tarea
 * La columna 'roles_permitidos' define qué roles pueden ejecutar este tipo.
 */
class TipoTarea extends Model
{
    protected $table = 'tipo_tarea';
    protected $primaryKey = 'tipo_tarea_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'roles_permitidos',
    ];

    /**
     * Un TipoTarea tiene muchas Tareas que lo utilizan.
     * FK: tareas.tipo_tarea_id → tipo_tarea.tipo_tarea_id
     */
    public function tareas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tarea::class, 'tipo_tarea_id', 'tipo_tarea_id');
    }
}