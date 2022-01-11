<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $table = "patients";
    protected $primaryKey = "id";
    protected $fillable = ["fullName","personalIdentification"];


    public function Diagnosis(){
        return $this->hasMany(Diagnosis::class,'idPatient','id');
    }

    public function scopeDni($query, $dni){
            return $query->where('personalIdentification',(string)$dni);
    }


}
