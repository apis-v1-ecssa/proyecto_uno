<?php

namespace App\Http\Controllers\V1\Principal\Venta;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Principal\Detail;
use App\Models\V1\Principal\Status;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Principal\Deliverie;
use App\Http\Controllers\ApiController;

class VentaController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function facturado() 
    {
        $data = Deliverie::with('status', 'client', 'detail.status')->whereIn('status_id', [Status::FACTURADO, Status::EN_PROCESO])->orderByDesc('id')->get();
        return $this->successResponse($data);
    }

    public function completo()
    {
        $data = Deliverie::with('status', 'client', 'detail.status')->where('status_id', Status::COMPLETO)->orderByDesc('id')->get();
        return $this->successResponse($data);
    }

    public function anulado()
    {
        $data = Deliverie::with('status', 'client', 'detail.status')->where('status_id', Status::CANCELADO)->orderByDesc('id')->get();
        return $this->successResponse($data);
    }

    public function completar_todo(Deliverie $deliverie)
    {
        try {
            DB::beginTransaction();

                $detalle = Detail::where('deliverie_id', $deliverie->id)->get();

                foreach ($detalle as $value) {
                    $value->found = $value->amount;
                    $value->delivery_time = Carbon::now();
                    $value->status_id = Status::COMPLETO;
                    $value->user_id = Auth::user()->id;
                    $value->save();
                }

                $deliverie->delivery_time = Carbon::now();
                $deliverie->status_id = Status::COMPLETO;
                $deliverie->user_id = Auth::user()->id;
                $deliverie->save();

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
            $deliverie->delivery_time = Carbon::now();
            $deliverie->user_id = Auth::user()->id;
            $deliverie->save();

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

            $deliverie->delivery_time = Carbon::now();
            $deliverie->user_id = Auth::user()->id;

            foreach ($request->detail as $value) {
                $detalle = Detail::find($value['id']);
                $detalle->found = $value['found'];
                $detalle->delivery_time = $value['found'] == $detalle->amount ? Carbon::now() : null;
                $detalle->status_id = $value['found'] == $detalle->amount ? Status::COMPLETO : Status::ENTREGADO;
                $detalle->observation = $value['observation'];
                $detalle->user_id = Auth::user()->id;
                $detalle->save();
            }

            $buscar = Detail::where('deliverie_id', $deliverie->id)->where('status_id', '!=', Status::COMPLETO)->count();

            $deliverie->status_id = $buscar != 0 ? Status::EN_PROCESO : Status::COMPLETO;
            $deliverie->save();

            DB::commit();

            return $this->successResponse('Cambio aplicado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error en el controlador', 423);
        }
    }
}
