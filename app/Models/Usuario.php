<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo Usuario — Extiende Authenticatable para usar el sistema de auth de Laravel.
 *
 * Tabla: usuarios
 * Relaciones:
 *   - Pertenece a un Rol (belongsTo)
 *   - Pertenece a un Estadio (belongsTo)
 *   - Tiene muchas Tareas asignadas (belongsToMany vía tarea_user)
 *   - Ha creado muchas Tareas (hasMany)
 *   - Tiene muchos registros en HistorialTareas (hasMany)
 */
class Usuario extends Authenticatable
{
    use Notifiable;

    // ─── Configuración de la tabla ────────────────────────────────────────
    /** Nombre de la tabla en MySQL (no sigue la convención plural de Laravel) */
    protected $table = 'usuarios';

    /** Clave primaria personalizada (Laravel asume 'id' por defecto) */
    protected $primaryKey = 'user_id';

    /**
     * Deshabilitamos los timestamps automáticos de Laravel (created_at / updated_at)
     * porque la tabla usa la columna 'timestamp' con un esquema diferente.
     */
    public $timestamps = false;

    // ─── Asignación masiva ────────────────────────────────────────────────
    /** Campos que se pueden llenar con create() o fill() */
    protected $fillable = [
        'rol_id',
        'estadio_id',
        'nombre',
        'email',
        'password',
    ];

    /** Campos que NUNCA se exponen en JSON/array (seguridad) */
    protected $hidden = [
        'password',
    ];

    /** Castings automáticos de tipos de datos */
    protected $casts = [
        'password' => 'hashed', // Laravel 11: hashea automáticamente al asignar
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────

    /**
     * Un Usuario pertenece a un Rol (ej: Técnico, Jefe, Comité).
     * FK: usuarios.rol_id → roles.rol_id
     */
    public function rol(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    /**
     * Un Usuario pertenece a un Estadio (lugar donde trabaja).
     * FK: usuarios.estadio_id → estadios.estadio_id
     */
    public function estadio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Estadio::class, 'estadio_id', 'estadio_id');
    }

    /**
     * Tareas asignadas al usuario — Relación muchos a muchos
     * a través de la tabla pivote 'tarea_user'.
     * withPivot() incluye las columnas extra de la tabla pivote.
     */
    public function tareasAsignadas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Tarea::class,       // Modelo relacionado
            'tarea_user',       // Tabla pivote
            'user_id',          // FK del modelo actual en la pivote
            'tarea_id'          // FK del modelo relacionado en la pivote
        )
        ->using(TareaUser::class)   // Usar modelo Pivot personalizado
        ->withPivot([               // Columnas extra de la pivote accesibles con ->pivot->
            'tarea_user_id',
            'estado_asignacion',
            'fecha_asignacion',
            'fecha_completado',
            'observaciones',
        ]);
    }

    /**
     * Tareas que este usuario ha CREADO como Jefe de Mantenimiento.
     * FK: tareas.creador_id → usuarios.user_id
     */
    public function tareasCreadas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tarea::class, 'creador_id', 'user_id');
    }

    /**
     * Entradas en el historial donde este usuario realizó una acción.
     * FK: historial_tareas.user_id → usuarios.user_id
     */
    public function historial(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistorialTarea::class, 'user_id', 'user_id');
    }

    // ─── Helpers de Rol ───────────────────────────────────────────────────

    /** Devuelve true si el usuario es del Comité FIFA (rol_id = 1) */
    public function esComite(): bool
    {
        return (int) $this->rol_id === 1;
    }

    /** Devuelve true si el usuario es Jefe de Mantenimiento (rol_id = 2) */
    public function esJefe(): bool
    {
        return (int) $this->rol_id === 2;
    }

    /** Devuelve true si el usuario es Técnico de Campo (rol_id = 3) */
    public function esTecnico(): bool
    {
        return (int) $this->rol_id === 3;
    }
}