<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TariffController extends Controller
{
    public function index(): View
    {
        return view('front.tariffs', [
            'tariffs' => config('tariffs', []),
        ]);
    }

    public function redirectTypo(): RedirectResponse
    {
        return redirect()->route('tariffs.index', [], 301);
    }
}
