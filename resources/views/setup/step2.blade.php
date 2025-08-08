<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Academic Management System') }} - Setup Step 2</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 12v-2m-6 6h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-2xl font-bold text-gray-900">
                Academic Configuration
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Step 2 of 4 - Configure academic year and grading system
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
            <div class="bg-white rounded-lg shadow px-6 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-indigo-600">Step 2 of 4</span>
                    <span class="text-sm text-gray-500">50% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: 50%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form method="POST" action="{{ route('setup.process-step2') }}" class="space-y-8">
                    @csrf

                    <!-- Academic Year Configuration -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Academic Year Setup</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Academic Year -->
                            <div class="md:col-span-3">
                                <label for="current_academic_year" class="block text-sm font-medium text-gray-700 mb-1">
                                    Current Academic Year <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="current_academic_year" id="current_academic_year" 
                                       value="{{ old('current_academic_year', session('setup_step2.current_academic_year', '2081-2082')) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('current_academic_year') border-red-500 @enderror"
                                       placeholder="e.g., 2081-2082">
                                <p class="mt-1 text-xs text-gray-500">Use Bikram Sambat year format</p>
                                @error('current_academic_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Academic Year Start Month -->
                            <div>
                                <label for="academic_year_start_month" class="block text-sm font-medium text-gray-700 mb-1">
                                    Academic Year Starts <span class="text-red-500">*</span>
                                </label>
                                <select name="academic_year_start_month" id="academic_year_start_month" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('academic_year_start_month') border-red-500 @enderror">
                                    <option value="">Select Month</option>
                                    @php
                                        $months = [
                                            1 => 'Baishakh (April)',
                                            2 => 'Jestha (May)',
                                            3 => 'Ashadh (June)',
                                            4 => 'Shrawan (July)',
                                            5 => 'Bhadra (August)',
                                            6 => 'Ashwin (September)',
                                            7 => 'Kartik (October)',
                                            8 => 'Mangsir (November)',
                                            9 => 'Poush (December)',
                                            10 => 'Magh (January)',
                                            11 => 'Falgun (February)',
                                            12 => 'Chaitra (March)'
                                        ];
                                        $selectedStart = old('academic_year_start_month', session('setup_step2.academic_year_start_month', 1));
                                    @endphp
                                    @foreach($months as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedStart == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_start_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Academic Year End Month -->
                            <div>
                                <label for="academic_year_end_month" class="block text-sm font-medium text-gray-700 mb-1">
                                    Academic Year Ends <span class="text-red-500">*</span>
                                </label>
                                <select name="academic_year_end_month" id="academic_year_end_month" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('academic_year_end_month') border-red-500 @enderror">
                                    <option value="">Select Month</option>
                                    @php
                                        $selectedEnd = old('academic_year_end_month', session('setup_step2.academic_year_end_month', 12));
                                    @endphp
                                    @foreach($months as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedEnd == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_end_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Grading Scale Configuration -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Default Grading Scale</h3>
                        
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600">
                                Choose a default grading scale for your institution. You can customize this later or create additional scales for different levels.
                            </p>

                            @php
                                $selectedScale = old('default_grading_scale', session('setup_step2.default_grading_scale', 'standard'));
                            @endphp

                            <!-- Standard Grading Scale -->
                            <div class="relative">
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $selectedScale === 'standard' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                                    <input type="radio" name="default_grading_scale" value="standard" 
                                           {{ $selectedScale === 'standard' ? 'checked' : '' }}
                                           class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium text-gray-900">Standard Grading Scale</div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            General purpose grading scale suitable for most institutions
                                        </div>
                                        <div class="text-xs text-gray-500 mt-2">
                                            A+ (90-100), A (80-89), B+ (70-79), B (60-69), C+ (50-59), C (40-49), D (30-39), F (0-29)
                                        </div>
                                        <div class="text-xs text-gray-500">Pass Mark: 40%</div>
                                    </div>
                                </label>
                            </div>

                            <!-- High School Grading Scale -->
                            <div class="relative">
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $selectedScale === 'high_school' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                                    <input type="radio" name="default_grading_scale" value="high_school" 
                                           {{ $selectedScale === 'high_school' ? 'checked' : '' }}
                                           class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium text-gray-900">High School Grading Scale</div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            Designed for high school level (Classes 9-12) with division system
                                        </div>
                                        <div class="text-xs text-gray-500 mt-2">
                                            A+ (90-100) Distinction, A (80-89) First Division, B+ (70-79) Second Division, etc.
                                        </div>
                                        <div class="text-xs text-gray-500">Pass Mark: 35%</div>
                                    </div>
                                </label>
                            </div>

                            <!-- University Grading Scale -->
                            <div class="relative">
                                <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $selectedScale === 'university' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                                    <input type="radio" name="default_grading_scale" value="university" 
                                           {{ $selectedScale === 'university' ? 'checked' : '' }}
                                           class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium text-gray-900">University Grading Scale</div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            Higher education grading scale with stricter requirements
                                        </div>
                                        <div class="text-xs text-gray-500 mt-2">
                                            A+ (85-100), A (75-84), B+ (65-74), B (55-64), C+ (50-54), C (45-49), D (35-44), F (0-34)
                                        </div>
                                        <div class="text-xs text-gray-500">Pass Mark: 45%</div>
                                    </div>
                                </label>
                            </div>

                            @error('default_grading_scale')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Educational Levels -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Educational Levels</h3>
                        
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600">
                                Select the educational levels your institution offers. This will help set up the appropriate academic structure.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($levels as $level)
                                    @php
                                        $selectedLevels = old('levels', session('setup_step2.levels', []));
                                    @endphp
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ in_array($level->id, $selectedLevels) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                                        <input type="checkbox" name="levels[]" value="{{ $level->id }}" 
                                               {{ in_array($level->id, $selectedLevels) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">{{ $level->name }}</div>
                                            @if($level->description)
                                                <div class="text-sm text-gray-600">{{ $level->description }}</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @error('levels')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between pt-8 border-t border-gray-200">
                        <a href="{{ route('setup.step1') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Continue
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Update visual feedback for radio buttons and checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[type="radio"]');
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            function updateSelection(input, isSelected) {
                const label = input.closest('label');
                if (isSelected) {
                    label.classList.add('border-indigo-500', 'bg-indigo-50');
                    label.classList.remove('border-gray-300');
                } else {
                    label.classList.remove('border-indigo-500', 'bg-indigo-50');
                    label.classList.add('border-gray-300');
                }
            }

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Reset all radio buttons in the same group
                    const groupName = this.name;
                    document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                        updateSelection(r, r.checked);
                    });
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelection(this, this.checked);
                });
            });
        });
    </script>
</body>
</html>
