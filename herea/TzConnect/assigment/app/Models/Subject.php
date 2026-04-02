<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{

public function exams(){
return $this->belongsTo(Exam::class);
}
}
