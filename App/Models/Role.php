<?php

namespace App\Models;

use Core\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'fecha_creacion'
    ];
    protected $hidden = [];
    protected $timestamps = false;
    protected $softDeletes = false;

    // Relación: usuarios que tienen este rol
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id', 'id');
    }

}
