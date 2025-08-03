@extends('layouts.app')

@section('content')
<!-- Breadcrumbs -->
<nav class="mb-6" aria-label="Breadcrumb">
  <ol class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
    <li class="flex items-center">
      <a href="{{ route('dashboard') }}" class="hover:text-red-600 dark:hover:text-red-400 transition-colors whitespace-nowrap">
        Home
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('subjects.index') }}" class="hover:text-red-600 dark:hover:text-red-400 transition-colors whitespace-nowrap">
        Subjects
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('subjects.classes', $classSection->subject->id) }}" class="hover:text-red-600 dark:hover:text-red-400 max-w-[120px] sm:max-w-none truncate">
        {{ $classSection->subject->code }} - {{ $classSection->subject->title }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('grading.system', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'term' => 'midterm']) }}" class="hover:text-red-600 dark:hover:text-red-400 max-w-[120px] sm:max-w-none truncate">
        {{ $classSection->section }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">Gradebook</span>
    </li>
  </ol>
</nav>

<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gradebook - {{ $classSection->section }}</h2>
    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $classSection->subject->code }} - {{ $classSection->subject->title }}</p>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
      @if($gradingStructure)
        Weights: Midterm {{ $gradingStructure->midterm_weight }}% | Final {{ $gradingStructure->final_weight }}%
      @else
        Weights: Midterm 50% | Final 50%
      @endif
    </p>
  </div>
  <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-4 w-full">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
      <label for="grading_mode" class="text-sm font-medium text-gray-700 dark:text-gray-300 sm:mr-2">Grading Mode:</label>
      <select id="grading_mode" class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-red-500 w-full sm:w-auto">
        <option value="percentage">Percentage-Based</option>
        <option value="computed">Computed (1.0–5.0)</option>
        <option value="rule_based">Rule-Based (1.0–5.0)</option>
      </select>
    </div>
    <div class="hidden sm:block w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
    <a href="{{ route('grading.system', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'term' => 'midterm']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors w-full sm:w-auto justify-center">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
      Back to Grading
    </a>
    <button
      class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white font-medium rounded-lg transition-colors hover:bg-red-600 w-full sm:w-auto justify-center"
      onclick="document.getElementById('exportModal').classList.remove('hidden')"
    >
      <i data-lucide="download" class="w-4 h-4"></i>
      Export
    </button>
  </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
  <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-80">
    <h3 class="text-lg font-bold mb-4">Export Gradebook</h3>
    <form method="GET" action="{{ route('gradebook.export', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}">
      <input type="hidden" name="format" id="exportFormat" value="">
      <button type="button" class="w-full mb-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        onclick="document.getElementById('exportFormat').value='pdf'; this.form.submit();">
        PDF
      </button>
      <button type="button" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        onclick="document.getElementById('exportFormat').value='excel'; this.form.submit();">
        Excel
      </button>
      <button type="button" class="w-full mt-4 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
        onclick="document.getElementById('exportModal').classList.add('hidden')">
        Cancel
      </button>
    </form>
  </div>
</div>

