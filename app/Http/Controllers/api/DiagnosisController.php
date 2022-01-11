<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\DiagnosisHistoryLog;
use App\Models\Patients;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'diagnosis' => 'string|max:2000'
        ]);
        try {
            DB::beginTransaction();
            $diagnosis = new Diagnosis();
            $diagnosis->idPatient = $patient->id;
            $diagnosis->description = $request->input('diagnosis');
            $diagnosis->date = Carbon::now();
            $diagnosis->save();

            $logHistory = new DiagnosisHistoryLog();
            $logHistory->idReg = $diagnosis->id;
            $logHistory->editBy = Auth::user()->id;
            $logHistory->oldDescription = $diagnosis->description;
            $logHistory->oldDate = Carbon::make($diagnosis->date);
            $logHistory->save();

            DB::commit();
        } catch (\Exception $e){
            DB::rollBack();
            Log::channel('daily')->debug($e);

            return response()->json([
                'error' => true,
                'msg' => trans('messages.error_process_request')
            ]);
        }


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

        try {
            $response['diagnosis'] += Diagnosis::select('description AS diagnosis','date')
                ->where('idPatient',$patient->id)
                ->get()->toArray();
        } catch (\Exception $e){
            Log::channel('daily')->debug($e);
            return response()->json([
                'error' => true,
                'msg' => trans('messages.error_process_request')
            ]);
        }


        return response()->json($response);
    }
}
