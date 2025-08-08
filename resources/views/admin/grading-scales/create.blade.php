<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create Grading Scale') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Define a new grading scale with grade ranges and GPA calculations</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.grading-scales.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Grading Scales
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.grading-scales.store') }}" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        <p class="text-sm text-gray-600 mt-1">Define the basic details of the grading scale</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="e.g., Standard Grading Scale">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Level -->
                            <div>
                                <label for="level_id" class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                                <select name="level_id" id="level_id" 
                                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Global (All Levels)</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Program -->
                            <div>
                                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                                <select name="program_id" id="program_id" 
                                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }} ({{ $program->level->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pass Mark -->
                            <div>
                                <label for="pass_mark" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pass Mark (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="pass_mark" id="pass_mark" value="{{ old('pass_mark', 40) }}" 
                                       min="0" max="100" step="0.01" required
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('pass_mark')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Max Marks -->
                            <div>
                                <label for="max_marks" class="block text-sm font-medium text-gray-700 mb-1">
                                    Maximum Marks <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_marks" id="max_marks" value="{{ old('max_marks', 100) }}" 
                                       min="1" step="0.01" required
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('max_marks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Checkboxes -->
                            <div class="md:col-span-2">
                                <div class="flex flex-wrap gap-6">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Set as Default</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Optional description of the grading scale">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Grade Ranges -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Grade Ranges</h3>
                                <p class="text-sm text-gray-600 mt-1">Define the grade ranges and their corresponding GPA values</p>
                            </div>
                            <button type="button" id="addGradeRange" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Grade Range
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="gradeRangesContainer" class="space-y-4">
                            <!-- Default grade ranges will be added here by JavaScript -->
                        </div>
                        @error('grade_ranges')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Grading Scale
                            </button>
                            <a href="{{ route('admin.grading-scales.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-lg transition duration-200">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let gradeRangeIndex = 0;
            const container = document.getElementById('gradeRangesContainer');
            const addButton = document.getElementById('addGradeRange');

            // Default grade ranges
            const defaultGrades = [
                { grade: 'A+', min: 90, max: 100, gpa: 4.0, description: 'Outstanding', passing: true },
                { grade: 'A', min: 80, max: 89, gpa: 3.6, description: 'Excellent', passing: true },
                { grade: 'B+', min: 70, max: 79, gpa: 3.2, description: 'Very Good', passing: true },
                { grade: 'B', min: 60, max: 69, gpa: 2.8, description: 'Good', passing: true },
                { grade: 'C+', min: 50, max: 59, gpa: 2.4, description: 'Satisfactory', passing: true },
                { grade: 'C', min: 40, max: 49, gpa: 2.0, description: 'Acceptable', passing: true },
                { grade: 'D', min: 30, max: 39, gpa: 1.6, description: 'Partially Acceptable', passing: false },
                { grade: 'F', min: 0, max: 29, gpa: 0.0, description: 'Fail', passing: false }
            ];

            // Add default grade ranges
            defaultGrades.forEach(grade => {
                addGradeRange(grade);
            });

            addButton.addEventListener('click', function() {
                addGradeRange();
            });

            function addGradeRange(data = {}) {
                const gradeRangeHtml = `
                    <div class="grade-range-item border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-sm font-medium text-gray-900">Grade Range ${gradeRangeIndex + 1}</h4>
                            <button type="button" class="remove-grade-range text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grade</label>
                                <input type="text" name="grade_ranges[${gradeRangeIndex}][grade]" value="${data.grade || ''}" required
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="A+">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Min %</label>
                                <input type="number" name="grade_ranges[${gradeRangeIndex}][min_percentage]" value="${data.min || ''}" 
                                       min="0" max="100" step="0.01" required
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Max %</label>
                                <input type="number" name="grade_ranges[${gradeRangeIndex}][max_percentage]" value="${data.max || ''}" 
                                       min="0" max="100" step="0.01" required
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">GPA</label>
                                <input type="number" name="grade_ranges[${gradeRangeIndex}][gpa]" value="${data.gpa || ''}" 
                                       min="0" max="4" step="0.1" required
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <input type="text" name="grade_ranges[${gradeRangeIndex}][description]" value="${data.description || ''}"
                                       class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Optional">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center">
                                    <input type="checkbox" name="grade_ranges[${gradeRangeIndex}][is_passing]" value="1" 
                                           ${data.passing !== false ? 'checked' : ''}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Passing</span>
                                </label>
                            </div>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', gradeRangeHtml);
                gradeRangeIndex++;

                // Add remove functionality
                const removeButtons = container.querySelectorAll('.remove-grade-range');
                removeButtons[removeButtons.length - 1].addEventListener('click', function() {
                    this.closest('.grade-range-item').remove();
                });
            }
        });
    </script>
</x-app-layout>
