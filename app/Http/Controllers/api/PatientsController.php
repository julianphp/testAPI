<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Patients;
use Illuminate\Support\Facades\Log;

use function response;

class PatientsController extends Controller
{
    public function new($fullName, $dni){
        $response = [];
        if (strlen($fullName) > 75){
            $response += [
                'error' => true,
                'details' => 'El nombre excede el tama;o maximo (75)'
            ];
        }
        //solo aceptamos DNI no NIE
        //TODO realizar comprobacion
        if ( strlen($dni) !== 9 ){
            $response += [
                'error' => true,
                'details' => 'DNI no es correcto.'
            ];
        }
        if (empty($response)){
            $patient = new Patients();
            $patient->fullName = $fullName;
            $patient->DNI = $dni;
            $patient->save();

            $response += [
                'error' => false,
                'fullName' => $patient->fullName,
                'DNI' => $patient->DNI,
            ];
            Log::info($patient);

        }

        return response()->json($response,200);
    }

    public function details($dni){
        return response()->json([
            'dfasdsdf' => 69696969
        ],200);
    }
}
