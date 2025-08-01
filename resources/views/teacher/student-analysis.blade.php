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
      <a href="{{ route('subjects.classes', $subject->id) }}" class="hover:text-red-600 dark:hover:text-red-400 max-w-[120px] sm:max-w-none truncate">
        {{ $subject->code }} - {{ $subject->title }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('grading.system', ['subject' => $subject->id, 'classSection' => $classSection->id, 'term' => $term]) }}" class="hover:text-red-600 dark:hover:text-red-400 transition-colors whitespace-nowrap">
        {{ $classSection->section }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">{{ $student->first_name }} {{ $student->last_name }} Analysis</span>
    </li>
  </ol>
</nav>

<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-2">{{ $student->first_name }} {{ $student->last_name }} <span class="text-gray-500">({{ $student->student_id }})</span></h1>
    <p class="mb-4 text-gray-600">Email: <a href="mailto:{{ $student->email }}" class="underline">{{ $student->email }}</a></p>
    <p class="mb-6 text-gray-700 dark:text-gray-300 font-medium">Subject: <span class="font-semibold">{{ $subject->code }} - {{ $subject->title }}</span></p>

    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Scores Over Time</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($assessmentTypes as $type)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold mb-2">{{ $type->name }}</h3>
                <canvas id="chart-{{ $type->id }}" height="180"></canvas>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@foreach($assessmentTypes as $type)
    const labels{{ $type->id }} = @json($type->assessments->pluck('name'));
    const percents{{ $type->id }} = @json($type->assessments->map(function($a) use ($student) {
        $score = $a->scores->where('student_id', $student->id)->first();
        $max = $a->max_score ?? 0;
        return ($score && $score->score !== null && $max > 0) ? round(($score->score / $max) * 100, 1) : null;
    }));
    new Chart(document.getElementById('chart-{{ $type->id }}').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels{{ $type->id }},
            datasets: [{
                label: '{{ $type->name }} (%)',
                data: percents{{ $type->id }},
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            }]
        },
        options: {scales: {y: {beginAtZero: true, max: 100}}}
    });
@endforeach
</script>
@endsection