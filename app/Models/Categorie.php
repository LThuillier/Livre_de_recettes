<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'type']; // type: entree, plat, dessert

    public function recettes()
    {
        return $this->belongsToMany(Recette::class);
    }
}
