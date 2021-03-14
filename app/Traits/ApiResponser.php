<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use App\Models\Sistema\TransportUpdate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait ApiResponser
{
  protected function successResponse($data, $code = 200)
  {
    return response()->json($data, $code);
  }

  protected function errorResponse($message, $code)
  {
    return response()->json(['error' => $message, 'code' => $code], $code);
  }
  
  protected function showAll(Collection $collection, $code = 200)
  {
    return $this->successResponse(['data' => $collection, 'code' => $code], $code);
  }

  protected function showOne(Model $instance, $code = 200)
  {
    return $this->successResponse(['data' => $instance, 'code' => $code], $code);
  }

  protected function getB64Image($base64_image)
  {
    // Obtener el String base-64 de los datos         
    $image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
    // Decodificar ese string y devolver los datos de la imagen        
    $image = base64_decode($image_service_str);
    // Retornamos el string decodificado
    return $image;
  }
}
