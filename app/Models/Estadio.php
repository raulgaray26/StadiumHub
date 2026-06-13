<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Estadio — Representa los estadios sede de la FIFA 2026.
 *
 * Tabla: estadios
 */
class Estadio extends Model
{
    protected $table = 'estadios';
    protected $primaryKey = 'estadio_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'pais',
        'ciudad',
        'capacidad',
        'dimensiones',
    ];

    /**
     * Un Estadio tiene muchos Usuarios asignados (su personal).
     * FK: usuarios.estadio_id → estadios.estadio_id
     */
    public function usuarios(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Usuario::class, 'estadio_id', 'estadio_id');
    }

    /**
     * Un Estadio tiene muchas Tareas de mantenimiento asociadas.
     * FK: tareas.estadio_id → estadios.estadio_id
     */
    public function tareas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tarea::class, 'estadio_id', 'estadio_id');
    }
}