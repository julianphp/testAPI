<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiagnosisController extends Controller
{
    //
    public function new(Request $request){
        $patient = Patients::dni($request->input('dni'))->first();
        Log::channel('daily')->debug($request->input('dni'));
        if (!$patient){
            return response()->json([
                'error' => true,
                'msg' => 'El paciente solicitado no existe'
            ]);
        }

        $request->validate([
            'description' => 'string|max:2000'
        ]);

        $diagnosis = new Diagnosis();
        $diagnosis->idPatient = $patient->id;
        $diagnosis->description = $request->input('description');
        $diagnosis->save();

        return response()->json([
            'error' => false,
            'fullName' => $patient->fullName,
            'dni' => $patient->DNI,
            'description' => $diagnosis->description,
        ]);

    }
    public function patientListAll(Request $request){
        $patient = Patients::dni($request->input('dni'))->first();

        if (!$patient){
            return response()->json([
                'error' => true,
                'msg' => 'El paciente solicitado no existe'
            ]);
        }
        $response = [
            'error' => false,
            'fullName' => $patient->fullName,
            'diagnosis' => []
            ];
        $response['diagnosis'] += Diagnosis::select('description','date')
                                            ->where('idPatient',$patient->id)
                                            ->get()->toArray();
        /*
        $response['diagnosis'] +=  DB::table('patients as pt')
                            ->join('diagnosis as dg','pt.id','=','dg.idPatient')
                            ->select('dg.description', 'dg.date')
                            ->where('pt.DNI', $patient->DNI)
                            ->get()->toArray();
        */
        return response()->json($response);
    }
}
