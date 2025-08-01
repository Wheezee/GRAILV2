<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'term',
        'score',
        'is_late',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_late' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
} 