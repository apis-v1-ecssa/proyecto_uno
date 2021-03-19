<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Principal\Client;
use App\Models\V1\Principal\Detail;
use App\Models\V1\Principal\Status;
use App\Models\V1\Principal\Deliverie;
use App\Models\V1\Seguridad\Usuario;
use App\Traits\ApiResponser;
use Maatwebsite\Excel\Concerns\ToCollection;

class DataPrueba implements ToCollection
{
    use ApiResponser;
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $value) {
            if (!is_null($value[0])) {

                DB::beginTransaction();

                if (!is_numeric($value[0]) && strlen($value[0]) < 25) {
                    $cliente = Client::firstOrCreate(
                        ['card_code' => $value[0]],
                        ['nit' => is_null($value[5]) ? 'CF' : $value[5], 'name' => is_null($value[1]) ? 'NA' : $value[1]]
                    );

                    if(is_numeric($value[2]) && strlen($value[8]) < 100 && strlen($value[3]) < 20) {
                        $venta = Deliverie::where('docto_no', $value[2])->first();

                        if (is_null($venta)) {
                            $venta = Deliverie::create(
                                [
                                    'docto_no' => $value[2],
                                    'series' => $value[3],
                                    'seller' => $value[8],
                                    'doc_date' => date('Y-m-d', strtotime($value[4])),
                                    'delivery_time' => null,
                                    'status_id' => Status::FACTURADO,
                                    'client_id' => $cliente->id,
                                    'user_id' => Usuario::all()->random()->id
                                ]
                            );

                            $this->bitacora('deliveries', $venta, $venta->user_id, $venta->id, 0, $venta->status_id, 0);
                        }

                        if (is_numeric($value[6])) {
                            $detalle = Detail::create(
                                [
                                    'item_code' => '$value[9]',
                                    'amount' => $value[6],
                                    'found' => 0,
                                    'description' => $value[7],
                                    'observation' => null,
                                    'delivery_time' => null,
                                    'status_id' => Status::FACTURADO,
                                    'deliverie_id' => $venta->id
                                ]
                            );

                            $this->bitacora('details', $detalle, $venta->user_id, $venta->id, $detalle->id, $venta->status_id, $detalle->status_id);
                        }
                    }
                }

                DB::commit();

                echo "{$value[2]} - {$value[0]}" . PHP_EOL;
            }
        }
    }
}
