<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DownloadRecord extends Model
{
    use HasUuids;

    protected $fillable = ['exam_id', 'user_id', 'file_type', 'ip_address'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
