<?php

namespace App\Controllers;

use App\Controllers\Core\DataController;

class Home extends DataController
{
    public function index(): string
    {
        return view('welcome_message');
    }
}
