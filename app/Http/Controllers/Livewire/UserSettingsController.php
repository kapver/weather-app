<?php

namespace App\Http\Controllers\Livewire;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserSettingsController extends Controller
{
    /**
     * Show the user settings screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        return view('settings.show', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
