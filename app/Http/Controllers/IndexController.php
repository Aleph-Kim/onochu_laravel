<?php

namespace App\Http\Controllers;

use App\Enums\ViewStatus;
use App\Models\Popup;

class IndexController extends Controller
{
    public function __invoke()
    {
        $popups = Popup::getPopup();
        return view('index', compact(
            'popups',
        ));
    }
}
