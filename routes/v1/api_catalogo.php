<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//rutas para DepartamentoController
Route::resource('departamento', 'Departamento\DepartamentoController')->only('index');

//rutas para MunicipioController
Route::resource('municipio', 'Municipio\MunicipioController')->only('index');

