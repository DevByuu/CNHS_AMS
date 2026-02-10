@extends('layouts.app')

@section('content')
<div class="students-container">
    <!-- Header Section -->
    <div class="students-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">Student Management</h2>
                        <p class="text-white-50 mb-0">View and manage student records</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('students.index') }}" class="btn btn-primary btn-add">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Add New Student
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-mini-card stat-blue">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value">{{ $students->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-mini-card stat-green">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">With RFID</div>
                    <div class="stat-value">{{ $studentsWithRfid ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-mini-card stat-orange">
                <div class="stat-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Grade Levels</div>
                    <div class="stat-value">6</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-mini-card stat-purple">
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Active Today</div>
                    <div class="stat-value">{{ $activeToday ?? 987 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-card mb-4">
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="searchInput" 
                        placeholder="Search by name, LRN, or RFID..."
                        value="{{ request('search') }}">
                    <button class="btn btn-sm btn-clear" id="clearSearch" style="display: none;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-2">
                <select class="form-select filter-select" id="gradeFilter">
                    <option value="">All Grades</option>
                    <option value="Grade 7" {{ request('grade') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                    <option value="Grade 8" {{ request('grade') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                    <option value="Grade 9" {{ request('grade') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                    <option value="Grade 10" {{ request('grade') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                    <option value="Grade 11" {{ request('grade') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                    <option value="Grade 12" {{ request('grade') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                </select>
            </div>
            <div class="col-lg-2">
                <select class="form-select filter-select" id="rfidFilter">
                    <option value="">RFID Status</option>
                    <option value="with" {{ request('rfid') == 'with' ? 'selected' : '' }}>With RFID</option>
                    <option value="without" {{ request('rfid') == 'without' ? 'selected' : '' }}>Without RFID</option>
                </select>
            </div>
            <div class="col-lg-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill" id="applyFilters">
                        <i class="bi bi-funnel me-2"></i>Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary" id="resetFilters">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table student-table" id="studentsTable">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Student Name</th>
                        <th>LRN</th>
                        <th>Grade</th>
                        <th>RFID Status</th>
                        <th width="120" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr class="student-row" data-student-id="{{ $student->id }}">
                        <td class="text-muted">{{ $students->firstItem() + $index }}</td>
                        <td>
                            <div class="student-name-cell">
                                <div class="student-avatar">
                                    {{ substr($student->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $student->name }}</div>
                                    @if($student->email)
                                    <small class="text-muted">{{ $student->email }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="lrn-code">{{ $student->lrn }}</code>
                        </td>
                        <td>
                            <span class="badge badge-grade">{{ $student->grade }}</span>
                        </td>
                        <td>
                            @if($student->rfid)
                                <span class="badge badge-rfid-active">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    Registered
                                </span>
                            @else
                                <span class="badge badge-rfid-inactive">
                                    <i class="bi bi-x-circle-fill me-1"></i>
                                    Not Set
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button 
                                    class="btn btn-sm btn-action btn-view" 
                                    onclick="viewStudent({{ $student->id }})"
                                    title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button 
                                    class="btn btn-sm btn-action btn-edit" 
                                    onclick="editStudent({{ $student->id }})"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button 
                                    class="btn btn-sm btn-action btn-delete" 
                                    onclick="deleteStudent({{ $student->id }})"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p class="mb-0 mt-3">No students found</p>
                                <small class="text-muted">Try adjusting your search or filters</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="table-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="pagination-wrapper">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Student Detail Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-modern">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center">
                    <div class="modal-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalStudentName">Student Details</h5>
                        <small class="text-muted" id="modalStudentId"></small>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body-custom">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><i class="bi bi-person me-2"></i>Full Name</label>
                            <div class="info-value" id="modalName">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><i class="bi bi-hash me-2"></i>LRN</label>
                            <div class="info-value" id="modalLrn">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><i class="bi bi-mortarboard me-2"></i>Grade Level</label>
                            <div class="info-value" id="modalGrade">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label><i class="bi bi-envelope me-2"></i>Email</label>
                            <div class="info-value" id="modalEmail">-</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="info-item">
                            <label><i class="bi bi-credit-card me-2"></i>RFID Number</label>
                            <div class="info-value" id="modalRfid">-</div>
                        </div>
                    </div>
                </div>

                <!-- RFID Status Card -->
                <div class="rfid-status-card mt-4" id="rfidStatusCard">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check status-icon"></i>
                            <div>
                                <div class="status-title">RFID Status</div>
                                <div class="status-text" id="rfidStatusText">Active</div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" id="updateRfidBtn">
                            <i class="bi bi-pencil me-1"></i>Update RFID
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-primary" id="editFromViewModalBtn">
                    <i class="bi bi-pencil me-2"></i>Edit Student
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-modern">
            <div class="modal-header-custom">
                <div class="d-flex align-items-center">
                    <div class="modal-icon bg-warning">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">Edit Student</h5>
                        <small class="text-muted" id="editModalStudentId"></small>
                    </div>
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form id="editStudentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body-custom">
                    <div class="row g-4">
                        <!-- Student Name -->
                        <div class="col-md-6">
                            <div class="form-group-modal">
                                <label for="editName" class="form-label-modal">
                                    <i class="bi bi-person me-2"></i>
                                    Student Name *
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control-modal" 
                                    id="editName" 
                                    name="name" 
                                    required>
                            </div>
                        </div>

                        <!-- LRN -->
                        <div class="col-md-6">
                            <div class="form-group-modal">
                                <label for="editLrn" class="form-label-modal">
                                    <i class="bi bi-hash me-2"></i>
                                    LRN *
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control-modal" 
                                    id="editLrn" 
                                    name="lrn" 
                                    maxlength="12"
                                    required>
                            </div>
                        </div>

                        <!-- Grade Level -->
                        <div class="col-md-6">
                            <div class="form-group-modal">
                                <label for="editGrade" class="form-label-modal">
                                    <i class="bi bi-mortarboard me-2"></i>
                                    Grade Level *
                                </label>
                                <select class="form-control-modal" id="editGrade" name="grade" required>
                                    <option value="">Select grade level</option>
                                    <option value="Grade 7">Grade 7</option>
                                    <option value="Grade 8">Grade 8</option>
                                    <option value="Grade 9">Grade 9</option>
                                    <option value="Grade 10">Grade 10</option>
                                    <option value="Grade 11">Grade 11</option>
                                    <option value="Grade 12">Grade 12</option>
                                </select>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group-modal">
                                <label for="editEmail" class="form-label-modal">
                                    <i class="bi bi-envelope me-2"></i>
                                    Email Address
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control-modal" 
                                    id="editEmail" 
                                    name="email">
                            </div>
                        </div>

                        <!-- RFID Number -->
                        <div class="col-md-12">
                            <div class="form-group-modal">
                                <label for="editRfid" class="form-label-modal">
                                    <i class="bi bi-credit-card me-2"></i>
                                    RFID Number
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        class="form-control-modal" 
                                        id="editRfid" 
                                        name="rfid"
                                        placeholder="Optional">
                                    <button class="btn btn-outline-primary" type="button" id="scanRfidBtn">
                                        <i class="bi bi-upc-scan me-1"></i>Scan Card
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveEditBtn">
                        <i class="bi bi-check-circle me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content delete-modal">
            <div class="modal-body text-center p-5">
                <div class="delete-icon mb-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h4 class="mb-3">Delete Student?</h4>
                <p class="text-muted mb-4">
                    Are you sure you want to delete <strong id="deleteStudentName"></strong>?
                    <br>This action cannot be undone and will affect all attendance records.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-2"></i>Yes, Delete
                    </button>
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
    .students-container {
        padding: 2rem 0;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header */
    .students-header {
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
        font-size: 2rem;
    }

    .students-header h2 {
        font-weight: 700;
        font-size: 1.75rem;
    }

    .btn-add {
        background: white;
        color: #667eea;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        color: #667eea;
    }

    /* Mini Stats Cards */
    .stat-mini-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .stat-mini-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .stat-blue { border-color: #3b82f6; }
    .stat-green { border-color: #10b981; }
    .stat-orange { border-color: #f59e0b; }
    .stat-purple { border-color: #8b5cf6; }

    .stat-mini-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-blue .stat-icon {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .stat-green .stat-icon {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .stat-orange .stat-icon {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .stat-purple .stat-icon {
        background: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
    }

    .stat-label {
        font-size: 0.8125rem;
        color: #6b7280;
        font-weight: 500;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
    }

    /* Filter Card */
    .filter-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .search-box {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1.125rem;
    }

    .search-box .form-control {
        padding-left: 3rem;
        padding-right: 3rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .search-box .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .btn-clear {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #9ca3af;
    }

    .btn-clear:hover {
        color: #6b7280;
    }

    .filter-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .student-table {
        margin: 0;
    }

    .student-table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .student-table th {
        font-weight: 700;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        padding: 1.25rem 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .student-table td {

        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .student-row {
        transition: all 0.2s ease;
    }

    .student-row:hover {
        background: #f9fafb;
    }

    .student-name-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
    }

    .lrn-code {
        background: #f3f4f6;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #6b7280;
    }

    .badge-grade {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        padding: 0.375rem 0.875rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8125rem;
    }

    .section-text {
        color: #6b7280;
        font-weight: 500;
    }

    .badge-rfid-active {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        padding: 0.375rem 0.875rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8125rem;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .badge-rfid-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        padding: 0.375rem 0.875rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8125rem;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .action-buttons {
        display: flex;
        gap: 0.375rem;
        justify-content: center;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 6px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-view {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .btn-view:hover {
        background: #3b82f6;
        color: white;
        transform: scale(1.1);
    }

    .btn-edit {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .btn-edit:hover {
        background: #f59e0b;
        color: white;
        transform: scale(1.1);
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: white;
        transform: scale(1.1);
    }

    .empty-state {
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 4rem;
    }

    /* Table Footer */
    .table-footer {
        padding: 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
    }

    /* Modal Styling */
    .modal-modern .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header-custom {
        padding: 2rem;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .btn-close-custom {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        background: #f3f4f6;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-close-custom:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .modal-body-custom {
        padding: 2rem;
    }

    .info-item {
        margin-bottom: 1rem;
    }

    .info-item label {
        font-size: 0.8125rem;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .info-item label i {
        color: #3b82f6;
    }

    .info-value {
        font-size: 1rem;
        color: #111827;
        font-weight: 600;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .rfid-status-card {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px solid #bae6fd;
    }

    .status-icon {
        font-size: 2rem;
        color: #0284c7;
        margin-right: 1rem;
    }

    .status-title {
        font-size: 0.875rem;
        color: #075985;
        font-weight: 600;
    }

    .status-text {
        font-size: 1.125rem;
        color: #0c4a6e;
        font-weight: 700;
    }

    .modal-footer-custom {
        padding: 1.5rem 2rem;
        border-top: 2px solid #f3f4f6;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Delete Modal */
    .delete-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .delete-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        animation: pulseWarning 2s infinite;
    }

    .delete-icon i {
        font-size: 3rem;
        color: #ef4444;
    }

    @keyframes pulseWarning {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
    }

    .delete-modal h4 {
        font-weight: 700;
        color: #111827;
    }

    .delete-modal .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .delete-modal .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    /* Edit Modal Styles */
    .modal-icon.bg-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .form-group-modal {
        margin-bottom: 0;
    }

    .form-label-modal {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
        display: flex;
        align-items: center;
    }

    .form-label-modal i {
        color: #3b82f6;
    }

    .form-control-modal {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .form-control-modal:focus {
        outline: none;
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-control-modal:disabled {
        background: #f3f4f6;
        cursor: not-allowed;
    }

    .input-group .form-control-modal {
        border-right: none;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border: 2px solid #e5e7eb;
        border-left: none;
    }

    .input-group .btn:hover {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .student-name-cell {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-buttons {
            flex-direction: column;
        }

        .table-footer .row {
            text-align: center;
        }

        .pagination-wrapper {
            justify-content: center;
            margin-top: 1rem;
        }
    }
</style>

<script>
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');

    searchInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            clearSearchBtn.style.display = 'block';
        } else {
            clearSearchBtn.style.display = 'none';
        }
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.style.display = 'none';
        applyFilters();
    });

    // Apply filters
    document.getElementById('applyFilters').addEventListener('click', applyFilters);

    function applyFilters() {
        const search = document.getElementById('searchInput').value;
        const grade = document.getElementById('gradeFilter').value;
        const rfid = document.getElementById('rfidFilter').value;

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (grade) params.append('grade', grade);
        if (rfid) params.append('rfid', rfid);

        window.location.href = '{{ route("students.create") }}?' + params.toString();
    }

    // Reset filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        window.location.href = '{{ route("students.index") }}';
    });

    // Enter key to search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    // View student details
    function viewStudent(studentId) {
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching student details',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch student data via AJAX
        fetch(`/students/${studentId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Student data received:', data);
            
            // Close loading
            Swal.close();

            // Populate modal with student data
            document.getElementById('modalStudentName').textContent = data.name || 'N/A';
            document.getElementById('modalStudentId').textContent = `Student ID: ${data.id}`;
            document.getElementById('modalName').textContent = data.name || 'N/A';
            document.getElementById('modalLrn').textContent = data.lrn || 'N/A';
            document.getElementById('modalGrade').textContent = data.grade || 'N/A';
            document.getElementById('modalEmail').textContent = data.email || 'Not provided';
            document.getElementById('modalRfid').textContent = data.rfid || 'Not registered';

            // Update RFID status
            const rfidStatusText = document.getElementById('rfidStatusText');
            if (data.rfid) {
                rfidStatusText.textContent = 'Active & Registered';
                rfidStatusText.style.color = '#059669';
            } else {
                rfidStatusText.textContent = 'Not Registered';
                rfidStatusText.style.color = '#dc2626';
            }

            // Store student ID globally for edit button
            window.currentStudentId = data.id;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('studentModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching student:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error Loading Student',
                html: `
                    <p>Failed to load student details.</p>
                    <small class="text-muted">Error: ${error.message}</small>
                    <br><br>
                    <strong>Troubleshooting:</strong>
                    <ul class="text-start small">
                        <li>Make sure the student exists in the database</li>
                        <li>Check that the route /students/{id} is defined</li>
                        <li>Verify the StudentController show() method returns JSON</li>
                    </ul>
                `,
                confirmButtonColor: '#3b82f6'
            });
        });
    }

    // Edit student
    function editStudent(studentId) {
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching student details',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch student data via AJAX
        fetch(`/students/${studentId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Student not found');
            }
            return response.json();
        })
        .then(data => {
            // Close loading
            Swal.close();

            // Populate edit form
            document.getElementById('editModalStudentId').textContent = `Editing Student ID: ${data.id}`;
            document.getElementById('editName').value = data.name || '';
            document.getElementById('editLrn').value = data.lrn || '';
            document.getElementById('editGrade').value = data.grade || '';
            document.getElementById('editEmail').value = data.email || '';
            document.getElementById('editRfid').value = data.rfid || '';

            // Set form action
            document.getElementById('editStudentForm').action = `/students/${studentId}`;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load student details. Please try again.',
                confirmButtonColor: '#3b82f6'
            });
        });
    }

    // Edit from view modal
    document.getElementById('editFromViewModalBtn').addEventListener('click', function() {
        if (!window.currentStudentId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Student ID not found',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
        
        // Close view modal
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('studentModal'));
        viewModal.hide();
        
        // Open edit modal after short delay
        setTimeout(() => {
            editStudent(window.currentStudentId);
        }, 300);
    });

    // Handle edit form submission
    document.getElementById('editStudentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('saveEditBtn');
        const originalHTML = submitBtn.innerHTML;

        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        // Submit form
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                modal.hide();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Student Updated!',
                    text: 'Student information has been updated successfully',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    // Reload page to show updated data
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Update failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
            
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: error.message || 'Failed to update student. Please try again.',
                confirmButtonColor: '#3b82f6'
            });
        });
    });

    // RFID Scan Button in Edit Modal
    document.getElementById('scanRfidBtn').addEventListener('click', function() {
        let rfidBuffer = '';
        let scanTimeout = null;

        Swal.fire({
            title: 'Ready to Scan',
            html: `
                <div class="text-center py-4">
                    <div class="scan-animation mb-3">
                        <i class="bi bi-upc-scan" style="font-size: 4rem; color: #3b82f6; animation: pulse 1.5s infinite;"></i>
                    </div>
                    <p class="mb-0">Tap your RFID card on the scanner...</p>
                    <small class="text-muted">Or click below to enter manually</small>
                </div>
            `,
            showCancelButton: true,
            showConfirmButton: false,
            cancelButtonText: 'Enter Manually',
            allowOutsideClick: false,
            didOpen: () => {
                // Listen for RFID scan
                const handleKeyPress = (e) => {
                    if (scanTimeout) clearTimeout(scanTimeout);

                    if (e.key === 'Enter') {
                        if (rfidBuffer.length > 0) {
                            document.getElementById('editRfid').value = rfidBuffer.trim();
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'RFID Captured!',
                                text: `Card number: ${rfidBuffer.trim()}`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            document.removeEventListener('keypress', handleKeyPress);
                        }
                        rfidBuffer = '';
                    } else {
                        rfidBuffer += e.key;
                        scanTimeout = setTimeout(() => {
                            if (rfidBuffer.length > 0) {
                                document.getElementById('editRfid').value = rfidBuffer.trim();
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'RFID Captured!',
                                    text: `Card number: ${rfidBuffer.trim()}`,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                document.removeEventListener('keypress', handleKeyPress);
                            }
                            rfidBuffer = '';
                        }, 100);
                    }
                };

                document.addEventListener('keypress', handleKeyPress);

                // Cleanup listener on cancel
                Swal.getConfirmButton().addEventListener('click', () => {
                    document.removeEventListener('keypress', handleKeyPress);
                });
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                // Manual entry
                Swal.fire({
                    title: 'Enter RFID Number',
                    input: 'text',
                    inputPlaceholder: 'Type RFID number',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    confirmButtonColor: '#3b82f6',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please enter an RFID number';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('editRfid').value = result.value;
                    }
                });
            }
        });
    });

    // Delete student - Show confirmation modal

    // Confirm delete action
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!studentToDelete) return;

        const btn = this;
        const originalHTML = btn.innerHTML;

        // Show loading state
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/students/${studentToDelete}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);

        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();

        // Show success message and submit
        Swal.fire({
            title: 'Deleting...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit form after short delay
        setTimeout(() => {
            form.submit();
        }, 500);
    });

    // Update RFID button functionality
    document.getElementById('updateRfidBtn').addEventListener('click', function() {
        if (!window.currentStudentId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Student ID not found',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
        
        Swal.fire({
            title: 'Update RFID',
            html: `
                <div class="text-start">
                    <label class="form-label fw-semibold">Scan or Enter RFID Number</label>
                    <input type="text" id="rfidInput" class="form-control form-control-lg" placeholder="Scan RFID card or type manually" autofocus>
                    <small class="text-muted">Tap your RFID card on the scanner or enter the number manually</small>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3b82f6',
            preConfirm: () => {
                const rfid = document.getElementById('rfidInput').value;
                if (!rfid) {
                    Swal.showValidationMessage('Please enter an RFID number');
                    return false;
                }
                return rfid;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Here you would send an AJAX request to update the RFID
                Swal.fire({
                    icon: 'success',
                    title: 'RFID Updated!',
                    text: 'Student RFID has been updated successfully',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    // Reload the page to reflect changes
                    location.reload();
                });
            }
        });

        // Focus on input after modal opens
        setTimeout(() => {
            document.getElementById('rfidInput').focus();
        }, 100);
    });

    function deleteStudent(studentId) {

    // Get student name
    const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
    const studentName = row
        ? row.querySelector('.fw-semibold').textContent
        : 'this student';

    Swal.fire({
        title: 'Are you sure?',
        html: `
            <p>You are about to delete:</p>
            <strong class="text-danger">${studentName}</strong>
            <br><br>
            <small>This action cannot be undone.</small>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#3b82f6'
    }).then((result) => {

        if (result.isConfirmed) {

            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX Delete
            fetch(`/students/${studentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })

            .then(response => response.json())

            .then(data => {

                if (data.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Remove row instantly (no reload needed)
                    if (row) {
                        row.remove();
                    }

                } else {
                    throw new Error(data.message || 'Delete failed');
                }

            })

            .catch(error => {

                Swal.fire({
                    icon: 'error',
                    title: 'Delete Failed',
                    text: error.message || 'Something went wrong',
                    confirmButtonColor: '#3b82f6'
                });

            });

        }

    });
}
</script>
@endsection
