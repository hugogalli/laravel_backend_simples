<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = ['title'];

    public function atendimentos()
    {
        return $this->hasMany(Atendimento::class);
    }
}
