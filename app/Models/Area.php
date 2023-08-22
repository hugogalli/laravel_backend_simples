<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $fillable = ['title'];

    public function atendimentos()
    {
        return $this->hasMany(Atendimento::class);
    }

    public function analistas()
    {
        return $this->belongsToMany(User::class, 'analista_areas', 'area_id', 'analista_id');
    }
}
