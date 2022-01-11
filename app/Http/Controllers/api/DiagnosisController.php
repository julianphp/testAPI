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
    /**
     * Create a new diagnosis for a given patient.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *  POST ['personalidentification','description'] /api/diagnosis/new
     */
    public function new(Request $request): \Illuminate\Http\JsonResponse
    {
        $patient = Patients::dni($request->input('personalidentification'))->first();

        if (!$patient){
            return response()->json([
                'error' => true,
                'msg' => trans('patients.patient_not_exist')
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
            'personalIdentification' => $patient->personalIdentification,
            'diagnosis' => $diagnosis->description,
        ]);

    }

    /**
     * List all diagnosis for a given patient.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * POST ['personalidentification']  /api/diagnosis/patientListAll
     */
    public function patientListAll(Request $request): \Illuminate\Http\JsonResponse
    {
        $patient = Patients::dni($request->input('personalidentification'))->first();

        if (!$patient){
            return response()->json([
                'error' => true,
                'msg' => trans('patients.patient_not_exist')
            ]);
        }
        $response = [
            'error' => false,
            'fullName' => $patient->fullName,
            'diagnosis' => []
            ];
        $response['diagnosis'] += Diagnosis::select('description AS diagnosis','date')
                                            ->where('idPatient',$patient->id)
                                            ->get()->toArray();

        return response()->json($response);
    }
}
