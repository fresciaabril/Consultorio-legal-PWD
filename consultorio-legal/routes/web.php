<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;

Route::get('/', function () {return view('inicio');});

Route::get('/consulta-demo', function () {return view('consultas.demo');});



Route::get('/solicitudes/crear', [SolicitudController::class, 'create']);
Route::post('/solicitudes', [SolicitudController::class, 'store']);
Route::get('/solicitudes', [SolicitudController::class, 'index']);
Route::patch('/solicitudes/{solicitud}/aceptar', [SolicitudController::class, 'aceptar']);
Route::patch('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar']);