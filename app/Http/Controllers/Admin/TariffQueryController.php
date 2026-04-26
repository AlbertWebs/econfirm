<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffQuery;
use Illuminate\Contracts\View\View;

class TariffQueryController extends Controller
{
    public function index(): View
    {
        $queries = TariffQuery::query()
            ->orderByDesc('id')
            ->paginate(40);

        return view('admin.tariff-queries.index', compact('queries'));
    }
}
