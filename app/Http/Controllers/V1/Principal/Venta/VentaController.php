<?php

namespace App\Http\Controllers\V1\Principal\Venta;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Principal\Detail;
use App\Models\V1\Principal\Status;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Models\V1\Principal\Deliverie;
use App\Http\Controllers\ApiController;
use App\Models\V1\Principal\Client;
use Illuminate\Support\Facades\Storage;

class VentaController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function aceptado()
    {
        $data = Deliverie::with('status', 'client', 'detail', 'user')->where('status_id', Status::ACEPTADO)->where('user_id', Auth::user()->id)->orderBy('doc_date')->get();
        return $this->successResponse($data);
    }

    public function facturado()
    {
        $data = Deliverie::with('status', 'client', 'detail.status', 'user')->whereIn('status_id', [Status::FACTURADO, Status::EN_PROCESO, Status::COMPLETO])->where('user_id', Auth::user()->id)->orderBy('doc_date')->get();
        return $this->successResponse($data);
    }

    public function completo()
    {
        $data = Deliverie::with('status', 'client', 'detail', 'user')->where('status_id', Status::COMPLETO)->where('user_id', Auth::user()->id)->orderBy('doc_date')->get();
        return $this->successResponse($data);
    }

    public function anulado()
    {
        $data = Deliverie::with('status', 'client', 'detail', 'user')->where('status_id', Status::CANCELADO)->where('user_id', Auth::user()->id)->orderBy('doc_date')->get();
        return $this->successResponse($data);
    }

    public function completar_todo(Request $request, Deliverie $deliverie)
    {
        try {
            DB::beginTransaction();

            $deliverie->delivery_time = Carbon::now();
            $deliverie->description = (isset($request->description) && !is_null($request->description) && !empty($request->description)) ? $request->description : $deliverie->description;
            $deliverie->status_id = Status::COMPLETO;

            if ($deliverie->isDirty()) {
                $this->bitacora('deliveries', $deliverie, Auth::user()->id, $deliverie->id, 0, $deliverie->status_id, 0);
                $deliverie->save();
            }

            $detalle = Detail::where('deliverie_id', $deliverie->id)->get();

            foreach ($detalle as $value) {
                $value->found = $value->amount;
                $value->delivery_time = Carbon::now();
                $value->status_id = Status::COMPLETO;

                if ($value->isDirty()) {
                    $this->bitacora('details', $detalle, Auth::user()->id, $deliverie->id, $value->id, $deliverie->status_id, $value->status_id);
                    $value->save();
                }
            }

            DB::commit();

            return $this->successResponse('Cambio aplicado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 423);
        }
    }

    public function cancelar(Deliverie $deliverie)
    {
        try {
            DB::beginTransaction();

            $deliverie->status_id = Status::CANCELADO;

            if ($deliverie->isDirty()) {
                $deliverie->delivery_time = Carbon::now();
                $this->bitacora('deliveries', $deliverie, Auth::user()->id, $deliverie->id, 0, $deliverie->status_id, 0);
                $deliverie->save();
            }

            $detalle = Detail::where('deliverie_id', $deliverie->id)->get();

            foreach ($detalle as $value) {
                $value->found = 0;
                $value->delivery_time = Carbon::now();
                $value->status_id = Status::CANCELADO;

                if ($value->isDirty()) {
                    $this->bitacora('details', $value, Auth::user()->id, $deliverie->id, $value->id, $deliverie->status_id, $value->status_id);
                    $value->save();
                }
            }

            DB::commit();

            return $this->successResponse('Cambio aplicado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador', 423);
        }
    }

    public function entregar(Request $request, Deliverie $deliverie)
    {
        try {
            DB::beginTransaction();

            foreach ($request->detail as $value) {
                $detalle = Detail::find($value['id']);
                $detalle->found = $value['found'];
                $detalle->delivery_time = $value['found'] == $detalle->amount ? Carbon::now() : null;
                $detalle->status_id = $value['found'] == $detalle->amount ? Status::COMPLETO : Status::EN_PROCESO;
                $detalle->observation = $value['observation'];

                if ($detalle->isDirty()) {
                    $this->bitacora('details', $detalle, Auth::user()->id, $deliverie->id, $detalle->id, $deliverie->status_id, $detalle->status_id);
                    $detalle->save();
                }
            }

            $buscar = Detail::where('deliverie_id', $deliverie->id)->where('status_id', '!=', Status::COMPLETO)->count();

            $deliverie->status_id = $buscar != 0 ? Status::EN_PROCESO : Status::COMPLETO;

            if ($deliverie->isDirty()) {
                $deliverie->delivery_time = Carbon::now();
                $this->bitacora('deliveries', $deliverie, Auth::user()->id, $deliverie->id, 0, $deliverie->status_id, 0);
                $deliverie->save();
            }

            DB::commit();

            return $this->successResponse('Cambio aplicado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador', 423);
        }
    }

    public function aceptar(Request $request, Deliverie $deliverie)
    {
        try {
            DB::beginTransaction();

            if (!is_null($request->firma)) {
                $deliverie->status_id = Status::ACEPTADO;
                $cliente = Client::find($deliverie->client_id)->card_code;

                $img = $this->getB64Image($request->firma);
                $image = Image::make($img);
                $image->fit(870, 620, function ($constraint) {
                    $constraint->upsize();
                });
                $image->encode('png', 70);
                $path = "{$deliverie->docto_no}/{$cliente}.png";
                Storage::disk('firma')->put($path, $image);
                $deliverie->firma = $path;
            }

            if ($deliverie->isDirty()) {
                $this->bitacora('deliveries', $deliverie, Auth::user()->id, $deliverie->id, 0, $deliverie->status_id, 0);
                $deliverie->save();
            }

            DB::commit();

            return $this->successResponse('Cambio aplicado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador', 423);
        }
    }
}