<!-- Gradebook Table -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full min-w-[1400px]">
    <thead>
      <tr>
        <th rowspan="3" class="px-6 py-3 text-left bg-white dark:bg-gray-800 sticky left-0 top-0 z-20 border-b border-gray-200 dark:border-gray-700">
          <div class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Students</div>
        </th>
        
        <!-- Midterm Section -->
        @if($midtermAssessmentTypes->count() > 0)
          @php
            $midtermColspan = 0;
            foreach($midtermAssessmentTypes as $type) {
              $midtermColspan += $assessments['midterm'][$type->id]['assessments']->count() ?: 1;
            }
          @endphp
          <th colspan="{{ $midtermColspan }}" class="px-6 py-3 text-center bg-blue-50 dark:bg-blue-900/20 sticky top-0 z-10 border-b border-gray-200 dark:border-gray-700">
            <div class="text-sm font-medium text-blue-900 dark:text-blue-100">Midterm</div>
            <div class="text-xs text-blue-600 dark:text-blue-400">
              @if($gradingStructure)
                Weight: {{ $gradingStructure->midterm_weight }}%
              @else
                Weight: 50%
              @endif
            </div>
          </th>
        @endif
        
        <!-- Final Section -->
        @if($finalAssessmentTypes->count() > 0)
          @php
            $finalColspan = 0;
            foreach($finalAssessmentTypes as $type) {
              $finalColspan += $assessments['final'][$type->id]['assessments']->count() ?: 1;
            }
          @endphp
          <th colspan="{{ $finalColspan }}" class="px-6 py-3 text-center bg-green-50 dark:bg-green-900/20 sticky top-0 z-10 border-b border-gray-200 dark:border-gray-700">
            <div class="text-sm font-medium text-green-900 dark:text-green-100">Final</div>
            <div class="text-xs text-green-600 dark:text-green-400">
              @if($gradingStructure)
                Weight: {{ $gradingStructure->final_weight }}%
              @else
                Weight: 50%
              @endif
            </div>
          </th>
        @endif
        
        <th rowspan="3" class="px-6 py-3 text-center bg-white dark:bg-gray-800 sticky top-0 z-10 border-b border-gray-200 dark:border-gray-700">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Midterm Grade</div>
        </th>
        <th rowspan="3" class="px-6 py-3 text-center bg-white dark:bg-gray-800 sticky top-0 z-10 border-b border-gray-200 dark:border-gray-700">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Final Grade</div>
        </th>
        <th rowspan="3" class="px-6 py-3 text-center bg-white dark:bg-gray-800 sticky top-0 z-10 border-b border-gray-200 dark:border-gray-700">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Overall Grade</div>
        </th>
      </tr>
      <tr>
        <!-- Midterm Assessment Types -->
        @foreach($midtermAssessmentTypes as $assessmentType)
          @php
            $assessmentCount = $assessments['midterm'][$assessmentType->id]['assessments']->count();
            $colspan = max($assessmentCount, 1);
          @endphp
          <th colspan="{{ $colspan }}" class="px-4 py-2 text-center bg-blue-50 dark:bg-blue-900/20 sticky top-12 z-10 border-b border-gray-200 dark:border-gray-700">
            <div class="text-xs font-medium text-blue-900 dark:text-blue-100">{{ $assessmentType->name }}</div>
            <div class="text-xs text-blue-600 dark:text-blue-400">Weight: {{ $assessmentType->weight }}%</div>
          </th>
        @endforeach
        
        <!-- Final Assessment Types -->
        @foreach($finalAssessmentTypes as $assessmentType)
          @php
            $assessmentCount = $assessments['final'][$assessmentType->id]['assessments']->count();
            $colspan = max($assessmentCount, 1);
          @endphp
          <th colspan="{{ $colspan }}" class="px-4 py-2 text-center bg-green-50 dark:bg-green-900/20 sticky top-12 z-10 border-b border-gray-200 dark:border-gray-700">
            <div class="text-xs font-medium text-green-900 dark:text-green-100">{{ $assessmentType->name }}</div>
            <div class="text-xs text-green-600 dark:text-green-400">Weight: {{ $assessmentType->weight }}%</div>
          </th>
        @endforeach
      </tr>
      <tr>
        <!-- Midterm Assessments -->
        @foreach($midtermAssessmentTypes as $assessmentType)
          @php
            $assessmentList = $assessments['midterm'][$assessmentType->id]['assessments'];
          @endphp
          @if($assessmentList->count() > 0)
            @foreach($assessmentList as $assessment)
              <th class="px-4 py-2 text-center bg-blue-50 dark:bg-blue-900/20 sticky top-20 z-10 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('assessments.index', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'term' => 'midterm', 'assessmentType' => $assessmentType->id]) }}" 
                   class="text-blue-600 dark:text-blue-400 hover:underline text-xs">
                  {{ $assessment->name }}
                </a>
                <div class="text-xs text-blue-500 dark:text-blue-400">Max: {{ $assessment->max_score }}</div>
              </th>
            @endforeach
          @else
            <th class="px-4 py-2 text-center bg-blue-50 dark:bg-blue-900/20 sticky top-20 z-10 border-b border-gray-200 dark:border-gray-700">
              <div class="text-xs text-blue-500 dark:text-blue-400">No Assessments</div>
            </th>
          @endif
        @endforeach
        
        <!-- Final Assessments -->
        @foreach($finalAssessmentTypes as $assessmentType)
          @php
            $assessmentList = $assessments['final'][$assessmentType->id]['assessments'];
          @endphp
          @if($assessmentList->count() > 0)
            @foreach($assessmentList as $assessment)
              <th class="px-4 py-2 text-center bg-green-50 dark:bg-green-900/20 sticky top-20 z-10 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('assessments.index', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'term' => 'final', 'assessmentType' => $assessmentType->id]) }}" 
                   class="text-green-600 dark:text-green-400 hover:underline text-xs">
                  {{ $assessment->name }}
                </a>
                <div class="text-xs text-green-500 dark:text-green-400">Max: {{ $assessment->max_score }}</div>
              </th>
            @endforeach
          @else
            <th class="px-4 py-2 text-center bg-green-50 dark:bg-green-900/20 sticky top-20 z-10 border-b border-gray-200 dark:border-gray-700">
              <div class="text-xs text-green-500 dark:text-green-400">No Assessments</div>
            </th>
          @endif
        @endforeach
      </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
      @forelse($students as $student)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
          <td class="px-6 py-4 bg-white dark:bg-gray-800 sticky left-0 z-10">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $student->last_name }}, {{ $student->first_name }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $student->student_id }}</div>
          </td>
          
          <!-- Midterm Scores -->
          @foreach($midtermAssessmentTypes as $assessmentType)
            @php
              $assessmentList = $assessments['midterm'][$assessmentType->id]['assessments'];
            @endphp
            @if($assessmentList->count() > 0)
              @foreach($assessmentList as $assessment)
                @php
                  $score = $student->assessmentScores()->where('assessment_id', $assessment->id)->first();
                  $displayScore = $score && $score->score !== null ? $score->score : '--';
                  $percentage = $score && $score->score !== null ? round(($score->score / $assessment->max_score) * 100, 1) : null;
                @endphp
                <td class="px-4 py-3 text-center hover:bg-blue-100 dark:hover:bg-blue-900/20 transition-colors">
                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $displayScore }}
                  </div>
                  @if($percentage !== null)
                    <div class="text-xs text-blue-600 dark:text-blue-400">
                      {{ $percentage }}%
                    </div>
                  @endif
                </td>
              @endforeach
            @else
              <td class="px-4 py-3 text-center hover:bg-blue-100 dark:hover:bg-blue-900/20 transition-colors">
                <div class="text-sm text-gray-500 dark:text-gray-400">--</div>
              </td>
            @endif
          @endforeach
          
          <!-- Final Scores -->
          @foreach($finalAssessmentTypes as $assessmentType)
            @php
              $assessmentList = $assessments['final'][$assessmentType->id]['assessments'];
            @endphp
            @if($assessmentList->count() > 0)
              @foreach($assessmentList as $assessment)
                @php
                  $score = $student->assessmentScores()->where('assessment_id', $assessment->id)->first();
                  $displayScore = $score && $score->score !== null ? $score->score : '--';
                  $percentage = $score && $score->score !== null ? round(($score->score / $assessment->max_score) * 100, 1) : null;
                @endphp
                <td class="px-4 py-3 text-center hover:bg-green-100 dark:hover:bg-green-900/20 transition-colors">
                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $displayScore }}
                  </div>
                  @if($percentage !== null)
                    <div class="text-xs text-green-600 dark:text-green-400">
                      {{ $percentage }}%
                    </div>
                  @endif
                </td>
              @endforeach
            @else
              <td class="px-4 py-3 text-center hover:bg-green-100 dark:hover:bg-green-900/20 transition-colors">
                <div class="text-sm text-gray-500 dark:text-gray-400">--</div>
              </td>
            @endif
          @endforeach
          
          <!-- Midterm Grade -->
          <td class="px-4 py-3 text-center font-semibold">
            @if($student->midterm_grade !== null)
              <span class="grade-display text-lg text-blue-600 dark:text-blue-400" data-grade="{{ $student->midterm_grade }}" data-type="percentage">
                {{ $student->midterm_grade }}%
              </span>
            @else
              <span class="text-sm text-gray-500 dark:text-gray-400">--</span>
            @endif
          </td>
          
          <!-- Final Grade -->
          <td class="px-4 py-3 text-center font-semibold">
            @if($student->final_grade !== null)
              <span class="grade-display text-lg text-green-600 dark:text-green-400" data-grade="{{ $student->final_grade }}" data-type="percentage">
                {{ $student->final_grade }}%
              </span>
            @else
              <span class="text-sm text-gray-500 dark:text-gray-400">--</span>
            @endif
          </td>
          
          <!-- Overall Grade -->
          <td class="px-4 py-3 text-center font-semibold">
            @if($student->overall_grade !== null)
              <span class="grade-display text-lg font-bold" data-grade="{{ $student->overall_grade }}" data-type="percentage">
                {{ $student->overall_grade }}%
              </span>
            @else
              <span class="text-sm text-gray-500 dark:text-gray-400">--</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="{{ ($midtermAssessmentTypes->count() + $finalAssessmentTypes->count() + 4) }}" class="px-6 py-12 text-center">
            <div class="text-gray-400 dark:text-gray-500 mb-4">
              <i data-lucide="users" class="w-16 h-16 mx-auto"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No students enrolled</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Enroll students to view grades</p>
            <a href="{{ route('grading.system', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'term' => 'midterm']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
              <i data-lucide="plus" class="w-4 h-4"></i>
              Enroll Students
            </a>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Grading Structure Summary -->
