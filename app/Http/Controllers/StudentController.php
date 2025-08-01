<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\AssessmentType;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function showAnalysis($subjectId, $classSectionId, $studentId, $term)
    {
        $subject = Subject::findOrFail($subjectId);
        $classSection = ClassSection::where('id', $classSectionId)->where('subject_id', $subjectId)->firstOrFail();
        $student = Student::where('id', $studentId)->where('class_section_id', $classSectionId)->firstOrFail();
        $assessmentTypes = $subject->assessmentTypes()->where('term', $term)->with(['assessments.scores'])->orderBy('order')->get();
        return view('teacher.student-analysis', compact('student', 'subject', 'classSection', 'assessmentTypes', 'term'));
    }
}