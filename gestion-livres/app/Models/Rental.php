<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'date_emprunt',
        'date_retour_prevue',
        'date_retour_reelle',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calculerPenalite($tarifParJour = 0.5)
    {
        $dateReference = $this->date_retour_reelle
            ? Carbon::parse($this->date_retour_reelle)
            : Carbon::now();

        $dateRetourPrevue = Carbon::parse($this->date_retour_prevue);

        if ($dateReference->lessThanOrEqualTo($dateRetourPrevue)) {
            return 0;
        }

        $joursDeRetard = $dateRetourPrevue->diffInDays($dateReference);

        return round($joursDeRetard * $tarifParJour, 2);
    }
}
