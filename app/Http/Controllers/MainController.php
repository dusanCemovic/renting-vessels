<?php

namespace App\Http\Controllers;

class MainController
{
    public function index()
    {
        return to_route('reservations.index');
    }
}
