<?php

namespace App\Http\Controllers\V1\Seguridad\Usuario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Seguridad\Usuario;
use App\Http\Controllers\ApiController;
use App\Models\V1\Seguridad\RolMenu;
use App\Models\V1\Seguridad\UsuarioRol;
use Illuminate\Database\QueryException;

class UsuarioRolController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @OA\Post(
     *      path="/service/rest/v1/security/user_rol",
     *      operationId="postUserRol",
     *      tags={"Usuario y Rol"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Asignar rol al usuario.",
     *      description="Retorna el objeto del rol asignado.",
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
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules(), $this->messages());
        try {
            DB::beginTransaction();

            UsuarioRol::where('user_id', $request->id)->forceDelete();

            foreach ($request->roles as $value) {
                $insert = new UsuarioRol();
                $insert->user_id = $request->id;
                $insert->rol_id = $value['id'];
                $insert->save();
            }

            DB::commit();

            return $this->successResponse('Registro agregado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 423);
        }
    }

    /**
     * @OA\Get(
     *      path="/service/rest/v1/security/user_rol/{user_rol} ",
     *      operationId="findUserRolbyId",
     *      tags={"Usuario y Rol"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Muestra todos los roles asignados al usuario seleccionado.",
     *      description="Retorna un array de roles del usuario seleccionado.",
     *      @OA\Parameter(
     *          description="ID del usuario para buscar los roles",
     *          in="path",
     *          name="user_rol",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function show(Usuario $user_rol)
    {
        $rols = UsuarioRol::select('rol_id')->where('user_id', $user_rol->id)->distinct('rol_id')->pluck('rol_id');
        $menus = RolMenu::select('menu_id')->whereIn('rol_id', $rols)->distinct('menu_id')->with('menu')->get();
        return $this->successResponse($menus);
    }

    /**
     * @OA\Delete(
     *      path="/service/rest/v1/security/user_rol/{user_rol} ",
     *      operationId="deleteUser",
     *      tags={"Usuario y Rol"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Eliminar el rol asignado al usuario seleccionado.",
     *      description="Retorna el objeto del rol asignado al usuario eliminado.",
     *      @OA\Parameter(
     *          description="ID del rol para eliminar",
     *          in="path",
     *          name="user_rol",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function destroy(UsuarioRol $user_rol)
    {
        try {
            $user_rol->delete();
            return $this->successResponse('Registro desactivado');
        } catch (\Exception $e) {
            if ($e instanceof QueryException) {
                return $this->errorResponse('El registro se encuentra en uso', 423);
            }
        }
    }

    //Reglas de validaciones
    public function rules($id = null)
    {
        $validar = [
            'roles.*.id' => 'required|integer|exists:rols,id',
            'id' => 'required|integer|exists:users,id'
        ];

        return $validar;
    }

    //Mensajes para las reglas de validaciones
    public function messages($id = null)
    {
        return [
            'roles.*.id.required' => 'El rol es obligatorio',
            'roles.*.id.integer' => 'El rol no es un número',
            'roles.*.id.exists' => 'El rol no existe en la base de datos',

            'id.required' => 'El usuario es obligatorio',
            'id.integer' => 'El usuario no es un número',
            'id.exists' => 'El usuario no existe en la base de datos'
        ];
    }
}
