<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_no',
        'first_name',
        'last_name',
        'course',
        'year_level',
        'section',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getFullNameAttribute()
    {
        return $this->last_name . ', ' . $this->first_name;
    }
}
