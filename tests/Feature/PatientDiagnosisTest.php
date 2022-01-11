<?php

namespace Tests\Feature;

use App\Models\User;
use Faker\Factory;
use Faker\Guesser\Name;
use Faker\Provider\es_ES\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientDiagnosisTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNewPatient()
    {

        DB::beginTransaction();
        $faker = Factory::create();
        $password = $faker->password;
        $user = new User();
        $user->email = $faker->email;
        $user->password = Hash::make($password);
        $user->save();

        $response = $this->post('api/login',['email' => $user->email,'password' => $password]);

        $response->assertJson([
            'error' => false,
        ]);
        $token = $response->decodeResponseJson()['token'];

        $name = \Str::limit($faker->name,253,'');
        $identification = Person::dni();

        //insert new patient
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('api/patient/new',['fullname' => $name,'personalidentification' => $identification]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertJson([
            'error' => false,
        ]);

        //edit patient
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('api/patient/edit',['personalidentification' => $identification, 'fullname' => \Str::limit($faker->name,254,'')]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertJson([
            'error' => false,
        ]);

        //show patient details
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('api/patient/details',['personalidentification' => $identification]);

        $response->assertJson([
            'error' => false,
        ]);

        //list all patients
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('api/patient/listAll');

        $response->assertJson([
            'error' => false,
        ]);



        // Diagnosis add new
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('api/diagnosis/new',['personalidentification' => $identification,'diagnosis' => $faker->text(100)]);

        $response->assertJson([
            'error' => false,
        ]);
        // Diagnosis show all for an patient
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('api/diagnosis/patientListAll',['personalidentification' => $identification]);

        $response->assertJson([
            'error' => false,
        ]);

        // delete patient
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('api/patient/delete',['personalidentification' => $identification, 'force' => 1]);

        $response->assertJson([
            'error' => false,
        ]);

        // logout patient
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('api/logout',['personalidentification' => $identification]);

        $response->assertJson([
            'error' => false,
        ]);


        DB::rollBack(); //FIXME delete all record in db at finish

    }
}
