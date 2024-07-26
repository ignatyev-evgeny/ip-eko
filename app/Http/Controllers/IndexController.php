<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Log;

class IndexController extends Controller {

    public function index(Request $request) {

        try {

            return view('index');

        } catch (Exception $exception) {
            report($exception);
            abort(500, $exception->getMessage());
        }
    }
}
