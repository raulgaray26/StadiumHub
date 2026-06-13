<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo HistorialTarea — Log de auditoría del sistema.
 *
 * Tabla: historial_tareas
 * Registra CADA acción relevante: creación, asignación y completado de tareas.
 * Este historial es la fuente de datos del dashboard del Comité FIFA.
 *
 * Columna 'accion': 'Creada' | 'Asignada' | 'Completada' | 'Editada'
 */
class HistorialTarea extends Model
{
    protected $table = 'historial_tareas';
    protected $primaryKey = 'historial_id';
    public $timestamps = false; // Usamos columna 'timestamp' manual

    protected $fillable = [
        'tarea_id',
        'user_id',
        'creador_id',
        'accion',
        'estado_anterior',
        'estado_nuevo',
        'detalle',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────

    /**
     * Este registro de historial pertenece a una Tarea específica.
     */
    public function tarea(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tarea::class, 'tarea_id', 'tarea_id');
    }

    /**
     * El Usuario que EJECUTÓ la acción registrada (ej: el técnico que completó).
     * FK: historial_tareas.user_id → usuarios.user_id
     */
    public function usuario(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id', 'user_id');
    }

    /**
     * El Usuario que CREÓ o INICIÓ la acción (ej: el jefe que asignó).
     * FK: historial_tareas.creador_id → usuarios.user_id
     */
    public function creador(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creador_id', 'user_id');
    }
}