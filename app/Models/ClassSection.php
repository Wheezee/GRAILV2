<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'section',
        'schedule',
        'classroom',
        'student_count',
        'teacher_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
