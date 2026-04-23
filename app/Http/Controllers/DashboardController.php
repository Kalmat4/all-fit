<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Dashboard/Index', [
            'todaySession' => null,        // WorkoutSession или null
            'recentPrograms' => [],        // последние программы пользователя
        ]);
    }
}
