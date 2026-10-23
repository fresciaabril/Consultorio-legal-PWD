<?php

use Illuminate\Support\Facades\Route;

Route::get('/consulta-demo', function () {
    return view('consultas.demo');
});