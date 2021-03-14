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

//rutas para VentaController
Route::name('venta.facturado')->get('facturado', 'Venta\VentaController@facturado');
Route::name('venta.completo')->get('completo', 'Venta\VentaController@completo');
Route::name('venta.anulado')->get('anulado', 'Venta\VentaController@anulado');
Route::name('venta.completar_todo')->get('completar_todo/{deliverie}', 'Venta\VentaController@completar_todo');
Route::name('venta.cancelar')->get('cancelar/{deliverie}', 'Venta\VentaController@cancelar');
Route::name('venta.entregar')->put('entregar/{deliverie}', 'Venta\VentaController@entregar');