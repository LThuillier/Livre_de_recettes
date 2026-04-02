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
        return $this->belongsToMany(Recette::class, 'ingredient_recette')
                    ->withPivot('quantite', 'unite');
    }

    public function estLiquide()
    {
        $motsLiquides = ['lait', 'eau', 'huile', 'vin', 'jus', 'crème', 'sirop', 'cl', 'ml'];
        foreach ($motsLiquides as $mot) {
            if (str_contains(strtolower($this->nom), $mot)) return true;
        }
        return false;
    }
}
