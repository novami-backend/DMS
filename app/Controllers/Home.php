<?php

namespace App\Controllers;

class Home extends BaseController
{

    public function index()
    {
        return redirect()->to('/login');
    }

    // All authentication handled by Auth controller now
}
