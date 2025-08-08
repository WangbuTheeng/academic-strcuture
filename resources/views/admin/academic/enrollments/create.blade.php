@extends('layouts.admin')

@section('title', 'Enroll Student')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Enroll Student</h1>
            <p class="mb-0 text-muted">Enroll a student in a program and assign to a class</p>
        </div>
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Enrollments
        </a>
    </div>

    <!-- Enrollment Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Enrollment Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.enrollments.store') }}">
                        @csrf

                        <!-- Student Selection -->
                        <div class="mb-3">
                            <label for="student_id" class="form-label">
                                Select Student <span class="text-danger">*</span>
                            </label>
                            <select name="student_id" id="student_id" 
                                    class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">Choose Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }} 
                                        ({{ $student->admission_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the student to enroll</div>
                        </div>

                        <!-- Program Selection -->
                        <div class="mb-3">
                            <label for="program_id" class="form-label">
                                Program <span class="text-danger">*</span>
                            </label>
                            <select name="program_id" id="program_id" 
                                    class="form-select @error('program_id') is-invalid @enderror" required>
                                <option value="">Select Program</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" 
                                            data-level="{{ $program->level_id }}"
                                            data-department="{{ $program->department_id }}"
                                            {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }} 
                                        ({{ $program->department->name ?? 'N/A' }} - {{ $program->level->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('program_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the academic program</div>
                        </div>

                        <!-- Class Selection -->
                        <div class="mb-3">
                            <label for="class_id" class="form-label">
                                Class <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <select name="class_id" id="class_id"
                                        class="form-select @error('class_id') is-invalid @enderror" required>
                                    <option value="">Select Program First</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="retry-classes-btn"
                                        onclick="retryLoadClasses()" style="display: none;">
                                    <i class="fas fa-redo"></i> Retry
                                </button>
                            </div>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Classes will be loaded based on selected program</div>
                        </div>

                        <!-- Academic Year Selection -->
                        <div class="mb-3">
                            <label for="academic_year_id" class="form-label">
                                Academic Year <span class="text-danger">*</span>
                            </label>
                            <select name="academic_year_id" id="academic_year_id" 
                                    class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" 
                                            {{ old('academic_year_id', $year->is_current ? $year->id : '') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                        @if($year->is_current) (Current) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the academic year for enrollment</div>
                        </div>

                        <div class="row">
                            <!-- Enrollment Date -->
                            <div class="col-md-6 mb-3">
                                <label for="enrollment_date" class="form-label">
                                    Enrollment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="enrollment_date" id="enrollment_date" 
                                       value="{{ old('enrollment_date', date('Y-m-d')) }}"
                                       class="form-control @error('enrollment_date') is-invalid @enderror" required>
                                @error('enrollment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Date of enrollment</div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" 
                                        class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="transferred" {{ old('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Current enrollment status</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Roll Number -->
                            <div class="col-md-6 mb-3">
                                <label for="roll_number" class="form-label">
                                    Roll Number
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="suggest-roll-btn">
                                        <i class="fas fa-magic"></i> Suggest Next
                                    </button>
                                </label>
                                <input type="text" name="roll_number" id="roll_number"
                                       value="{{ old('roll_number') }}"
                                       class="form-control @error('roll_number') is-invalid @enderror"
                                       placeholder="Leave empty for auto-generation">
                                @error('roll_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="roll-number-help">
                                        <strong>Sequential Roll Numbers:</strong> Auto-generated in enrollment order (e.g., CS25001, CS25002, CS25003...)
                                        <br>Leave empty for auto-generation, or enter a custom roll number
                                    </span>
                                    <div id="roll-number-status" class="mt-1"></div>
                                </div>
                            </div>

                            <!-- Section -->
                            <div class="col-md-6 mb-3">
                                <label for="section" class="form-label">
                                    Section
                                </label>
                                <input type="text" name="section" id="section" 
                                       value="{{ old('section') }}"
                                       class="form-control @error('section') is-invalid @enderror" 
                                       placeholder="e.g., A, B, Alpha">
                                @error('section')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Class section (if applicable)</div>
                            </div>
                        </div>

                        <!-- Information Alert -->
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle text-info"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-info">Enrollment Guidelines</h6>
                                    <ul class="mb-0 text-info">
                                        <li>Ensure the student is not already enrolled in the same program for the selected academic year</li>
                                        <li>The class should match the program's level and department</li>
                                        <li>Roll numbers are assigned sequentially in enrollment order (001, 002, 003...)</li>
                                        <li>After enrollment, you can assign specific subjects to the student</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i>
                                Enroll Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #374151;
}

.form-text {
    font-size: 0.875rem;
    color: #6b7280;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.text-danger {
    color: #dc3545 !important;
}

.btn {
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing enrollment form...');

        const programSelect = document.getElementById('program_id');
        const classSelect = document.getElementById('class_id');

        if (!programSelect) {
            console.error('Program select element not found!');
            return;
        }

        if (!classSelect) {
            console.error('Class select element not found!');
            return;
        }

        console.log('Found program and class select elements');
        console.log('Initial program value:', programSelect.value);

        // Load classes based on selected program
        programSelect.addEventListener('change', function() {
            const programId = this.value;
            console.log(`🔄 Program changed to: ${programId}`);

            // Reset class selection
            classSelect.innerHTML = '<option value="">Loading classes...</option>';
            classSelect.disabled = true;

            if (programId) {
                console.log(`📡 Loading classes for program ID: ${programId}`);

                const url = `{{ route('admin.enrollments.classes-by-program') }}?program_id=${programId}`;
                console.log('🌐 Fetching URL:', url);

                // Add visual feedback
                classSelect.style.backgroundColor = '#fff3cd';

                // Fetch classes for the selected program
                fetch(url, {
                    method: 'GET',
                    credentials: 'same-origin', // Include cookies for authentication
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                    .then(response => {
                        console.log(`📊 Response status: ${response.status}`);
                        console.log('📋 Response headers:', Object.fromEntries(response.headers.entries()));

                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('❌ Response body:', text);
                                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('✅ Classes data received:', data);
                        console.log('📊 Data type:', typeof data, 'Array?', Array.isArray(data));

                        classSelect.innerHTML = '<option value="">Select Class</option>';
                        classSelect.style.backgroundColor = '';

                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach((classItem, index) => {
                                console.log(`➕ Adding class ${index + 1}:`, classItem);
                                const option = document.createElement('option');
                                option.value = classItem.id;
                                // Use the formatted display_name from the backend
                                option.textContent = classItem.display_name || classItem.name;
                                classSelect.appendChild(option);
                            });
                            console.log(`✅ Successfully loaded ${data.length} classes for program ${programId}`);
                            classSelect.style.backgroundColor = '#d4edda';
                            setTimeout(() => classSelect.style.backgroundColor = '', 2000);
                        } else {
                            classSelect.innerHTML = '<option value="">No classes available for this program</option>';
                            console.warn(`⚠️ No classes found for program ${programId}. Data:`, data);
                            classSelect.style.backgroundColor = '#f8d7da';
                        }

                        classSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('❌ Error loading classes:', error);
                        console.error('🔍 Error details:', error.message);
                        console.error('📍 Stack trace:', error.stack);

                        classSelect.innerHTML = '<option value="">Error loading classes. Please try again.</option>';
                        classSelect.disabled = false;
                        classSelect.style.backgroundColor = '#f8d7da';

                        // Show retry button
                        document.getElementById('retry-classes-btn').style.display = 'block';

                        // Show user-friendly error message
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to load classes. Please refresh the page and try again.');
                        } else {
                            alert(`Failed to load classes: ${error.message}\n\nPlease check the browser console for more details.`);
                        }
                    });
            } else {
                console.log('ℹ️ No program selected, showing default message');
                classSelect.innerHTML = '<option value="">Select Program First</option>';
                classSelect.disabled = false;
                classSelect.style.backgroundColor = '';
            }
        });

        // Retry function for manual testing
        window.retryLoadClasses = function() {
            const programId = programSelect.value;
            if (programId) {
                console.log('🔄 Manual retry triggered for program:', programId);
                if (window.jQuery) {
                    loadClassesWithJQuery(programId);
                } else {
                    programSelect.dispatchEvent(new Event('change'));
                }
            } else {
                alert('Please select a program first');
            }
        };

        // Alternative jQuery implementation (fallback)
        window.loadClassesWithJQuery = function(programId) {
            console.log('🔄 Using jQuery fallback for program:', programId);

            if (!window.jQuery) {
                console.error('❌ jQuery not available');
                return;
            }

            classSelect.innerHTML = '<option value="">Loading with jQuery...</option>';
            classSelect.disabled = true;

            $.ajax({
                url: '{{ route("admin.enrollments.classes-by-program") }}',
                method: 'GET',
                data: { program_id: programId },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    console.log('✅ jQuery success:', data);
                    classSelect.innerHTML = '<option value="">Select Class</option>';

                    if (data && data.length > 0) {
                        data.forEach(classItem => {
                            const option = document.createElement('option');
                            option.value = classItem.id;
                            option.textContent = classItem.display_name || classItem.name;
                            classSelect.appendChild(option);
                        });
                    } else {
                        classSelect.innerHTML = '<option value="">No classes available</option>';
                    }
                    classSelect.disabled = false;
                },
                error: function(xhr, status, error) {
                    console.error('❌ jQuery error:', status, error);
                    console.error('📋 Response:', xhr.responseText);
                    classSelect.innerHTML = '<option value="">Error loading classes</option>';
                    classSelect.disabled = false;
                }
            });
        };

        // Trigger the load on page load if a program is already selected
        if (programSelect.value) {
            console.log('Program already selected on page load, triggering change event');
            programSelect.dispatchEvent(new Event('change'));
        } else {
            console.log('No program selected on page load');
        }

        // Roll number suggestion functionality
        const suggestRollBtn = document.getElementById('suggest-roll-btn');
        const rollNumberInput = document.getElementById('roll_number');
        // classSelect is already declared above, no need to redeclare
        const academicYearSelect = document.getElementById('academic_year_id');
        const rollNumberStatus = document.getElementById('roll-number-status');

        // Function to get next available roll number
        function suggestNextRollNumber() {
            const classId = classSelect.value;
            const academicYearId = academicYearSelect.value;

            if (!classId || !academicYearId) {
                rollNumberStatus.innerHTML = '<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Please select class and academic year first</small>';
                return;
            }

            suggestRollBtn.disabled = true;
            suggestRollBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

            fetch(`{{ route('admin.enrollments.next-roll-number') }}?class_id=${classId}&academic_year_id=${academicYearId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.roll_number) {
                        rollNumberInput.value = data.roll_number;
                        rollNumberStatus.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Suggested next available roll number</small>';
                    } else {
                        rollNumberStatus.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Could not generate roll number</small>';
                    }
                })
                .catch(error => {
                    console.error('Error getting roll number:', error);
                    rollNumberStatus.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Error getting roll number</small>';
                })
                .finally(() => {
                    suggestRollBtn.disabled = false;
                    suggestRollBtn.innerHTML = '<i class="fas fa-magic"></i> Suggest Next';
                });
        }

        // Function to validate roll number
        function validateRollNumber() {
            const classId = classSelect.value;
            const academicYearId = academicYearSelect.value;
            const rollNumber = rollNumberInput.value.trim();

            if (!classId || !academicYearId || !rollNumber) {
                rollNumberStatus.innerHTML = '';
                return;
            }

            // Simple client-side validation (server-side validation is still the authority)
            rollNumberStatus.innerHTML = '<small class="text-info"><i class="fas fa-spinner fa-spin"></i> Checking availability...</small>';

            // Simulate validation delay
            setTimeout(() => {
                rollNumberStatus.innerHTML = '<small class="text-muted"><i class="fas fa-info-circle"></i> Roll number will be validated on submission</small>';
            }, 500);
        }

        // Event listeners
        suggestRollBtn.addEventListener('click', suggestNextRollNumber);
        rollNumberInput.addEventListener('input', validateRollNumber);
        classSelect.addEventListener('change', () => {
            rollNumberStatus.innerHTML = '';
            if (rollNumberInput.value) {
                validateRollNumber();
            }
        });
        academicYearSelect.addEventListener('change', () => {
            rollNumberStatus.innerHTML = '';
            if (rollNumberInput.value) {
                validateRollNumber();
            }
        });
    });
</script>
@endpush
@endsection
