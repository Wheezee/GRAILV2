@extends('layouts.app')

@section('content')
<div class="mb-8">
  <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Dashboard</h2>
  <p class="text-gray-600 dark:text-gray-400 mb-6">Welcome back!</p>

  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-2">
    <!-- Subjects Count -->
    <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">📚</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalSubjects ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Subjects</div>
    </div>
    <!-- Student Count -->
    <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">👨‍🎓</div>
      <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalStudents ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Enrolled Students</div>
    </div>
  </div>
</div>

<!-- Latest Created Items Section -->
<div class="mt-10">
  <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Latest Created Items</h3>
  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <!-- Latest Activities -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">📝 Activities</div>
      @forelse($latestActivities ?? [] as $activity)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <div class="w-full text-left p-2 rounded transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $activity->name ?? 'Sample Activity' }} ({{ $activity->subject->title ?? 'Sample Subject' }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $activity->created_at ? $activity->created_at->format('M d, Y H:i') : '--' }}</div>
          </div>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No activities yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Quizzes -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">📊 Quizzes</div>
      @forelse($latestQuizzes ?? [] as $quiz)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <div class="w-full text-left p-2 rounded transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $quiz->name ?? 'Sample Quiz' }} ({{ $quiz->subject->title ?? 'Sample Subject' }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $quiz->created_at ? $quiz->created_at->format('M d, Y H:i') : '--' }}</div>
          </div>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No quizzes yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Exams -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">🧪 Exams</div>
      @forelse($latestExams ?? [] as $exam)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <div class="w-full text-left p-2 rounded transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $exam->name ?? 'Sample Exam' }} ({{ $exam->subject->title ?? 'Sample Subject' }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $exam->created_at ? $exam->created_at->format('M d, Y H:i') : '--' }}</div>
          </div>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No exams yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Recitations -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">🎤 Recitations</div>
      @forelse($latestRecitations ?? [] as $recitation)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <div class="w-full text-left p-2 rounded transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $recitation->name ?? 'Sample Recitation' }} ({{ $recitation->subject->title ?? 'Sample Subject' }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $recitation->created_at ? $recitation->created_at->format('M d, Y H:i') : '--' }}</div>
          </div>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No recitations yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Projects -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">📋 Projects</div>
      @forelse($latestProjects ?? [] as $project)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <div class="w-full text-left p-2 rounded transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->name ?? 'Sample Project' }} ({{ $project->subject->title ?? 'Sample Subject' }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $project->created_at ? $project->created_at->format('M d, Y H:i') : '--' }}</div>
          </div>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No projects yet.</div>
      @endforelse
    </div>
  </div>
</div>

<!-- Quick Stats Section -->
<div class="mt-10">
  <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Stats</h3>
  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Upcoming Items -->
    <div class="bg-white dark:bg-gray-800 border border-yellow-200 dark:border-yellow-700 rounded-xl p-6 shadow-sm">
      <div class="text-3xl mb-2">⏰</div>
      <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalUpcoming ?? '0' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Upcoming Items</div>
    </div>
    
    <!-- Graded Items -->
    <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 rounded-xl p-6 shadow-sm">
      <div class="text-3xl mb-2">✅</div>
      <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalGraded ?? '0' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Graded Items</div>
    </div>
    
    <!-- Pending Items -->
    <div class="bg-white dark:bg-gray-800 border border-orange-200 dark:border-orange-700 rounded-xl p-6 shadow-sm">
      <div class="text-3xl mb-2">⏳</div>
      <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $totalPending ?? '0' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Pending Grading</div>
    </div>
    
    <!-- Class Sections -->
    <div class="bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-700 rounded-xl p-6 shadow-sm">
      <div class="text-3xl mb-2">🏫</div>
      <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $totalClassSections ?? '0' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Class Sections</div>
    </div>
  </div>
</div>
@endsection 