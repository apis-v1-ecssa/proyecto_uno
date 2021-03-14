<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Principal\Client;
use App\Models\V1\Principal\Detail;
use App\Models\V1\Principal\Status;
use App\Models\V1\Principal\Deliverie;
use Maatwebsite\Excel\Concerns\ToCollection;

class DataPrueba implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $value) {
            if (!is_null($value)) {

                DB::beginTransaction();

                if (!is_numeric($value[0])) {
                    $cliente = Client::firstOrCreate(
                        ['card_code' => $value[0]],
                        ['nit' => $value[1], 'name' => $value[2]]
                    );

                    if(is_numeric($value[5])) {
                        $venta = Deliverie::where('docto_no', $value[5])->first();

                        if (is_null($venta)) {
                            $venta = Deliverie::create(
                                [
                                    'docto_no' => $value[5],
                                    'seller' => $value[4],
                                    'description' => null,
                                    'delivery_time' => null,
                                    'status_id' => Status::all()->random()->id,
                                    'client_id' => $cliente->id,
                                    'user_id' => 1
                                ]
                            );
                        }

                        if (is_numeric($value[9])) {
                            Detail::create(
                                [
                                    'item_code' => $value[7],
                                    'amount' => $value[9],
                                    'found' => 0,
                                    'description' => $value[8],
                                    'observation' => null,
                                    'delivery_time' => null,
                                    'status_id' => Status::EN_PROCESO,
                                    'deliverie_id' => $venta->id,
                                    'user_id' => $venta->user_id
                                ]
                            );
                        }
                    }
                }

                DB::commit();

                echo "{$value[5]} - {$value[7]}" . PHP_EOL;
            }
        }
    }
}
