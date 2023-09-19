<?php

    use Illuminate\Support\Facades\Route;

    // initial bootstrap
    Route::any('/sfw/{object?}/{action?}', 'Softinline\SfwComponent\Controllers\BootstrapController@index');