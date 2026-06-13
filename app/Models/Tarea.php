<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Tarea — Representa una tarea de mantenimiento de césped.
 *
 * Tabla: tareas
 * La columna 'metadata' almacena JSON con información adicional variable.
 * La columna 'timestamp' registra la fecha/hora de creación.
 */
class Tarea extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'tarea_id';
    public $timestamps = false; // Usamos columna 'timestamp' manual

    protected $fillable = [
        'tipo_tarea_id',
        'estadio_id',
        'creador_id',
        'titulo',
        'descripcion',
        'estado',
        'fecha_limite',
        'metadata',
        'timestamp',
    ];

    /** Convierte automáticamente la columna 'metadata' a/desde array PHP */
    protected $casts = [
        'metadata'     => 'array',
        'fecha_limite' => 'date',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────

    /**
     * Una Tarea pertenece a un TipoTarea (ej: Riego, Corte, Fertilización).
     * FK: tareas.tipo_tarea_id → tipo_tarea.tipo_tarea_id
     */
    public function tipoTarea(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TipoTarea::class, 'tipo_tarea_id', 'tipo_tarea_id');
    }

    /**
     * Una Tarea está asociada a un Estadio específico.
     * FK: tareas.estadio_id → estadios.estadio_id
     */
    public function estadio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Estadio::class, 'estadio_id', 'estadio_id');
    }

    /**
     * Una Tarea fue creada por un Usuario (Jefe de Mantenimiento).
     * FK: tareas.creador_id → usuarios.user_id
     */
    public function creador(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creador_id', 'user_id');
    }

    /**
     * Una Tarea puede estar asignada a muchos Usuarios (Técnicos).
     * Relación muchos-a-muchos a través de 'tarea_user'.
     */
    public function usuariosAsignados(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'tarea_user',
            'tarea_id',
            'user_id'
        )
        ->using(TareaUser::class)
        ->withPivot([
            'tarea_user_id',
            'estado_asignacion',
            'fecha_asignacion',
            'fecha_completado',
            'observaciones',
        ]);
    }

    /**
     * Una Tarea tiene muchas entradas en el historial de auditoría.
     * FK: historial_tareas.tarea_id → tareas.tarea_id
     */
    public function historial(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistorialTarea::class, 'tarea_id', 'tarea_id');
    }
}