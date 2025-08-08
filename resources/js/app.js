import './bootstrap';

// Academic Management System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation helpers
    initializeFormValidation();

    // Initialize mark calculation helpers
    initializeMarkCalculations();

    // Initialize data tables
    initializeDataTables();
});

// Form validation helper functions
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

// Mark calculation helper functions
function initializeMarkCalculations() {
    const markInputs = document.querySelectorAll('.mark-input');
    markInputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateRowTotal(this);
            validateMarkInput(this);
        });
    });
}

// Data table helper functions
function initializeDataTables() {
    // Add search functionality
    const searchInputs = document.querySelectorAll('.data-table-search');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            filterTable(this);
        });
    });
}

// Utility functions
function calculateRowTotal(input) {
    const row = input.closest('tr');
    if (!row) return;

    const markInputs = row.querySelectorAll('.mark-input');
    let total = 0;

    markInputs.forEach(markInput => {
        const value = parseFloat(markInput.value) || 0;
        total += value;
    });

    const totalDisplay = row.querySelector('.total-display');
    if (totalDisplay) {
        totalDisplay.textContent = total.toFixed(2);
    }
}

function validateMarkInput(input) {
    const max = parseFloat(input.getAttribute('max'));
    const value = parseFloat(input.value);

    if (value > max) {
        input.classList.add('border-red-500', 'bg-red-50');
        input.setCustomValidity(`Maximum marks allowed: ${max}`);
    } else {
        input.classList.remove('border-red-500', 'bg-red-50');
        input.setCustomValidity('');
    }
}

function validateForm(form) {
    const requiredInputs = form.querySelectorAll('[required]');
    let isValid = true;

    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });

    return isValid;
}

function filterTable(searchInput) {
    const table = searchInput.closest('.data-table-container').querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    const searchTerm = searchInput.value.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
