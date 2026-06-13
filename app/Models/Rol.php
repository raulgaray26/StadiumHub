<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Rol — Representa los roles de usuario del sistema.
 *
 * Tabla: roles
 * Datos esperados:
 *   rol_id=1 → Comité FIFA
 *   rol_id=2 → Jefe de Mantenimiento
 *   rol_id=3 → Técnico de Campo
 */
class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Un Rol puede tener muchos Usuarios asignados.
     * FK: usuarios.rol_id → roles.rol_id
     */
    public function usuarios(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Usuario::class, 'rol_id', 'rol_id');
    }
}