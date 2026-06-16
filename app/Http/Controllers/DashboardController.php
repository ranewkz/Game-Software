<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin_view.dashboard', [
            // Pie chart data
            'gameModes' => [
                'Solo' => 120,
                'Duo' => 90,
                'Squad' => 60,
                'Custom' => 30,
            ],

            // Line chart data
            'weeklyActive' => [50, 70, 65, 90, 120, 160, 140],

            // Table data (example: recent matches / logs)
            'recentMatches' => [
                ['player' => 'Rexx', 'mode' => 'Solo', 'result' => 'Win', 'score' => 1800],
                ['player' => 'Alex', 'mode' => 'Duo', 'result' => 'Lose', 'score' => 900],
                ['player' => 'Mina', 'mode' => 'Squad', 'result' => 'Win', 'score' => 2100],
            ]
        ]);
    }
}