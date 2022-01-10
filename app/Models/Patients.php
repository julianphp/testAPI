<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $table = "patients";
    protected $primaryKey = "id";
    protected $fillable = ["fullName","DNI"];


    public function Diagnosis(){
        return $this->hasMany(Diagnosis::class,'idPatient','id');
    }

    public function scopeDni($query, $dni){
        if ($dni){
            return $query->where('dni',$dni)->first();
        }
    }


}
