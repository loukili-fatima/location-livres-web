<?php

namespace App\Http\Controllers;

use App\Models\MlPrediction;
use Illuminate\Http\Request;

class MlPredictionController extends Controller
{
    public function index(Request $request)
    {
        $prediction = MlPrediction::where("user_id", $request->user()->id)
            ->with("category")
            ->latest()
            ->first();

        return view("ml-predictions.index", compact("prediction"));
    }
}