@if($gradingStructure)
<div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Grading Structure:</h4>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">
            Midterm ({{ $gradingStructure->midterm_weight }}%)
          </span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-green-100 dark:bg-green-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">
            Final ({{ $gradingStructure->final_weight }}%)
          </span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-gray-100 dark:bg-gray-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">
            Total: 100%
          </span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-red-100 dark:bg-red-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">
            {{ $students->count() }} students
          </span>
        </div>
      </div>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400">
      <span class="font-medium">{{ $students->count() }}</span> students enrolled
    </div>
  </div>
</div>
@endif

<script>
// Grade display functionality with multiple modes
let currentGradingMode = 'percentage'; // Default to percentage

// Grade conversion functions
function convertGrade(percentage, mode) {
  if (mode === 'percentage') {
    return percentage + '%';
  }

  if (mode === 'computed') {
    if (percentage >= 100) return '1.0';
    if (percentage >= 60) {
      const grade = 3.0 - ((percentage - 60) / 40) * 2.0;
      return grade.toFixed(2);
    }
    return '5.0';
  }

  if (mode === 'rule_based') {
    if (percentage >= 97) return '1.00';
    if (percentage >= 94) return '1.25';
    if (percentage >= 91) return '1.50';
    if (percentage >= 88) return '1.75';
    if (percentage >= 85) return '2.00';
    if (percentage >= 82) return '2.25';
    if (percentage >= 79) return '2.50';
    if (percentage >= 76) return '2.75';
    if (percentage >= 75) return '3.00';
    return '5.00';
  }
}

