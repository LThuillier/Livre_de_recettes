<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recette extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'temps_preparation',
        'difficulte',
        'regime_alimentaire',
        'user_id',
        'portions',        // Nouveau champ pour les portions
        'est_public',   // Corrigé : tiret → underscore
    ];

    public function getTempsFormatteAttribute()
    {
        $minutes = $this->temps_preparation;
        
        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $heures = floor($minutes / 60);
        $resteMin = $minutes % 60;

        if ($resteMin === 0) {
            return $heures . 'h';
        }

        return $heures . 'h ' . sprintf('%02d', $resteMin) . 'min';
    }

    // Relation avec les ingrédients
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)
                    ->withPivot('quantite', 'unite');
    }

    // Relation avec les catégories
    public function categories()
    {
        return $this->belongsToMany(Categorie::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}