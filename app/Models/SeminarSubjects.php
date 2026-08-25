<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeminarSubjects extends Model
{
    use HasFactory;

    protected $table = 'seminar_subjects';
    protected $primaryKey = 'id';

    protected $fillable = [
        'subject',
        'type',
        'summary',
        'syllabus',
        'duration',
        'status',
        'created_by',
        'updated_by',
    ];

    public function seminarRequests()
    {
        return $this->hasMany(SeminarRequests::class);
    }

    public function getCreatedBy() {
        if($this->created_by==0) {
            $res = ["name"=>"", "surname" => ""];
            return (object) $res;
        }
        return $this->hasOne('App\Models\User', 'id', 'created_by')->first();
    }

    public function getUpdatedBy() {
        if($this->updated_by==0) {
            $res = ["name"=>"", "surname" => ""];
            return (object) $res;
        }
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->first();
    }
}
