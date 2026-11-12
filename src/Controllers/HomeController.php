<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Softinline\SfwComponent\Models\SfwConfig;

class HomeController extends Controller
{
    /**
     * Display the home screen.
     *
     * @return mixed
     */
    public function index()
    {
        $config = null;

        try {
            $config = SfwConfig::getFirst();
        } catch (\Throwable $e) {
            $config = null;
        }

        return view('sfwcomponent::backoffice.home.index', [
            'config' => $config,
        ]);
    }
}
