<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function __construct(private SettingsService $service) {}

    public function index(): Response
    {
        return Inertia::render('Settings/Index', [
            'calorie_deficit_mode' => Auth::user()->calorie_deficit_mode,
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $this->service->update(Auth::user(), $request->validated());

        return back()->with('success', 'Настройки сохранены.');
    }
}
