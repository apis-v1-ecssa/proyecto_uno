<?php

namespace App\Http\Controllers\WEB\V1;

use App\Http\Controllers\Controller;
use App\Model\WEB\Precio;
use Illuminate\Http\Request;

class PrecioController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/ecssa/precios",
     *      operationId="getPrecios",
     *      tags={"Precios"},
     * security={
     *  {"passport": {}},
     *   },
     *      summary="Muestra todos los precios de los diferentes productos",
     *      description="Retorna todos lo registros de precios almacenados en la Base de Datos",
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Solicitud incorrecta"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="Servicio no encontrado"
     *   ),
     *  )
     */

    public function index()
    {
        try {
            $precios = Precio::all();
            return response()->json(['data' => $precios], 200);
        } catch (\Exception $th) {
            return response()->json(['data' => "Error en el controlador"], 500);
        }
    }
}
