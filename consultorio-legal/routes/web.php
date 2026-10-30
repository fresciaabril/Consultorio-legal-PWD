<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/consulta-demo', function () {
    return view('consultas.demo');
});