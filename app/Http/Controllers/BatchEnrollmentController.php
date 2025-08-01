<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;


class BatchEnrollmentController extends Controller
{
    public function showUploadForm($subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        return view('teacher.batch-enrollment', compact('classSection'));
    }

    public function uploadStudents(Request $request, $subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            $students = [];
            $errors = [];
            $successCount = 0;
            $rowNumber = 0;

            // Convert XLSX/XLS to CSV if needed
            if ($extension === 'xlsx' || $extension === 'xls') {
                $rows = $this->convertExcelToCsv($file);
                if ($rows === null) {
                    return back()->withErrors(['excel_file' => 'Error reading Excel file. Please check the file format.'])->withInput();
                }
            } else {
                // Handle CSV file directly
                $handle = fopen($file->getRealPath(), 'r');
                $rows = [];
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }

            // Process each row
            foreach ($rows as $row) {
                $rowNumber++;
                
                // Skip header row
                if ($rowNumber === 1) {
                    continue;
                }

                // Get data from row array
                $data = $row;
                
                // Check if we have enough columns
                if (count($data) < 3) {
                    $errors[] = "Row {$rowNumber}: Insufficient data. Need at least Student ID, First Name, and Last Name.";
                    continue;
                }

                $studentData = [
                    'student_id' => trim($data[0]),
                    'first_name' => trim($data[1]),
                    'last_name' => trim($data[2]),
                    'email' => isset($data[3]) ? trim($data[3]) : null,
                ];

                // Validate student data
                $validator = Validator::make($studentData, [
                    'student_id' => 'required|string|max:255',
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'nullable|email|max:255',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Check if student already exists
                $existingStudent = Student::where('student_id', $studentData['student_id'])
                    ->where('class_section_id', $classSection->id)
                    ->first();

                if ($existingStudent) {
                    $errors[] = "Row {$rowNumber}: Student ID '{$studentData['student_id']}' already exists in this class.";
                    continue;
                }

                $students[] = $studentData;
            }

            // If no errors, save all students
            if (empty($errors)) {
                foreach ($students as $studentData) {
                    Student::create([
                        'student_id' => $studentData['student_id'],
                        'first_name' => $studentData['first_name'],
                        'last_name' => $studentData['last_name'],
                        'email' => $studentData['email'],
                        'class_section_id' => $classSection->id,
                    ]);
                    $successCount++;
                }

                // Update student count
                $classSection->update([
                    'student_count' => $classSection->students()->count()
                ]);

                return redirect()->route('grading.system', [
                    'subject' => $subjectId,
                    'classSection' => $classSectionId,
                    'term' => 'midterm'
                ])->with('success', "Successfully enrolled {$successCount} students!");
            } else {
                return back()->withErrors($errors)->withInput();
            }

        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Error reading file: ' . $e->getMessage()])->withInput();
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_enrollment_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Student ID', 'First Name', 'Last Name', 'Email (Optional)']);
            
            // Add example rows
            fputcsv($file, ['2021-0001', 'John', 'Doe', 'john.doe@email.com']);
            fputcsv($file, ['2021-0002', 'Jane', 'Smith', 'jane.smith@email.com']);
            fputcsv($file, ['2021-0003', 'Mike', 'Johnson', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function unenrollStudent(Request $request, $subjectId, $classSectionId, $studentId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $student = Student::where('id', $studentId)
            ->where('class_section_id', $classSection->id)
            ->firstOrFail();

        // Delete the student
        $student->delete();

        // Update student count
        $classSection->update([
            'student_count' => $classSection->students()->count()
        ]);

        return back()->with('success', "Student '{$student->first_name} {$student->last_name}' has been unenrolled successfully.");
    }

    public function bulkUnenrollStudents(Request $request, $subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $studentIds = $request->input('student_ids');
        $students = Student::whereIn('id', $studentIds)
            ->where('class_section_id', $classSection->id)
            ->get();

        $unenrolledCount = 0;
        foreach ($students as $student) {
            $student->delete();
            $unenrolledCount++;
        }

        // Update student count
        $classSection->update([
            'student_count' => $classSection->students()->count()
        ]);

        return back()->with('success', "Successfully unenrolled {$unenrolledCount} students.");
    }

    public function convertExcelToCsv($file)
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = [];

            foreach ($sheet->toArray(null, true, true, true) as $row) {
                $rows[] = array_values($row); // reset keys to 0-based
            }

            return $rows;

        } catch (\Exception $e) {
            \Log::error('Error reading Excel file: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update a student's details in the grading system.
     */
    public function updateStudent(Request $request, $subjectId, $classSectionId, $term, $studentId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $subjectModel = auth()->user()->subjects()->findOrFail($subjectId);
        $classSectionModel = \App\Models\ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subjectModel->id)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $student = $classSectionModel->students()->where('id', $studentId)->firstOrFail();

        $validated = $request->validate([
            'student_id' => 'required|string|max:255|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $student->update($validated);

        return back()->with('success', 'Student updated successfully!');
    }
} 