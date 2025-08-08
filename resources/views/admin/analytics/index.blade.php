<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Analytics Dashboard') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Comprehensive academic performance analytics</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="GET" class="flex gap-2">
                    <select name="academic_year_id" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $academicYear->id == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.analytics.export', ['type' => 'overview', 'format' => 'pdf']) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Overview Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Overview Statistics - {{ $academicYear->name }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-900">Total Students</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ number_format($analytics['overview']['total_students']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-900">Pass Percentage</p>
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($analytics['overview']['pass_percentage'], 1) }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-900">Average Percentage</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ number_format($analytics['overview']['average_percentage'], 1) }}%</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-orange-900">Total Exams</p>
                                    <p class="text-2xl font-bold text-orange-600">{{ number_format($analytics['overview']['total_exams']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Analytics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('admin.analytics.student-performance') }}" 
                           class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition duration-200">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">Student Performance</h4>
                                    <p class="text-sm text-gray-500">Individual student analytics</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.analytics.subject-analytics') }}" 
                           class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition duration-200">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">Subject Analytics</h4>
                                    <p class="text-sm text-gray-500">Subject-wise performance</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.analytics.class-analytics') }}" 
                           class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition duration-200">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">Class Analytics</h4>
                                    <p class="text-sm text-gray-500">Class-wise performance</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('admin.analytics.exam-analytics') }}" 
                           class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition duration-200">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">Exam Analytics</h4>
                                    <p class="text-sm text-gray-500">Exam-wise performance</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grade Distribution Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Distribution</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($analytics['performance']['grade_distribution'] as $grade => $count)
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900">{{ $count }}</div>
                            <div class="text-sm text-gray-600">Grade {{ $grade }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Subject Performance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Subjects</h3>
                    
                    <div class="space-y-4">
                        @foreach($analytics['performance']['subject_performance']->sortByDesc('average_percentage')->take(5) as $subject => $performance)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $subject }}</h4>
                                <p class="text-sm text-gray-600">{{ $performance['total_count'] }} students</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">{{ number_format($performance['average_percentage'], 1) }}%</div>
                                <div class="text-sm text-gray-600">
                                    {{ $performance['pass_count'] }}/{{ $performance['total_count'] }} passed
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Comparison with Previous Year -->
            @if($analytics['comparisons']['comparison_available'])
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Year-over-Year Comparison</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-medium text-blue-900 mb-2">Pass Percentage Change</h4>
                            <div class="text-2xl font-bold {{ $analytics['comparisons']['improvement']['pass_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $analytics['comparisons']['improvement']['pass_percentage'] >= 0 ? '+' : '' }}{{ number_format($analytics['comparisons']['improvement']['pass_percentage'], 1) }}%
                            </div>
                            <p class="text-sm text-gray-600">vs previous year</p>
                        </div>
                        
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-medium text-green-900 mb-2">Average Percentage Change</h4>
                            <div class="text-2xl font-bold {{ $analytics['comparisons']['improvement']['average_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $analytics['comparisons']['improvement']['average_percentage'] >= 0 ? '+' : '' }}{{ number_format($analytics['comparisons']['improvement']['average_percentage'], 1) }}%
                            </div>
                            <p class="text-sm text-gray-600">vs previous year</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
