<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public const STATUSES = [
        'present' => 'Present',
        'late'    => 'Late',
        'absent'  => 'Absent',
        'excused' => 'Excused',
    ];

    public const BADGES = [
        'present' => 'success',
        'late'    => 'warning',
        'absent'  => 'danger',
        'excused' => 'secondary',
    ];

    protected $fillable = [
        'student_id',
        'date',
        'status',
        'time_in',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getBadgeAttribute()
    {
        return self::BADGES[$this->status] ?? 'secondary';
    }
}
