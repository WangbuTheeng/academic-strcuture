<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Academic Management System') }} - Setup</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Academic Management System
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Welcome to the setup wizard
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        System Setup Required
                    </h3>
                    <p class="text-gray-600 mb-8">
                        This appears to be your first time using the Academic Management System. 
                        Let's get you set up with a quick 4-step configuration process.
                    </p>
                </div>

                <!-- Setup Steps Overview -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">1</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Institution Information</p>
                                <p class="text-xs text-gray-500">Basic details about your institution</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400">2-3 min</div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 text-sm font-medium">2</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Academic Configuration</p>
                                <p class="text-xs text-gray-500">Academic year and grading setup</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400">1-2 min</div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 text-sm font-medium">3</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Admin Account</p>
                                <p class="text-xs text-gray-500">Create your administrator account</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400">1 min</div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 text-sm font-medium">4</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Review & Complete</p>
                                <p class="text-xs text-gray-500">Confirm settings and finish setup</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400">1 min</div>
                    </div>
                </div>

                <!-- Features Overview -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">What you'll get:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Student Management</p>
                                <p class="text-xs text-gray-500">Complete student information system</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Examination System</p>
                                <p class="text-xs text-gray-500">Flexible exam creation and marking</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Marksheet Generation</p>
                                <p class="text-xs text-gray-500">Automated PDF marksheet creation</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Role-based Access</p>
                                <p class="text-xs text-gray-500">Secure user management system</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Start Setup Button -->
                <div class="text-center">
                    <a href="{{ route('setup.step1') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        Start Setup Process
                    </a>
                    <p class="mt-3 text-xs text-gray-500">
                        This process will take approximately 5-7 minutes to complete
                    </p>
                </div>

                <!-- System Requirements -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">System Requirements Met:</h4>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            PHP {{ PHP_VERSION }}
                        </div>
                        <div class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Laravel {{ app()->version() }}
                        </div>
                        <div class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Database Connected
                        </div>
                        <div class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Storage Writable
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
