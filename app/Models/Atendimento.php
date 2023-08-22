<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'tipo', 'pessoa', 'user_id', 'cliente_id', 'area_id', 'analista_id', 'status', 'info_adicional'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function analista()
    {
        return $this->belongsTo(User::class);
    }
}
