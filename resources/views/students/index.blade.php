@extends('layouts.app')

@section('content')
<div class="student-form-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Card -->
            <div class="form-header-card mb-4">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">Add New Student</h2>
                        <p class="text-white-50 mb-0">Fill in the student details or use RFID scanner</p>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="form-card">
                <!-- RFID Scanner Section -->
                <div class="rfid-scanner-section mb-4">
                    <div class="scanner-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-credit-card-2-front me-2 text-primary"></i>
                                <h5 class="mb-0">RFID Scanner</h5>
                            </div>
                            <span class="badge" id="scannerStatus">
                                <i class="bi bi-circle-fill me-1"></i>Ready
                            </span>
                        </div>
                    </div>
                    <div class="scanner-body">
                        <div class="scanner-display" id="scannerDisplay">
                            <div class="scanner-icon">
                                <i class="bi bi-upc-scan"></i>
                            </div>
                            <div class="scanner-text">
                                <p class="mb-1 fw-semibold">Tap or Scan RFID Card</p>
                                <p class="text-muted small mb-0">The RFID number will be automatically filled</p>
                            </div>
                        </div>
                        <div class="scanner-success" id="scannerSuccess" style="display: none;">
                            <div class="success-animation">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold text-success">RFID Card Detected!</p>
                                <p class="text-muted small mb-0">Card number has been captured</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('students.store') }}" id="studentForm">
                    @csrf

                    <div class="row g-4">
                        <!-- Student Name -->
                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i>
                                    Student Name *
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    placeholder="Enter full name"
                                    value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- LRN -->
                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label for="lrn" class="form-label">
                                    <i class="bi bi-hash me-1"></i>
                                    LRN (Learner Reference Number) *
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control @error('lrn') is-invalid @enderror" 
                                    id="lrn" 
                                    name="lrn" 
                                    placeholder="12-digit LRN"
                                    maxlength="12"
                                    value="{{ old('lrn') }}"
                                    required>
                                @error('lrn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Format: XXXXXXXXXXXX (12 digits)</small>
                            </div>
                        </div>

                        <!-- RFID Number -->
                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label for="rfid" class="form-label">
                                    <i class="bi bi-credit-card me-1"></i>
                                    RFID Number
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        class="form-control @error('rfid') is-invalid @enderror" 
                                        id="rfid" 
                                        name="rfid" 
                                        placeholder="Scan RFID card or enter manually"
                                        value="{{ old('rfid') }}"
                                        readonly>
                                    <button 
                                        class="btn btn-outline-primary" 
                                        type="button" 
                                        id="clearRfidBtn"
                                        title="Clear RFID">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <button 
                                        class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="manualRfidBtn"
                                        title="Enter manually">
                                        <i class="bi bi-keyboard"></i>
                                    </button>
                                </div>
                                @error('rfid')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional - Use scanner or enter manually</small>
                            </div>
                        </div>

                        <!-- Grade Level -->
                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label for="grade" class="form-label">
                                    <i class="bi bi-mortarboard me-1"></i>
                                    Grade / Year Level *
                                </label>
                                <select 
                                    class="form-select @error('grade') is-invalid @enderror" 
                                    id="grade" 
                                    name="grade"
                                    required>
                                    <option value="" selected disabled>Select grade level</option>
                                    <option value="Grade 7" {{ old('grade') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                    <option value="Grade 8" {{ old('grade') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                    <option value="Grade 9" {{ old('grade') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                                    <option value="Grade 10" {{ old('grade') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                                    <option value="Grade 11" {{ old('grade') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                    <option value="Grade 12" {{ old('grade') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                </select>
                                @error('grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email (Optional) -->
                        <div class="col-md-6">
                            <div class="form-floating-custom">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>
                                    Email Address
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    placeholder="student@example.com"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional</small>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions mt-5">
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('students.create') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>
                                Add Student
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="info-card mt-4">
                <div class="info-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="info-content">
                    <strong>RFID Scanner Instructions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Click on the RFID scanner area to activate it</li>
                        <li>Tap or scan the student's RFID card</li>
                        <li>The card number will be automatically captured</li>
                        <li>You can also manually enter the RFID number using the keyboard icon</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('success') }}",
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

<style>
    .student-form-container {
        padding: 2rem 0;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header Card */
    .form-header-card {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
        padding: 2rem;
        border-radius: 16px;
        color: white;
    }

    .header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.5rem;
        font-size: 2rem;
    }

    .form-header-card h2 {
        font-weight: 700;
        font-size: 1.75rem;
    }

    /* Form Card */
    .form-card {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    /* RFID Scanner Section */
    .rfid-scanner-section {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px dashed #3b82f6;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .rfid-scanner-section:hover {
        border-color: #2563eb;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.2);
    }

    .scanner-header {
        margin-bottom: 1rem;
    }

    .scanner-header h5 {
        font-weight: 700;
        color: #1e40af;
    }

    #scannerStatus {
        background: #10b981;
        color: white;
        padding: 0.375rem 0.875rem;
        border-radius: 50px;
        font-size: 0.8125rem;
        font-weight: 600;
    }

    #scannerStatus.scanning {
        background: #f59e0b;
        animation: pulse 1.5s infinite;
    }

    #scannerStatus.success {
        background: #10b981;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    .scanner-display, .scanner-success {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .scanner-display:hover {
        background: #f8fafc;
        transform: scale(1.02);
    }

    .scanner-icon {
        font-size: 3rem;
        color: #3b82f6;
        animation: scan 2s infinite;
    }

    @keyframes scan {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .scanner-success .success-animation {
        font-size: 3rem;
        color: #10b981;
        animation: checkmark 0.5s ease;
    }

    @keyframes checkmark {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    /* Form Styling */
    .form-floating-custom {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .form-label i {
        color: #3b82f6;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    .form-control.is-invalid:focus, .form-select.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .input-group .btn {
        border: 2px solid #e5e7eb;
        border-left: none;
        transition: all 0.3s ease;
    }

    .input-group .btn:hover {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .form-text {
        font-size: 0.8125rem;
        margin-top: 0.375rem;
    }

    /* Form Actions */
    .form-actions {
        padding-top: 2rem;
        border-top: 2px solid #f3f4f6;
    }

    .btn-lg {
        padding: 0.875rem 2rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }

    .btn-outline-secondary {
        border: 2px solid #e5e7eb;
        color: #6b7280;
    }

    .btn-outline-secondary:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #374151;
    }

    /* Info Card */
    .info-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-left: 4px solid #f59e0b;
        padding: 1.5rem;
        border-radius: 12px;
        display: flex;
        gap: 1rem;
    }

    .info-icon {
        font-size: 1.5rem;
        color: #d97706;
        flex-shrink: 0;
    }

    .info-content {
        color: #78350f;
    }

    .info-content ul {
        padding-left: 1.25rem;
    }

    .info-content li {
        margin-bottom: 0.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }

        .scanner-display {
            flex-direction: column;
            text-align: center;
        }

        .form-actions .d-flex {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rfidInput = document.getElementById('rfid');
        const scannerDisplay = document.getElementById('scannerDisplay');
        const scannerSuccess = document.getElementById('scannerSuccess');
        const scannerStatus = document.getElementById('scannerStatus');
        const clearRfidBtn = document.getElementById('clearRfidBtn');
        const manualRfidBtn = document.getElementById('manualRfidBtn');
        const studentForm = document.getElementById('studentForm');

        let scannerActive = false;
        let rfidBuffer = '';
        let scanTimeout = null;

        // Activate scanner when clicking on scanner display
        scannerDisplay.addEventListener('click', function() {
            activateScanner();
        });

        function activateScanner() {
            scannerActive = true;
            scannerStatus.classList.add('scanning');
            scannerStatus.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Scanning...';
            rfidBuffer = '';
            
            // Focus on a hidden element to capture keystrokes
            document.body.focus();
        }

        // Listen for RFID card scan (simulated by keyboard input)
        document.addEventListener('keypress', function(e) {
            if (!scannerActive) return;

            // Clear previous timeout
            if (scanTimeout) {
                clearTimeout(scanTimeout);
            }

            // RFID scanners typically send characters quickly followed by Enter
            if (e.key === 'Enter') {
                if (rfidBuffer.length > 0) {
                    processRfidScan(rfidBuffer.trim());
                    rfidBuffer = '';
                }
            } else {
                rfidBuffer += e.key;
                
                // Auto-submit after 100ms of no input (typical RFID scan speed)
                scanTimeout = setTimeout(function() {
                    if (rfidBuffer.length > 0) {
                        processRfidScan(rfidBuffer.trim());
                        rfidBuffer = '';
                    }
                }, 100);
            }
        });

        function processRfidScan(rfidNumber) {
            if (rfidNumber.length < 4) return; // Ignore very short inputs

            rfidInput.value = rfidNumber;
            scannerActive = false;
            
            // Show success animation
            scannerDisplay.style.display = 'none';
            scannerSuccess.style.display = 'flex';
            scannerStatus.classList.remove('scanning');
            scannerStatus.classList.add('success');
            scannerStatus.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Captured';

            // Play success sound (optional)
            playBeep();

            // Reset after 3 seconds
            setTimeout(function() {
                scannerSuccess.style.display = 'none';
                scannerDisplay.style.display = 'flex';
                scannerStatus.classList.remove('success');
                scannerStatus.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Ready';
            }, 3000);
        }

        // Clear RFID button
        clearRfidBtn.addEventListener('click', function() {
            rfidInput.value = '';
            rfidBuffer = '';
            scannerSuccess.style.display = 'none';
            scannerDisplay.style.display = 'flex';
            scannerStatus.classList.remove('success');
            scannerStatus.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Ready';
        });

        // Manual RFID entry button
        manualRfidBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Enter RFID Number',
                input: 'text',
                inputLabel: 'RFID Card Number',
                inputPlaceholder: 'Enter the RFID number manually',
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3b82f6',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please enter an RFID number';
                    }
                    if (value.length < 4) {
                        return 'RFID number is too short';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processRfidScan(result.value);
                }
            });
        });

        // Form validation
        studentForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const lrn = document.getElementById('lrn').value.trim();
            const grade = document.getElementById('grade').value;

            if (!name || !lrn || !grade) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Required Fields',
                    text: 'Please fill in all required fields (Name, LRN, and Grade)',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding Student...';
        });

        // LRN formatting (accept only numbers)
        document.getElementById('lrn').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Optional: Beep sound for successful scan
        function playBeep() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        }

        // Auto-focus on name field on page load
        document.getElementById('name').focus();
    });

    // =======================
// SWEETALERT FORM SUBMIT
// =======================

studentForm.addEventListener('submit', function(e) {

    e.preventDefault(); // Stop normal submit

    const name = document.getElementById('name').value.trim();
    const lrn = document.getElementById('lrn').value.trim();
    const grade = document.getElementById('grade').value;

    // Basic validation
    if (!name || !lrn || !grade) {

        Swal.fire({
            icon: 'error',
            title: 'Missing Required Fields',
            text: 'Please fill in Name, LRN, and Grade.',
            confirmButtonColor: '#3b82f6'
        });

        return;
    }

    // Confirm before submit
    Swal.fire({
        title: 'Add Student?',
        html: `
            <p>Please confirm student details:</p>
            <strong>${name}</strong><br>
            LRN: ${lrn}<br>
            Grade: ${grade}
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3b82f6'
    }).then((result) => {

        if (result.isConfirmed) {

            const submitBtn = document.getElementById('submitBtn');

            // Loading alert
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            // Submit form
            studentForm.submit();
        }

    });

});

</script>
@endsection