<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller {
    public function list() {
        return view('client.index', [
            'clients' => Client::all()
        ]);
    }
}
