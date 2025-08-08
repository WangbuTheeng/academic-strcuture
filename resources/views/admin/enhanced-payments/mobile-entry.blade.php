@extends('layouts.admin')

@section('title', 'Mobile Payment Entry')

@section('content')
<div class="container-fluid">
    <!-- Mobile-Optimized Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">
                <i class="fas fa-mobile-alt text-primary me-2"></i>Quick Payment
            </h1>
            <p class="text-muted mb-0 small">Mobile-optimized payment entry</p>
        </div>
        <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-list me-1"></i>All Payments
        </a>
    </div>

    <!-- Payment Entry Card -->
    <div class="card shadow-sm">
        <div class="card-body p-3">
            <form id="mobilePaymentForm">
                @csrf
                
                <!-- Student Search -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-user me-1"></i>Student
                    </label>
                    <div class="input-group">
                        <input type="text" id="studentSearch" class="form-control" 
                               placeholder="Search by name or admission number..." autocomplete="off">
                        <button type="button" class="btn btn-outline-secondary" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="studentResults" class="list-group mt-2" style="display: none;"></div>
                    <input type="hidden" id="selectedStudentId" name="student_id">
                </div>

                <!-- Selected Student Info -->
                <div id="selectedStudentInfo" class="alert alert-info" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="selectedStudentName"></strong>
                            <br><small id="selectedStudentDetails"></small>
                        </div>
                        <div class="text-end">
                            <div class="text-danger fw-bold" id="selectedStudentOutstanding"></div>
                            <small class="text-muted">Outstanding</small>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div id="paymentDetails" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" name="amount" id="paymentAmount" 
                                       class="form-control" step="0.01" min="0.01" 
                                       placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select name="payment_method" id="paymentMethod" class="form-select" required>
                                <option value="">Select Method</option>
                                @foreach($paymentMethods as $key => $method)
                                    <option value="{{ $key }}">{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Payment Date</label>
                            <input type="date" name="payment_date" id="paymentDate" 
                                   class="form-control" value="{{ date('Y-m-d') }}" 
                                   max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" id="referenceNumber" 
                                   class="form-control" placeholder="Optional">
                        </div>
                    </div>

                    <!-- Additional Fields for Specific Payment Methods -->
                    <div id="bankFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" 
                                   placeholder="Enter bank name">
                        </div>
                    </div>

                    <div id="chequeFields" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Cheque Number</label>
                                <input type="text" name="cheque_number" class="form-control" 
                                       placeholder="Cheque number">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Cheque Date</label>
                                <input type="date" name="cheque_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" 
                                  placeholder="Optional remarks..."></textarea>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Quick Amounts</label>
                        <div class="d-flex flex-wrap gap-2" id="quickAmountButtons">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitPayment">
                            <i class="fas fa-credit-card me-2"></i>Process Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="card shadow-sm mt-3">
        <div class="card-header py-2">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-history me-1"></i>Recent Payments
            </h6>
        </div>
        <div class="card-body p-0">
            <div id="recentPayments" class="list-group list-group-flush">
                <!-- Will be populated dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>Processing payment...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Mobile-optimized styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0.5rem;
        }
        
        .card {
            border-radius: 0.5rem;
        }
        
        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
        }
        
        .form-control, .form-select {
            font-size: 16px; /* Prevents zoom on iOS */
        }
        
        .input-group-text {
            font-weight: bold;
        }
    }
    
    .student-result-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .student-result-item:hover {
        background-color: #f8f9fa;
    }
    
    .quick-amount-btn {
        min-width: 80px;
    }
    
    .payment-item {
        border-left: 4px solid #28a745;
        transition: all 0.2s;
    }
    
    .payment-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }
</style>
@endpush

