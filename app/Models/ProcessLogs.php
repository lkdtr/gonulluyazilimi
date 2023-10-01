<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessLogs extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'process_logs';
    protected $primaryKey = 'id';

    public function getProcessBy() {
        if($this->process_by==0) {
            $res = ["name"=>"", "surname" => ""];
            return (object) $res;
        }
        $result = $this->hasOne('App\Models\User', 'id', 'process_by')->first();
        if($result==null) {
            $res = ["name"=>"Silinmiş", "surname" => ""];
            return (object) $res;
        }

        return $result;
    }
}
