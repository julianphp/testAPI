<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function response;

class PatientsController extends Controller
{
    public function new(Request $request){
        $response = [];

        $fullName = $request->input('fullname');
        $dni = $request->input('dni');
        $request->validate([
            'fullname' => 'regex:/^[\pL\s\-]+$/u|max:75',
            'dni' => 'unique:patients,DNI'
            //'dni' => 'regex:/^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/i'
        ],[
            'fullname.regex' => trans('patients.error_fullname'),
            'fullname.max' => trans('patients.error_fullname'),
            'dni.unique' => 'willyyyyy',
        ]);

        //solo aceptamos DNI no NIE
        //TODO realizar comprobacion
        if ( strlen($request->input('dni')) !== 9 ){
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

    public function edit(Request $request){
        $patient = Patients::dni($request->input('dni'))->first();

        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => 'El paciente solicitado no existe'
            ]);
        }
        $request->validate([
            'fullname' => 'regex:/^[\pL\s\-]+$/u|max:75'
        ],[
            'fullname.regex' => trans('patients.error_fullname'),
        ]);

        $patient->fullName = $request->input('fullname');
        $patient->save();

        return response()->json([
            'fullName' => $patient->fullName,
            'dni' => $patient->DNI
        ]);
    }

    public function detail(Request $request){
        $patient = Patients::dni($request->input('dni'))->first();

        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => 'El paciente solicitado no existe'
            ]);
        }
        return response()->json([
            'error' => false,
            'fullName' => $patient->fullName,
            'DNI' => $patient->DNI,
        ]);
    }

    public function delete(Request $request){
        $patient = Patients::dni($request->input('dni'))->first();

        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => 'El paciente solicitado no existe.',
            ]);
        }

        if ($patient->Diagnosis->count() === 0){
            $patient->delete();

            return response()->json([
                'error' => false,
                'msg' => 'Se han borrado todos los datos relacionados con el paciente.'
            ]);
        }

        // si el paciente tiene algun diagnostico entonces no se podra borrar.
        if ($patient->Diagnosis->count() > 0 && (int)$request->input('force') !== 1) {
            return response()->json([
                'error' => true,
                'msg' => 'El paciente no se puede borrar debido a que tiene diagnosticos asociados. 123'
            ]);
        }

        // si se indica realizar un force en la peticion, entonces borrara tambien los diagnosticos.
        if ((int)$request->input('force') === 1){
            Diagnosis::where('id', \Arr::pluck($patient->Diagnosis, 'id'))->delete();
            $patient->delete();

            return response()->json([
                'error' => false,
                'msg' => 'Se ha borrado todos los datos relacionados con el paciente.'
            ]);
        }
    }

   public function listAll(){
        $allPatients = Patients::select('fullName','DNI')->get()->toArray();
        $response = ['error' => false];
        $response += $allPatients;
        return response()->json($response);
   }

}
