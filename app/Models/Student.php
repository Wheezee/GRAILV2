<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'email',
        'class_section_id',
    ];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function classSections()
    {
        // Compatibility: returns a hasOne relationship as a collection
        return $this->hasOne(ClassSection::class, 'id', 'class_section_id');
    }

    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
} 