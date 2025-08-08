<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Academic Management System') }} - Setup Step 1</title>

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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-2xl font-bold text-gray-900">
                Institution Information
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Step 1 of 4 - Tell us about your institution
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
            <div class="bg-white rounded-lg shadow px-6 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-indigo-600">Step 1 of 4</span>
                    <span class="text-sm text-gray-500">25% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: 25%"></div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form method="POST" action="{{ route('setup.process-step1') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Institution Information Section -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Institution Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Institution Name -->
                            <div class="md:col-span-2">
                                <label for="institution_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Institution Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="institution_name" id="institution_name" 
                                       value="{{ old('institution_name', session('setup_step1.institution_name')) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('institution_name') border-red-500 @enderror"
                                       placeholder="e.g., Kathmandu Model Secondary School">
                                @error('institution_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Institution Address -->
                            <div class="md:col-span-2">
                                <label for="institution_address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Institution Address <span class="text-red-500">*</span>
                                </label>
                                <textarea name="institution_address" id="institution_address" rows="3" required
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('institution_address') border-red-500 @enderror"
                                          placeholder="Complete address including ward, municipality/VDC, district">{{ old('institution_address', session('setup_step1.institution_address')) }}</textarea>
                                @error('institution_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="institution_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Phone Number
                                </label>
                                <input type="text" name="institution_phone" id="institution_phone" 
                                       value="{{ old('institution_phone', session('setup_step1.institution_phone')) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('institution_phone') border-red-500 @enderror"
                                       placeholder="01-4567890">
                                @error('institution_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="institution_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email Address
                                </label>
                                <input type="email" name="institution_email" id="institution_email" 
                                       value="{{ old('institution_email', session('setup_step1.institution_email')) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('institution_email') border-red-500 @enderror"
                                       placeholder="info@yourschool.edu.np">
                                @error('institution_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Website -->
                            <div class="md:col-span-2">
                                <label for="institution_website" class="block text-sm font-medium text-gray-700 mb-1">
                                    Website URL
                                </label>
                                <input type="url" name="institution_website" id="institution_website" 
                                       value="{{ old('institution_website', session('setup_step1.institution_website')) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('institution_website') border-red-500 @enderror"
                                       placeholder="https://www.yourschool.edu.np">
                                @error('institution_website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Logo and Seal Section -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Institution Logo & Seal</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Institution Logo -->
                            <div>
                                <label for="institution_logo" class="block text-sm font-medium text-gray-700 mb-1">
                                    Institution Logo
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="institution_logo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload logo</span>
                                                <input id="institution_logo" name="institution_logo" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                                @error('institution_logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Institution Seal -->
                            <div>
                                <label for="institution_seal" class="block text-sm font-medium text-gray-700 mb-1">
                                    Institution Seal
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="institution_seal" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload seal</span>
                                                <input id="institution_seal" name="institution_seal" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                                @error('institution_seal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Principal Information Section -->
                    <div class="border-t border-gray-200 pt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Principal Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Principal Name -->
                            <div>
                                <label for="principal_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Principal Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="principal_name" id="principal_name" 
                                       value="{{ old('principal_name', session('setup_step1.principal_name')) }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('principal_name') border-red-500 @enderror"
                                       placeholder="Full name of the principal">
                                @error('principal_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Principal Phone -->
                            <div>
                                <label for="principal_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Principal Phone
                                </label>
                                <input type="text" name="principal_phone" id="principal_phone" 
                                       value="{{ old('principal_phone', session('setup_step1.principal_phone')) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('principal_phone') border-red-500 @enderror"
                                       placeholder="Principal's contact number">
                                @error('principal_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Principal Email -->
                            <div class="md:col-span-2">
                                <label for="principal_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Principal Email
                                </label>
                                <input type="email" name="principal_email" id="principal_email" 
                                       value="{{ old('principal_email', session('setup_step1.principal_email')) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('principal_email') border-red-500 @enderror"
                                       placeholder="principal@yourschool.edu.np">
                                @error('principal_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between pt-8 border-t border-gray-200">
                        <a href="{{ route('setup.index') }}" 
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
        // File upload preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const logoInput = document.getElementById('institution_logo');
            const sealInput = document.getElementById('institution_seal');

            function handleFilePreview(input, previewContainer) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // You can add preview functionality here if needed
                            console.log('File selected:', file.name);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (logoInput) handleFilePreview(logoInput);
            if (sealInput) handleFilePreview(sealInput);
        });
    </script>
</body>
</html>
