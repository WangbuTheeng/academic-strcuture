<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Setup Complete - {{ config('app.name', 'Academic Structure') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Success Icon -->
            <div class="flex justify-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Success Message -->
            <div class="mt-6 text-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    Setup Complete!
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    Your Academic Structure system has been successfully configured.
                </p>
            </div>

            <!-- Success Details -->
            <div class="mt-8 bg-white shadow rounded-lg p-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Institution information configured</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Academic year and grading system set up</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Administrator account created</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-gray-700">Database initialized with default data</span>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-blue-900 mb-3">What's Next?</h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <span class="font-medium mr-2">1.</span>
                        <span>Log in to your admin dashboard to start managing the system</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-medium mr-2">2.</span>
                        <span>Add teachers, students, and subjects</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-medium mr-2">3.</span>
                        <span>Configure exam schedules and grading criteria</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-medium mr-2">4.</span>
                        <span>Customize marksheet templates and reports</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
                <a href="{{ route('login') }}" 
                   class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Go to Login
                </a>
                <a href="{{ route('admin.dashboard') }}" 
                   class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Admin Dashboard
                </a>
            </div>

            <!-- Support Information -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    Need help? Check the documentation or contact support.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect to login after 10 seconds
        setTimeout(function() {
            window.location.href = "{{ route('login') }}";
        }, 10000);

        // Show countdown
        let countdown = 10;
        const countdownElement = document.createElement('p');
        countdownElement.className = 'text-xs text-gray-400 mt-2';
        countdownElement.textContent = `Redirecting to login in ${countdown} seconds...`;
        document.querySelector('.text-xs.text-gray-500').parentNode.appendChild(countdownElement);

        const countdownInterval = setInterval(function() {
            countdown--;
            countdownElement.textContent = `Redirecting to login in ${countdown} seconds...`;
            if (countdown <= 0) {
                clearInterval(countdownInterval);
            }
        }, 1000);
    </script>
</body>
</html>
