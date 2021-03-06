<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\PatientHistoryLog;
use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;

use function response;

class PatientsController extends Controller
{
    /**
     * Create a new patient.
     * @param Request $request
     * @string $fullname
     * @string $personalidentification
     * @return \Illuminate\Http\JsonResponse
     * POST ['fullname','personalidentification'] /api/patient/new
     */
    public function new(Request $request): \Illuminate\Http\JsonResponse
    {
        $response = [];

        $fullName = $request->input('fullname');
        $personalidentification = $request->input('personalidentification');

        $validator = \Validator::make($request->all(),[
            'fullname' => 'regex:/^[\pL\s\.\'\-]+$/u|max:255',
            'personalidentification' => ['unique:patients,personalIdentification','regex:/^([0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE])|([XYZ][0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKE])$/i']
        ],[
            'fullname.regex' => trans('patients.error_fullname_format'),
            'fullname.max' => trans('patients.error_fullname'),
            'personalidentification.unique' => trans('patients.error_personalid_taken'),
            'personalidentification.regex' => trans('patients.error_personalid_incorrect')
        ]);
        if ($validator->errors()){
            $customReturn = ['error' => true];
            $customReturn += ['msg' => $validator->errors()];
            return response()->json($customReturn, 400);
        }
        if (empty($response)){
            try {
                DB::beginTransaction();

                $patient = new Patients();
                $patient->fullName = $fullName;
                $patient->personalIdentification = $personalidentification;
                $patient->save();

                $response += [
                    'error' => false,
                    'fullName' => $patient->fullName,
                    'personalIdentification' => $patient->personalIdentification,
                ];
                DB::commit();
            } catch (\Exception $e){
                DB::rollBack();
                Log::channel('daily')->debug($e);
            }


        }

        return response()->json($response,200);
    }

    /**
     * Edit the name of a given patient
     * @param Request $request
     * @string $fullname
     * @string $personalidentification
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
            ],404);
        }
        $validation = Validator::make($request->all(),[
            'fullname' => 'regex:/^[\pL\s\.\'\-]+$/u|max:255'
        ],[
            'fullname.regex' => trans('patients.error_fullname'),
            'fullname.max' => trans('patients.error_fullname'),
        ]);
        if ($validation->errors()){
            $customReturn = ['error' => true];
            $customReturn += ['msg' => $validation->errors()];

            return response()->json($customReturn,400);
        }

        try {

            DB::beginTransaction();
            //save log
            $patientSaveLog = new PatientHistoryLog();
            $patientSaveLog->patId = $patient->id;
            $patientSaveLog->editBy = Auth::user()->id;
            $patientSaveLog->oldFullName = $patient->fullName;
            $patientSaveLog->oldPersonalIdentification = $patient->personalIdentification;
            $patientSaveLog->save();

            $patient->fullName = $request->input('fullname');
            $patient->save();

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
            'dni' => $patient->personalIdentification
        ]);
    }

    /**
     * Return the details(fullname, personalidentification) of a given patient.
     * @param Request $request
     * @string $personalidentification
     * @return \Illuminate\Http\JsonResponse
     * POST ['personalidentification'] /api/patient/details
     */
    public function detail(Request $request): \Illuminate\Http\JsonResponse
    {
        $patient = Patients::dni($request->input('personalidentification'))->first();

        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => trans('patients.patient_not_exist')
            ],404);
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
     * @string $personalidentification
     * @return \Illuminate\Http\JsonResponse|void
     * POST ['personalidentification','force'] /api/patient/delete
     */
    public function delete(Request $request){
        $patient = Patients::dni($request->input('personalidentification'))->first();
        Log::channel('daily')->info($request->input('personalidentification'));
        if (!$patient) {
            return response()->json([
                'error' => true,
                'msg' => trans('patients.patient_not_exist')
            ],404);
        }

        if ($patient->Diagnosis->count() === 0){
            try {
                $patient->delete();
            } catch (\Exception $e){
                Log::channel('daily')->debug($e);

                return response()->json([
                    'error' => true,
                    'msg' => trans('messages.error_process_request')
                ]);
            }


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
            try {
                DB::beginTransaction();

                Diagnosis::where('id', \Arr::pluck($patient->Diagnosis, 'id'))->delete();
                $patient->delete();

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
       try {
           $allPatients = Patients::select('fullName','personalIdentification')->get()->toArray();
       } catch (\Exception $e) {
           Log::channel('daily')->debug($e);
           return response()->json([
               'error' => true,
               'msg' => trans('messages.error_process_request')
           ]);
       }
        $response = ['error' => false];
        $response += $allPatients;
        return response()->json($response);
   }

}
