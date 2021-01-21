<?php

namespace App\Http\Controllers\WEB\V1;

use App\Http\Controllers\Controller;
use App\Model\WEB\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/ecssa/productos",
     *      operationId="getProductos",
     *      tags={"Productos"},
     * security={
     *  {"passport": {}},
     *   },
     *      summary="Muestra todos los productos existentes",
     *      description="Retorna todos los registros de productos almacenados en la Base de Datos",
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
            $productos = Producto::all();
            return response()->json(['data' => $productos], 200);
        } catch (\Exception $th) {
            return response()->json(['data' => "Error en el controlador"], 500);
        }
    }
}
