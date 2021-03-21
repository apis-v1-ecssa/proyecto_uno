<?php

namespace App\Http\Controllers\V1\Principal\Bitacora;

use App\Http\Controllers\ApiController;
use App\Models\V1\Principal\Deliverie;
use Illuminate\Support\Facades\DB;

class BitacoraController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = DB::connection('sqlsrv')->table('deliveries')
            ->join('status', 'deliveries.status_id', 'status.id')
            ->join('clients', 'deliveries.client_id', 'clients.id')
            ->join('users', 'deliveries.user_id', 'users.id')
            ->select(
                'deliveries.id',
                'deliveries.docto_no',
                'deliveries.series',
                'deliveries.seller',
                DB::RAW("FORMAT(deliveries.doc_date, 'dd-MM-yyyy') as date"),
                'status.name',
                'status.color',
                'clients.name AS client',
                DB::RAW("CONCAT(users.name,' ',users.surname) as usuario")
            )
            ->orderByDesc('deliveries.doc_date')
            ->get();

        return $this->successResponse($data);
    }

    public function venta(Deliverie $deliverie)
    {
        $data = DB::connection('sqlsrv')->table('bitacora')
            ->join('status', 'bitacora.deliverie_status_id', 'status.id')
            ->join('users', 'bitacora.user_id', 'users.id')
            ->select(
                DB::RAW("FORMAT(bitacora.created_at, 'dd-MM-yyyy HH:mm:ss') as date"),
                'status.name',
                'status.color',
                DB::RAW("CONCAT(users.name,' ',users.surname) as usuario")
            )
            ->where('bitacora.deliverie_id', $deliverie->id)
            ->distinct('bitacora.created_at')
            ->orderByDesc('date')
            ->get();

        return $this->successResponse($data);
    }
}