@push('scripts')
<script>
    let searchTimeout;
    let selectedStudent = null;

    document.addEventListener('DOMContentLoaded', function() {
        initializePaymentEntry();
        loadRecentPayments();
    });

    function initializePaymentEntry() {
        const studentSearch = document.getElementById('studentSearch');
        const studentResults = document.getElementById('studentResults');
        const paymentMethod = document.getElementById('paymentMethod');

        // Student search functionality
        studentSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                studentResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                searchStudents(query);
            }, 300);
        });

        // Payment method change handler
        paymentMethod.addEventListener('change', function() {
            togglePaymentMethodFields(this.value);
        });

        // Clear search
        document.getElementById('clearSearch').addEventListener('click', function() {
            clearStudentSelection();
        });

        // Form submission
        document.getElementById('mobilePaymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            processPayment();
        });
    }

    function searchStudents(query) {
        fetch(`{{ route('admin.enhanced-payments.search-students') }}?search=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(students => {
                displayStudentResults(students);
            })
            .catch(error => {
                console.error('Error searching students:', error);
            });
    }

    function displayStudentResults(students) {
        const resultsContainer = document.getElementById('studentResults');
        
        if (students.length === 0) {
            resultsContainer.innerHTML = '<div class="list-group-item text-muted">No students found</div>';
            resultsContainer.style.display = 'block';
            return;
        }

        const resultsHtml = students.map(student => `
            <div class="list-group-item student-result-item" onclick="selectStudent(${student.id}, '${student.name}', '${student.admission_number}', '${student.class || ''}', ${student.total_outstanding}, '${student.formatted_outstanding}', ${student.pending_bills_count})">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${student.name}</strong>
                        <br><small class="text-muted">${student.admission_number} ${student.class ? '• ' + student.class : ''}</small>
                    </div>
                    <div class="text-end">
                        <div class="text-danger fw-bold">${student.formatted_outstanding}</div>
                        <small class="text-muted">${student.pending_bills_count} bills</small>
                    </div>
                </div>
            </div>
        `).join('');

        resultsContainer.innerHTML = resultsHtml;
        resultsContainer.style.display = 'block';
    }

    function selectStudent(id, name, admissionNumber, className, totalOutstanding, formattedOutstanding, pendingBillsCount) {
        selectedStudent = {
            id: id,
            name: name,
            admission_number: admissionNumber,
            class: className,
            total_outstanding: totalOutstanding,
            formatted_outstanding: formattedOutstanding,
            pending_bills_count: pendingBillsCount
        };

        // Update UI
        document.getElementById('selectedStudentId').value = id;
        document.getElementById('studentSearch').value = name;
        document.getElementById('selectedStudentName').textContent = name;
        document.getElementById('selectedStudentDetails').textContent = `${admissionNumber} ${className ? '• ' + className : ''} • ${pendingBillsCount} pending bills`;
        document.getElementById('selectedStudentOutstanding').textContent = formattedOutstanding;

        // Show selected student info and payment details
        document.getElementById('selectedStudentInfo').style.display = 'block';
        document.getElementById('paymentDetails').style.display = 'block';
        document.getElementById('studentResults').style.display = 'none';

        // Generate quick amount buttons
        generateQuickAmountButtons(totalOutstanding);

        // Load student payment history
        loadStudentPaymentHistory(id);
    }

    function clearStudentSelection() {
        selectedStudent = null;
        document.getElementById('selectedStudentId').value = '';
        document.getElementById('studentSearch').value = '';
        document.getElementById('selectedStudentInfo').style.display = 'none';
        document.getElementById('paymentDetails').style.display = 'none';
        document.getElementById('studentResults').style.display = 'none';
        document.getElementById('mobilePaymentForm').reset();
        document.getElementById('paymentDate').value = '{{ date("Y-m-d") }}';
    }

    function generateQuickAmountButtons(totalOutstanding) {
        const container = document.getElementById('quickAmountButtons');
        const amounts = [500, 1000, 2000, 5000];
        
        // Add total outstanding as an option if it's reasonable
        if (totalOutstanding > 0 && totalOutstanding <= 50000) {
            amounts.push(totalOutstanding);
        }

        const buttonsHtml = amounts.map(amount => `
            <button type="button" class="btn btn-outline-primary btn-sm quick-amount-btn" 
                    onclick="setPaymentAmount(${amount})">
                Rs. ${amount.toLocaleString()}
            </button>
        `).join('');

        container.innerHTML = buttonsHtml;
    }

    function setPaymentAmount(amount) {
        document.getElementById('paymentAmount').value = amount;
    }

    function togglePaymentMethodFields(method) {
        document.getElementById('bankFields').style.display = 
            ['bank_transfer', 'online'].includes(method) ? 'block' : 'none';
        document.getElementById('chequeFields').style.display = 
            method === 'cheque' ? 'block' : 'none';
    }

    function processPayment() {
        if (!selectedStudent) {
            alert('Please select a student first.');
            return;
        }

        const formData = new FormData(document.getElementById('mobilePaymentForm'));
        
        // Show loading modal
        const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        loadingModal.show();

        fetch('{{ route("admin.fees.enhanced-payments.process-mobile-payment") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            
            if (data.success) {
                // Show success message
                showAlert('Payment processed successfully!', 'success');
                
                // Reset form
                clearStudentSelection();
                
                // Reload recent payments
                loadRecentPayments();
            } else {
                showAlert(data.message || 'Error processing payment', 'danger');
            }
        })
        .catch(error => {
            loadingModal.hide();
            console.error('Error:', error);
            showAlert('Error processing payment. Please try again.', 'danger');
        });
    }

    function loadStudentPaymentHistory(studentId) {
        fetch(`{{ route('admin.enhanced-payments.get-student-payment-history') }}?student_id=${studentId}`)
            .then(response => response.json())
            .then(payments => {
                // Update recent payments with student's history
                displayRecentPayments(payments);
            })
            .catch(error => {
                console.error('Error loading payment history:', error);
            });
    }

    function loadRecentPayments() {
        // Load general recent payments
        fetch('{{ route("admin.fees.payments.index") }}?format=json&limit=5')
            .then(response => response.json())
            .then(payments => {
                displayRecentPayments(payments);
            })
            .catch(error => {
                console.error('Error loading recent payments:', error);
            });
    }

    function displayRecentPayments(payments) {
        const container = document.getElementById('recentPayments');
        
        if (!payments || payments.length === 0) {
            container.innerHTML = '<div class="list-group-item text-muted text-center py-3">No recent payments</div>';
            return;
        }

        const paymentsHtml = payments.map(payment => `
            <div class="list-group-item payment-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${payment.student?.full_name || 'N/A'}</strong>
                        <br><small class="text-muted">${payment.payment_number} • ${payment.payment_date}</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-bold">${payment.formatted_amount}</div>
                        <small class="text-muted">${payment.payment_method}</small>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = paymentsHtml;
    }

    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert at the top of the container
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
</script>
@endpush
