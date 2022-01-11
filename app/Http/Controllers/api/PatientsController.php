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
    /**
     * Create a new patient.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * POST ['fullname','personalidentification'] /api/patient/new
     */
    public function new(Request $request): \Illuminate\Http\JsonResponse
    {
        $response = [];

        $fullName = $request->input('fullname');
        $dni = $request->input('personalidentification');
        $request->validate([
            'fullname' => 'regex:/^[\pL\s\-]+$/u|max:75',
            'dni' => ['unique:patients,personalIdentification','regex:/^([0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE])|([XYZ][0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKE])$/i']
            //'dni' => 'regex:/^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/i'
        ],[
            'fullname.regex' => trans('patients.error_fullname'),
            'fullname.max' => trans('patients.error_fullname'),
            'dni.unique' => trans('patients.error_personalid_taken'),
            'dni.regex' => trans('patients.error_personalid_incorrect')
        ]);

        if (empty($response)){
            $patient = new Patients();
            $patient->fullName = $fullName;
            $patient->personalIdentification = $dni;
            $patient->save();

            $response += [
                'error' => false,
                'fullName' => $patient->fullName,
                'personalIdentification' => $patient->personalIdentification,
            ];
            Log::info($patient);

        }

        return response()->json($response,200);
    }

    /**
     * Edit the name of a given patient
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * POST ['personalidentification','fullname'] /api/patient/edit
     */
    public function edit(Request $request): \Illuminate\Http\JsonResponse
    {
        $patient = Patients::dni($request->input('personalidentification'))->first();

        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => trans('patients.patient_not_exist')
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
            'dni' => $patient->personalIdentification
        ]);
    }

    /**
     * Return the details(fullname, personalidentification) of a given patient.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * POST ['personalidentification'] /api/patient/detail
     */
    public function detail(Request $request): \Illuminate\Http\JsonResponse
    {
        $patient = Patients::dni($request->input('personalidentification'))->first();

        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => trans('patients.patient_not_exist')
            ]);
        }
        return response()->json([
            'error' => false,
            'fullName' => $patient->fullName,
            'personalIdentification' => $patient->personalIdentification,
        ]);
    }

    /**
     * Delete a given patient. If have diagnosis associated, it will not delete
     * To force delete a patient with diagnosis, include "force=1" param in POST.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     * POST ['personalidentification','force'] /api/patient/delete
     */
    public function delete(Request $request){
        $patient = Patients::dni($request->input('personalidentification'))->first();

        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => trans('patients.patient_not_exist')
            ]);
        }

        if ($patient->Diagnosis->count() === 0){
            $patient->delete();

            return response()->json([
                'error' => false,
                'msg' => trans('patients.deleted_all_data_associated')
            ]);
        }

        // if the patient has a diagnosis then it cannot be deleted.
        if ($patient->Diagnosis->count() > 0 && (int)$request->input('force') !== 1) {
            return response()->json([
                'error' => true,
                'msg' => trans('patients.cant_delete_patient_with_diagnosis')
            ]);
        }

        // if a force is indicated in the request, then it will delete the diagnostics as well.
        if ((int)$request->input('force') === 1){
            Diagnosis::where('id', \Arr::pluck($patient->Diagnosis, 'id'))->delete();
            $patient->delete();

            return response()->json([
                'error' => false,
                'msg' => trans('patients.deleted_all_data_associated')
            ]);
        }
    }

    /**
     * List all patients, return fullNane and personalidentification.
     * @return \Illuminate\Http\JsonResponse
     * GET /api/patient/listALl
     */
   public function listAll(): \Illuminate\Http\JsonResponse
   {
        $allPatients = Patients::select('fullName','personalIdentification')->get()->toArray();
        $response = ['error' => false];
        $response += $allPatients;
        return response()->json($response);
   }

}
