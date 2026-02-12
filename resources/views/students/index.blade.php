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
                <button class="btn btn-primary btn-add" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Add New Student
                </button>
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
                    <div class="stat-value">{{ $students->count() ?? 0 }}</div>
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
                    <div class="stat-value">{{ $activeToday ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-card mb-4">
        <form method="GET" action="{{ route('students.create') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-lg-5">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="searchInput" 
                            name="search"
                            placeholder="Search by name, LRN, or RFID..."
                            value="{{ request('search') }}">
                        <button 
                            class="btn btn-sm btn-clear" 
                            id="clearSearch" 
                            type="button"
                            style="display: {{ request('search') ? 'block' : 'none' }};">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-2">
                    <select class="form-select filter-select" id="gradeFilter" name="grade">
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
                    <select class="form-select filter-select" id="rfidFilter" name="rfid">
                        <option value="">RFID Status</option>
                        <option value="with" {{ request('rfid') == 'with' ? 'selected' : '' }}>With RFID</option>
                        <option value="without" {{ request('rfid') == 'without' ? 'selected' : '' }}>Without RFID</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary flex-fill" id="applyFilters">
                            <i class="bi bi-funnel me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </form>
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
                                    {{ strtoupper(substr($student->name, 0, 2)) }}
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
                                <small class="text-muted">
                                    @if(request()->hasAny(['search', 'grade', 'rfid']))
                                        Try adjusting your search or filters
                                    @else
                                        Click "Add New Student" to get started
                                    @endif
                                </small>
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
                        {{ $students->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add your modals here (view, edit, delete, add student modal) -->

<style>
    /* Add all your existing styles here */
    
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

    /* Stats Cards */
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
        pointer-events: none;
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
        outline: none;
    }

    .btn-clear {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #9ca3af;
        padding: 0.25rem 0.5rem;
    }

    .btn-clear:hover {
        color: #6b7280;
        background: #f3f4f6;
        border-radius: 6px;
    }

    .filter-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Table */
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
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
        padding: 1rem;
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
        padding: 3rem 1rem;
    }

    .empty-state i {
        font-size: 4rem;
    }

    /* Pagination */
    .table-footer {
        padding: 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const resetFiltersBtn = document.getElementById('resetFilters');
        const filterForm = document.getElementById('filterForm');

        // Show/hide clear button based on input
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearSearchBtn.style.display = 'block';
            } else {
                clearSearchBtn.style.display = 'none';
            }
        });

        // Clear search
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            searchInput.focus();
        });

        // Search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });

        // Reset all filters

        // Auto-submit on filter change (optional)
        document.getElementById('gradeFilter').addEventListener('change', function() {
            // Optionally auto-submit when grade changes
            // filterForm.submit();
        });

        document.getElementById('rfidFilter').addEventListener('change', function() {
            // Optionally auto-submit when RFID filter changes
            // filterForm.submit();
        });
    });

    // View student function
    function viewStudent(studentId) {
        // Your existing viewStudent code here
    }

    // Edit student function
    function editStudent(studentId) {
        // Your existing editStudent code here
    }

    // Delete student function
    function deleteStudent(studentId) {
        // Your existing deleteStudent code here
    }
</script>

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

@endsection