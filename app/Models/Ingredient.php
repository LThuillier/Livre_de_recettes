<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Hasfactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
        use HasFactory;

    protected $fillable = ['nom', 'unite_mesure'];

    public function recettes()
    {
        return $this->belongsToMany(Recette::class)
                    ->withPivot('quantite', 'unite');
    }
}