// Color coding for different grading modes
function getGradeColor(grade, mode) {
  if (mode === 'percentage') {
    return ''; // No color for percentage
  }
  
  if (mode === 'computed' || mode === 'rule_based') {
    const numGrade = parseFloat(grade);
    if (numGrade <= 1.0) return 'text-green-600'; // Excellent
    if (numGrade <= 1.5) return 'text-blue-600'; // Very Good to Good
    if (numGrade <= 1.75) return 'text-yellow-600'; // Satisfactory
    if (numGrade <= 2.5) return 'text-orange-600'; // Fair
    if (numGrade <= 2.75) return 'text-orange-600'; // Passing
    if (numGrade <= 3.0) return 'text-red-500'; // Lowest Passing
    return 'text-red-700'; // Failed
  }
  
  return '';
}

function updateGradeDisplay() {
  const gradeDisplays = document.querySelectorAll('.grade-display');
  const gradingMode = document.getElementById('grading_mode').value;
  
  gradeDisplays.forEach(display => {
    const grade = parseFloat(display.dataset.grade);
    if (!isNaN(grade)) {
      const convertedGrade = convertGrade(grade, gradingMode);
      display.textContent = convertedGrade;
      display.dataset.type = gradingMode;
      
      // Apply color coding
      const colorClass = getGradeColor(convertedGrade, gradingMode);
      display.className = 'grade-display text-lg';
      if (colorClass) {
        display.classList.add('font-bold', colorClass);
      }
    }
  });
}

// Add event listener to grading mode dropdown
document.addEventListener('DOMContentLoaded', function() {
  const gradingModeSelect = document.getElementById('grading_mode');
  if (gradingModeSelect) {
    gradingModeSelect.addEventListener('change', updateGradeDisplay);
  }
  
  // Initialize Lucide icons
  if (window.lucide) {
    lucide.createIcons();
  }
});
</script>
@endsection 