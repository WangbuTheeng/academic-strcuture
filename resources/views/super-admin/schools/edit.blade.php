<x-super-admin-layout>
    <!-- Page Header -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit School: {{ $school->name }}</h1>
                <p class="mt-2 text-gray-600">Update school information and educational levels</p>
            </div>
            <a href="{{ route('super-admin.schools.show', $school) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Back to School
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
                    <form method="POST" action="{{ route('super-admin.schools.update', $school) }}">
                        @csrf
                        @method('PUT')

                        <!-- School Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">School Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">School Name *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $school->name) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., ABC International School">
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700">School Code *</label>
                                    <input type="text" name="code" id="code" value="{{ old('code', $school->code) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="e.g., ABC001" style="text-transform: uppercase;" maxlength="50">
                                    <p class="mt-1 text-sm text-gray-500">Unique identifier for the school</p>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">School Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $school->email) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="info@school.edu">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $school->phone) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="+977-1-1234567">
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea name="address" id="address" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                              placeholder="School address">{{ old('address', $school->address) }}</textarea>
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                    <select name="status" id="status" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="active" {{ old('status', $school->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $school->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $school->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New School Password</label>
                                    <input type="password" name="password" id="password"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Leave empty to keep current password" minlength="8">
                                    <p class="mt-1 text-sm text-gray-500">Leave empty to keep the current password. Minimum 8 characters if changing.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Educational Levels Configuration -->
                        <div class="mb-8 border-t pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Educational Levels</h3>
                            <p class="text-sm text-gray-600 mb-4">Select which educational levels this school offers. This will update the level structure for the school.</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative">
                                    <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors duration-200">
                                        <div class="flex items-center h-5">
                                            <input id="level_school" name="levels[]" type="checkbox" value="school"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                   {{ in_array('school', old('levels', $currentLevels)) ? 'checked' : '' }}>
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
                                                   {{ in_array('college', old('levels', $currentLevels)) ? 'checked' : '' }}>
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
                                                   {{ in_array('bachelor', old('levels', $currentLevels)) ? 'checked' : '' }}>
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

                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                                    </div>
                                    <div class="ml-2 text-sm text-yellow-700">
                                        <strong>Warning:</strong> Changing educational levels will update the school's academic structure.
                                        Removing a level will delete all associated programs, classes, and academic data for that level.
                                        This action cannot be undone.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- School Statistics (Read-only) -->
                        <div class="mb-8 border-t pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">School Statistics</h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $school->users()->count() }}</div>
                                    <div class="text-sm text-gray-600">Total Users</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $school->students()->count() }}</div>
                                    <div class="text-sm text-gray-600">Students</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ $school->users()->role('admin')->count() }}</div>
                                    <div class="text-sm text-gray-600">Admins</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $school->users()->role('teacher')->count() }}</div>
                                    <div class="text-sm text-gray-600">Teachers</div>
                                </div>
                            </div>
                        </div>

                        <!-- School Settings -->
                        <div class="mb-8 border-t pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">School Information</h3>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Important Information</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>Created:</strong> {{ $school->created_at->format('M d, Y H:i') }}</li>
                                                <li><strong>Last Updated:</strong> {{ $school->updated_at->format('M d, Y H:i') }}</li>
                                                <li><strong>School Code:</strong> Used by users to login to this specific school</li>
                                                <li><strong>Status:</strong> Inactive/Suspended schools cannot be accessed by users</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('super-admin.schools.show', $school) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update School
                            </button>
                        </div>
                    </form>
                </div>
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

        // Add form submission validation with warning for level changes
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateLevels()) {
                e.preventDefault();
                return false;
            }

            // Check if levels have changed and show warning
            const originalLevels = @json($currentLevels);
            const currentSelectedLevels = Array.from(document.querySelectorAll('input[name="levels[]"]:checked')).map(cb => cb.value);

            const levelsChanged = originalLevels.sort().join(',') !== currentSelectedLevels.sort().join(',');

            if (levelsChanged) {
                const confirmed = confirm(
                    'You are about to change the educational levels for this school. ' +
                    'This will update the academic structure and may affect existing programs and classes. ' +
                    'Are you sure you want to continue?'
                );

                if (!confirmed) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
</x-super-admin-layout>
