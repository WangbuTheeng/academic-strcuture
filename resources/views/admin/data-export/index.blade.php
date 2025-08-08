<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Data Export & Import') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Export and import academic data</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Overview</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Total Students</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_students']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 4h6m-6 4h6m-7 4h8"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-900">Total Marks</p>
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_marks']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-purple-900">Total Exams</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_exams']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-orange-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-orange-900">Total Classes</p>
                                    <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_classes']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Data</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Export Students -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Export Students</h4>
                            <form action="{{ route('admin.data-export.students') }}" method="GET" class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Class (Optional)</label>
                                    <select name="class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Classes</option>
                                        @foreach(\App\Models\ClassModel::with('level')->get() as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->level->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Format</label>
                                    <select name="format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                    Export Students
                                </button>
                            </form>
                        </div>

                        <!-- Export Marks -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Export Marks</h4>
                            <form action="{{ route('admin.data-export.marks') }}" method="GET" class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Exam (Optional)</label>
                                    <select name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Exams</option>
                                        @foreach(\App\Models\Exam::with('academicYear')->latest()->get() as $exam)
                                            <option value="{{ $exam->id }}">{{ $exam->name }} ({{ $exam->academicYear->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Format</label>
                                    <select name="format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                    Export Marks
                                </button>
                            </form>
                        </div>

                        <!-- Export Results -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Export Results</h4>
                            <form action="{{ route('admin.data-export.results') }}" method="GET" class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Exam (Optional)</label>
                                    <select name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">All Exams</option>
                                        @foreach(\App\Models\Exam::with('academicYear')->latest()->get() as $exam)
                                            <option value="{{ $exam->id }}">{{ $exam->name }} ({{ $exam->academicYear->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Format</label>
                                    <select name="format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                    Export Results
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Import Data</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Import Students -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Import Students</h4>
                            <form action="{{ route('admin.data-export.import-students') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CSV File</label>
                                    <input type="file" name="file" accept=".csv,.txt" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Format: Name, Roll Number, Email, Phone, Address</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Target Class</label>
                                    <select name="class_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Select Class</option>
                                        @foreach(\App\Models\ClassModel::with('level')->get() as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->level->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                    Import Students
                                </button>
                            </form>
                        </div>

                        <!-- Import Marks -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Import Marks</h4>
                            <form action="{{ route('admin.data-export.import-marks') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CSV File</label>
                                    <input type="file" name="file" accept=".csv,.txt" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Format: Roll Number, Obtained Marks</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Exam</label>
                                    <select name="exam_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Select Exam</option>
                                        @foreach(\App\Models\Exam::with('academicYear')->latest()->get() as $exam)
                                            <option value="{{ $exam->id }}">{{ $exam->name }} ({{ $exam->academicYear->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                                    <select name="subject_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Select Subject</option>
                                        @foreach(\App\Models\Subject::all() as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                    Import Marks
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Errors Display -->
            @if(session('import_errors'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-medium text-red-900 mb-2">Import Errors</h4>
                <div class="max-h-40 overflow-y-auto">
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Export Templates -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Templates</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ asset('templates/students_template.csv') }}" download
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition duration-200">
                            <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-gray-900">Students Template</h4>
                                <p class="text-sm text-gray-500">CSV template for student import</p>
                            </div>
                        </a>

                        <a href="{{ asset('templates/marks_template.csv') }}" download
                           class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition duration-200">
                            <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-gray-900">Marks Template</h4>
                                <p class="text-sm text-gray-500">CSV template for marks import</p>
                            </div>
                        </a>

                        <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                            <svg class="h-6 w-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-gray-900">Import Guidelines</h4>
                                <p class="text-sm text-gray-500">Read before importing data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
