<x-super-admin-layout>
    <!-- Page Header -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New School</h1>
                <p class="mt-2 text-gray-600">Set up a new school with login credentials and basic information</p>
            </div>
            <a href="{{ route('super-admin.schools.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Back to Schools
            </a>
        </div>


            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('super-admin.schools.store') }}">
                        @csrf

                        <!-- School Login Credentials -->
                        <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-blue-900 mb-2">School Login Credentials</h3>
                            <p class="text-sm text-blue-700 mb-4">These credentials will be provided to the school for accessing their academic management system.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="code" class="block text-sm font-medium text-blue-900">School ID (Login ID) *</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                               class="block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-lg"
                                               placeholder="e.g., ABC001" style="text-transform: uppercase;" maxlength="50">
                                        <button type="button" id="generate-code"
                                                class="ml-2 inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Generate
                                        </button>
                                    </div>
                                    <p class="mt-1 text-sm text-blue-600">This will be the School ID for login (e.g., ABC001, PQR002)</p>
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-blue-900">School Password *</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="password" name="password" id="password" required
                                               class="block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                                               placeholder="Secure password for school access" minlength="8">
                                        <button type="button" id="toggle-password"
                                                class="ml-2 inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" id="generate-password"
                                                class="ml-2 inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Generate
                                        </button>
                                    </div>
                                    <p class="mt-1 text-sm text-blue-600">This password will be used for school-level access. Minimum 8 characters.</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="password_confirmation" class="block text-sm font-medium text-blue-900">Confirm Password *</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                           class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                                           placeholder="Re-enter the password to confirm">
                                    <div id="password-match-indicator" class="mt-1 text-sm hidden">
                                        <span id="password-match-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- School Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">School Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">School Name *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., ABC International School">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">School Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="info@school.edu">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="+977-1-1234567">
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="address" id="address" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="School address">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <!-- Educational Levels Configuration -->
                        <div class="mb-8 border-t pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Educational Levels</h3>
                            <p class="text-sm text-gray-600 mb-4">Select which educational levels this school will offer. This will create the appropriate level structure for the school.</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative">
                                    <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors duration-200">
                                        <div class="flex items-center h-5">
                                            <input id="level_school" name="levels[]" type="checkbox" value="school"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                   {{ in_array('school', old('levels', ['school', 'college'])) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3">
                                            <label for="level_school" class="font-medium text-gray-700 cursor-pointer">School Level</label>
                                            <p class="text-gray-500 text-sm mt-1">Basic Education (Classes 1-10)</p>
                                            <div class="text-xs text-indigo-600 mt-1">
                                                <i class="fas fa-graduation-cap mr-1"></i>
                                                Primary & Secondary Education
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors duration-200">
                                        <div class="flex items-center h-5">
                                            <input id="level_college" name="levels[]" type="checkbox" value="college"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                   {{ in_array('college', old('levels', ['school', 'college'])) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3">
                                            <label for="level_college" class="font-medium text-gray-700 cursor-pointer">College Level</label>
                                            <p class="text-gray-500 text-sm mt-1">Higher Secondary (Classes 11-12)</p>
                                            <div class="text-xs text-indigo-600 mt-1">
                                                <i class="fas fa-school mr-1"></i>
                                                Pre-University Education
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors duration-200">
                                        <div class="flex items-center h-5">
                                            <input id="level_bachelor" name="levels[]" type="checkbox" value="bachelor"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                   {{ in_array('bachelor', old('levels', [])) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3">
                                            <label for="level_bachelor" class="font-medium text-gray-700 cursor-pointer">Bachelor Level</label>
                                            <p class="text-gray-500 text-sm mt-1">Undergraduate Programs</p>
                                            <div class="text-xs text-indigo-600 mt-1">
                                                <i class="fas fa-university mr-1"></i>
                                                Higher Education Programs
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                    </div>
                                    <div class="ml-2 text-sm text-blue-700">
                                        <strong>Important:</strong> School and College levels are pre-selected as they are commonly required.
                                        Select Bachelor level if your institution offers undergraduate programs.
                                        You can modify these levels later from the school's academic structure settings.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin User Information -->
                        <div class="mb-8 border-t pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Default Admin User</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="admin_name" class="block text-sm font-medium text-gray-700">Admin Name</label>
                                    <input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name', 'School Administrator') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="School Administrator">
                                </div>

                                <div>
                                    <label for="admin_email" class="block text-sm font-medium text-gray-700">Admin Email</label>
                                    <input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="admin@school.edu">
                                    <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate based on school code</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="admin_password" class="block text-sm font-medium text-gray-700">Admin Password</label>
                                    <input type="password" name="admin_password" id="admin_password" value="{{ old('admin_password') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Leave empty to auto-generate" minlength="8">
                                    <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate a secure password</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('super-admin.schools.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Create School
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>

    <script>
        // Auto-uppercase school code
        document.getElementById('code').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        // Educational levels validation and visual feedback
        function validateLevels() {
            const checkboxes = document.querySelectorAll('input[name="levels[]"]');
            const checkedBoxes = document.querySelectorAll('input[name="levels[]"]:checked');

            if (checkedBoxes.length === 0) {
                alert('Please select at least one educational level for the school.');
                return false;
            }
            return true;
        }

        // Add visual feedback for level selection
        function updateLevelVisuals() {
            const levelCheckboxes = document.querySelectorAll('input[name="levels[]"]');

            levelCheckboxes.forEach(function(checkbox) {
                const container = checkbox.closest('.relative').querySelector('div');

                if (checkbox.checked) {
                    container.classList.add('border-indigo-500', 'bg-indigo-50');
                    container.classList.remove('border-gray-200');
                } else {
                    container.classList.remove('border-indigo-500', 'bg-indigo-50');
                    container.classList.add('border-gray-200');
                }
            });
        }

        // Initialize level visuals and add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            updateLevelVisuals();

            const levelCheckboxes = document.querySelectorAll('input[name="levels[]"]');
            levelCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateLevelVisuals);
            });
        });

        // Add form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateLevels()) {
                e.preventDefault();
                return false;
            }
        });

        // Generate school code
        document.getElementById('generate-code').addEventListener('click', function() {
            const schoolName = document.getElementById('name').value;
            if (!schoolName) {
                alert('Please enter school name first');
                return;
            }

            fetch('{{ route("super-admin.schools.generate-code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name: schoolName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('code').value = data.code;
                } else {
                    alert(data.message || 'Failed to generate code');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to generate code');
            });
        });

        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('password_confirmation');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                confirmField.type = 'text';
                this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path></svg>';
            } else {
                passwordField.type = 'password';
                confirmField.type = 'password';
                this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
            }
        });

        // Generate secure password
        document.getElementById('generate-password').addEventListener('click', function() {
            const password = generateSecurePassword();
            document.getElementById('password').value = password;
            document.getElementById('password_confirmation').value = password;

            // Temporarily show password
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('password_confirmation');
            passwordField.type = 'text';
            confirmField.type = 'text';

            // Show success message
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
            toast.textContent = 'Secure password generated and confirmed!';
            document.body.appendChild(toast);

            setTimeout(() => {
                document.body.removeChild(toast);
                passwordField.type = 'password';
                confirmField.type = 'password';
            }, 3000);

            // Trigger validation
            validatePasswordMatch();
        });

        function generateSecurePassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let password = "";
            for (let i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            return password;
        }

        // Password confirmation validation
        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const indicator = document.getElementById('password-match-indicator');
            const text = document.getElementById('password-match-text');

            if (confirmation.length > 0) {
                indicator.classList.remove('hidden');
                if (password === confirmation) {
                    text.textContent = '✓ Passwords match';
                    text.className = 'text-green-600';
                } else {
                    text.textContent = '✗ Passwords do not match';
                    text.className = 'text-red-600';
                }
            } else {
                indicator.classList.add('hidden');
            }
        }

        // Add event listeners for password validation
        document.getElementById('password').addEventListener('input', validatePasswordMatch);
        document.getElementById('password_confirmation').addEventListener('input', validatePasswordMatch);

        // Auto-generate admin email based on school code
        document.getElementById('code').addEventListener('input', function(e) {
            const code = e.target.value.toLowerCase();
            if (code && !document.getElementById('admin_email').value) {
                document.getElementById('admin_email').value = `admin@${code}.school`;
            }
        });

        // Form validation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;

            if (password !== confirmation) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }

            // Show loading state
            const submitButton = document.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating School...';
        });
    </script>
</x-super-admin-layout>
