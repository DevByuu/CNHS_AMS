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
                    <div class="d-flex gap-2 justify-content-md-end">
                        <button class="btn btn-success btn-upload" id="uploadCsvBtn">
                            <i class="bi bi-upload me-2"></i>Import CSV
                        </button>
                        <button class="btn btn-outline-light btn-download" id="downloadTemplateBtn">
                            <i class="bi bi-download me-2"></i>Download Template
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-mini-card stat-blue">
                    <div class="stat-icon"><i class="bi bi-people"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Students</div>
                        <div class="stat-value">{{ $students->total() ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-mini-card stat-green">
                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">With RFID</div>
                        <div class="stat-value">{{ $studentsWithRfid ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-mini-card stat-orange">
                    <div class="stat-icon"><i class="bi bi-mortarboard"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Grade Levels</div>
                        <div class="stat-value">6</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-mini-card stat-purple">
                    <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Active Today</div>
                        <div class="stat-value">{{ $activeToday ?? 987 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card mb-4">
            <div class="row g-3">
                <div class="col-lg-5">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput"
                            placeholder="Search by name, LRN, or RFID..." value="{{ request('search') }}">
                        <button class="btn btn-sm btn-clear" id="clearSearch" style="display:none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-2">
                    <select class="form-select filter-select" id="gradeFilter">
                        <option value="">All Grades</option>
                        <option value="Grade 7"  {{ request('grade') == 'Grade 7'  ? 'selected' : '' }}>Grade 7</option>
                        <option value="Grade 8"  {{ request('grade') == 'Grade 8'  ? 'selected' : '' }}>Grade 8</option>
                        <option value="Grade 9"  {{ request('grade') == 'Grade 9'  ? 'selected' : '' }}>Grade 9</option>
                        <option value="Grade 10" {{ request('grade') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                        <option value="Grade 11" {{ request('grade') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                        <option value="Grade 12" {{ request('grade') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select filter-select" id="rfidFilter">
                        <option value="">RFID Status</option>
                        <option value="with"    {{ request('rfid') == 'with'    ? 'selected' : '' }}>With RFID</option>
                        <option value="without" {{ request('rfid') == 'without' ? 'selected' : '' }}>Without RFID</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-outline-primary w-100" id="applyFilters">
                        <i class="bi bi-funnel me-2"></i>Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
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
                                        <div class="student-avatar">{{ substr($student->name, 0, 2) }}</div>
                                        <div>
                                            <div class="fw-semibold">{{ $student->name }}</div>
                                            @if($student->email)
                                                <small class="text-muted">{{ $student->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><code class="lrn-code">{{ $student->lrn }}</code></td>
                                <td><span class="badge badge-grade">{{ $student->grade }}</span></td>
                                <td>
                                    @if($student->rfid)
                                        <span class="badge badge-rfid-active">
                                            <i class="bi bi-check-circle-fill me-1"></i>Registered
                                        </span>
                                    @else
                                        <span class="badge badge-rfid-inactive">
                                            <i class="bi bi-x-circle-fill me-1"></i>Not Set
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-action btn-view"
                                            onclick="viewStudent({{ $student->id }})" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-action btn-edit"
                                            onclick="editStudent({{ $student->id }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-action btn-delete"
                                            onclick="deleteStudent({{ $student->id }})" title="Delete">
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

            <!-- ── FIXED PAGINATION ── -->
            @if($students->hasPages())
                <div class="table-footer">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <p class="text-muted mb-0 small">
                            Showing <strong>{{ $students->firstItem() }}</strong> to
                            <strong>{{ $students->lastItem() }}</strong> of
                            <strong>{{ $students->total() }}</strong> results
                        </p>
                        <nav aria-label="Student pagination">
                            <ul class="pagination mb-0">
                                {{-- Previous --}}
                                @if($students->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $students->previousPageUrl() }}" aria-label="Previous">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Page numbers --}}
                                @foreach($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                                    @if($page == $students->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @elseif($page == 1 || $page == $students->lastPage() || abs($page - $students->currentPage()) <= 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @elseif(abs($page - $students->currentPage()) == 2)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($students->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $students->nextPageUrl() }}" aria-label="Next">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── MODALS (unchanged) ── --}}

    <!-- Student Detail Modal -->
    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-modern">
                <div class="modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon"><i class="bi bi-person-circle"></i></div>
                        <div>
                            <h5 class="modal-title mb-0" id="modalStudentName">Student Details</h5>
                            <small class="text-muted" id="modalStudentId"></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="modal-body-custom">
                    <div class="row g-4">
                        <div class="col-md-6"><div class="info-item"><label><i class="bi bi-person me-2"></i>Full Name</label><div class="info-value" id="modalName">-</div></div></div>
                        <div class="col-md-6"><div class="info-item"><label><i class="bi bi-hash me-2"></i>LRN</label><div class="info-value" id="modalLrn">-</div></div></div>
                        <div class="col-md-6"><div class="info-item"><label><i class="bi bi-mortarboard me-2"></i>Grade Level</label><div class="info-value" id="modalGrade">-</div></div></div>
                        <div class="col-md-12"><div class="info-item"><label><i class="bi bi-credit-card me-2"></i>RFID Number</label><div class="info-value" id="modalRfid">-</div></div></div>
                    </div>
                    <div class="rfid-status-card mt-4" id="rfidStatusCard">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-shield-check status-icon"></i>
                                <div>
                                    <div class="status-title">RFID Status</div>
                                    <div class="status-text" id="rfidStatusText">Active</div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" id="updateRfidBtn"><i class="bi bi-pencil me-1"></i>Update RFID</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn btn-primary" id="editFromViewModalBtn"><i class="bi bi-pencil me-2"></i>Edit Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-modern">
                <div class="modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon bg-warning"><i class="bi bi-pencil-square"></i></div>
                        <div>
                            <h5 class="modal-title mb-0">Edit Student</h5>
                            <small class="text-muted" id="editModalStudentId"></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
                <form id="editStudentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body-custom">
                        <div class="row g-4">
                            <div class="col-md-6"><div class="form-group-modal"><label for="editName" class="form-label-modal"><i class="bi bi-person me-2"></i>Student Name *</label><input type="text" class="form-control-modal" id="editName" name="name" required></div></div>
                            <div class="col-md-6"><div class="form-group-modal"><label for="editLrn" class="form-label-modal"><i class="bi bi-hash me-2"></i>LRN *</label><input type="text" class="form-control-modal" id="editLrn" name="lrn" maxlength="12" required></div></div>
                            <div class="col-md-6"><div class="form-group-modal"><label for="editGrade" class="form-label-modal"><i class="bi bi-mortarboard me-2"></i>Grade Level *</label><select class="form-control-modal" id="editGrade" name="grade" required><option value="">Select grade level</option><option value="Grade 7">Grade 7</option><option value="Grade 8">Grade 8</option><option value="Grade 9">Grade 9</option><option value="Grade 10">Grade 10</option><option value="Grade 11">Grade 11</option><option value="Grade 12">Grade 12</option></select></div></div>
                            <div class="col-md-6"><div class="form-group-modal"><label for="editEmail" class="form-label-modal"><i class="bi bi-envelope me-2"></i>Email Address</label><input type="email" class="form-control-modal" id="editEmail" name="email"></div></div>
                            <div class="col-md-12"><div class="form-group-modal"><label for="editRfid" class="form-label-modal"><i class="bi bi-credit-card me-2"></i>RFID Number</label><div class="input-group"><input type="text" class="form-control-modal" id="editRfid" name="rfid" placeholder="Optional"><button class="btn btn-outline-primary" type="button" id="scanRfidBtn"><i class="bi bi-upc-scan me-1"></i>Scan Card</button></div></div></div>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveEditBtn"><i class="bi bi-check-circle me-2"></i>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content delete-modal">
                <div class="modal-body text-center p-5">
                    <div class="delete-icon mb-4"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <h4 class="mb-3">Delete Student?</h4>
                    <p class="text-muted mb-4">Are you sure you want to delete <strong id="deleteStudentName"></strong>?<br>This action cannot be undone.</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                        <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn"><i class="bi bi-trash me-2"></i>Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CSV Upload Modal -->
    <div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-modern">
                <div class="modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon" style="background:linear-gradient(135deg,#10b981 0%,#059669 100%)"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                        <div>
                            <h5 class="modal-title mb-0">Import Students from CSV</h5>
                            <small class="text-muted">Upload a CSV file to bulk import students</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
                <form id="uploadCsvForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body-custom">
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>CSV Format Requirements</h6>
                            <ul class="mb-0 small">
                                <li><strong>Required columns:</strong> name, lrn, grade</li>
                                <li><strong>Optional columns:</strong> email, rfid</li>
                                <li><strong>Grade format:</strong> "Grade 7", "Grade 8", etc. or just "7", "8", etc.</li>
                                <li><strong>Example:</strong> Download the template below to see the correct format</li>
                            </ul>
                        </div>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="csvFile" name="csv_file" accept=".csv" class="d-none">
                            <div class="upload-content">
                                <i class="bi bi-cloud-upload upload-icon"></i>
                                <h5 class="mt-3">Drop CSV file here or click to browse</h5>
                                <p class="text-muted">Maximum file size: 5MB</p>
                                <button type="button" class="btn btn-primary mt-2" id="browseBtn"><i class="bi bi-folder2-open me-2"></i>Browse Files</button>
                            </div>
                            <div class="selected-file" id="selectedFile" style="display:none;">
                                <i class="bi bi-file-earmark-spreadsheet-fill text-success"></i>
                                <div class="file-info"><strong id="fileName"></strong><small class="text-muted" id="fileSize"></small></div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeFileBtn"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                        <div id="previewSection" style="display:none;">
                            <hr class="my-4">
                            <h6 class="mb-3"><i class="bi bi-eye me-2"></i>Preview (First 5 rows)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="previewTable">
                                    <thead class="table-light"><tr id="previewHeaders"></tr></thead>
                                    <tbody id="previewBody"></tbody>
                                </table>
                            </div>
                            <div id="previewSummary" class="text-muted small"></div>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                        <button type="submit" class="btn btn-success" id="uploadBtn" disabled><i class="bi bi-upload me-2"></i>Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
        </script>
    @endif

    <style>
        .students-container { padding: 2rem 0; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        .students-header { background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%); box-shadow:0 10px 30px rgba(30,64,175,0.3); padding:2rem; border-radius:16px; color:white; }
        .header-icon { width:64px; height:64px; background:rgba(255,255,255,0.2); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:2rem; }
        .students-header h2 { font-weight:700; font-size:1.75rem; }

        .stat-mini-card { background:white; padding:1.25rem; border-radius:12px; display:flex; align-items:center; gap:1rem; box-shadow:0 2px 12px rgba(0,0,0,0.08); border-left:4px solid; transition:all 0.3s ease; }
        .stat-mini-card:hover { transform:translateY(-4px); box-shadow:0 4px 16px rgba(0,0,0,0.12); }
        .stat-blue { border-color:#3b82f6; } .stat-green { border-color:#10b981; } .stat-orange { border-color:#f59e0b; } .stat-purple { border-color:#8b5cf6; }
        .stat-mini-card .stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
        .stat-blue .stat-icon { background:rgba(59,130,246,0.1); color:#3b82f6; }
        .stat-green .stat-icon { background:rgba(16,185,129,0.1); color:#10b981; }
        .stat-orange .stat-icon { background:rgba(245,158,11,0.1); color:#f59e0b; }
        .stat-purple .stat-icon { background:rgba(139,92,246,0.1); color:#8b5cf6; }
        .stat-label { font-size:0.75rem; color:#6b7280; font-weight:500; }
        .stat-value { font-size:1.5rem; font-weight:700; color:#111827; }

        .filter-card { background:white; padding:1.5rem; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); }
        .search-box { position:relative; }
        .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:1.125rem; }
        .search-box .form-control { padding-left:3rem; padding-right:3rem; border:2px solid #e5e7eb; border-radius:10px; transition:all 0.3s ease; }
        .search-box .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 4px rgba(59,130,246,0.1); }
        .btn-clear { position:absolute; right:0.5rem; top:50%; transform:translateY(-50%); border:none; background:transparent; color:#9ca3af; }
        .filter-select { border:2px solid #e5e7eb; border-radius:10px; }
        .filter-select:focus { border-color:#3b82f6; box-shadow:0 0 0 4px rgba(59,130,246,0.1); }

        .table-card { background:white; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); overflow:hidden; }
        .student-table { margin:0; }
        .student-table thead { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); }
        .student-table th { font-weight:700; color:#374151; border-bottom:2px solid #e5e7eb; padding:1.25rem 1rem; font-size:0.875rem; text-transform:uppercase; letter-spacing:0.05em; }
        .student-table td { vertical-align:middle; border-bottom:1px solid #f3f4f6; padding:0.875rem 1rem; }
        .student-row:hover { background:#f9fafb; }
        .student-name-cell { display:flex; align-items:center; gap:0.75rem; }
        .student-avatar { width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.875rem; text-transform:uppercase; flex-shrink:0; }
        .lrn-code { background:#f3f4f6; padding:0.25rem 0.625rem; border-radius:6px; font-size:0.8125rem; font-weight:600; color:#6b7280; }
        .badge-grade { background:linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%); color:white; padding:0.375rem 0.875rem; border-radius:6px; font-weight:600; font-size:0.8125rem; }
        .badge-rfid-active { background:rgba(16,185,129,0.1); color:#059669; padding:0.375rem 0.875rem; border-radius:6px; font-weight:600; font-size:0.8125rem; border:1px solid rgba(16,185,129,0.2); }
        .badge-rfid-inactive { background:rgba(239,68,68,0.1); color:#dc2626; padding:0.375rem 0.875rem; border-radius:6px; font-weight:600; font-size:0.8125rem; border:1px solid rgba(239,68,68,0.2); }
        .action-buttons { display:flex; gap:0.375rem; justify-content:center; }
        .btn-action { width:32px; height:32px; padding:0; border-radius:6px; border:none; display:flex; align-items:center; justify-content:center; transition:all 0.2s ease; }
        .btn-view { background:rgba(59,130,246,0.1); color:#3b82f6; }
        .btn-view:hover { background:#3b82f6; color:white; transform:scale(1.1); }
        .btn-edit { background:rgba(245,158,11,0.1); color:#d97706; }
        .btn-edit:hover { background:#f59e0b; color:white; transform:scale(1.1); }
        .btn-delete { background:rgba(239,68,68,0.1); color:#dc2626; }
        .btn-delete:hover { background:#ef4444; color:white; transform:scale(1.1); }
        .empty-state { color:#9ca3af; }
        .empty-state i { font-size:4rem; }

        /* ── Pagination ── */
        .table-footer { padding:1.25rem 1.5rem; border-top:1px solid #f3f4f6; }
        .pagination { gap:0.2rem; flex-wrap:wrap; margin:0; }
        .pagination .page-link {
            width:34px; height:34px;
            display:flex; align-items:center; justify-content:center;
            padding:0; font-size:0.8rem; font-weight:500;
            border-radius:7px !important;
            border:1px solid #e5e7eb;
            color:#374151;
            line-height:1;
        }
        /* THIS is the key fix — constrain icon size inside page-link */
        .pagination .page-link i {
            font-size:0.75rem !important;
            line-height:1;
            pointer-events:none;
        }
        .pagination .page-item.active .page-link { background:#2563eb; border-color:#2563eb; color:white; box-shadow:0 2px 8px rgba(37,99,235,0.3); }
        .pagination .page-item.disabled .page-link { color:#d1d5db; background:#f9fafb; border-color:#f3f4f6; }
        .pagination .page-item:not(.active):not(.disabled) .page-link:hover { background:#eff6ff; border-color:#2563eb; color:#2563eb; }

        /* Modal */
        .modal-modern .modal-content { border:none; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.3); }
        .modal-header-custom { padding:1.5rem 2rem; border-bottom:2px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; }
        .modal-icon { width:48px; height:48px; background:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:1.35rem; margin-right:1rem; flex-shrink:0; }
        .modal-icon.bg-warning { background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%); }
        .modal-title { font-weight:700; font-size:1.125rem; }
        .btn-close-custom { width:34px; height:34px; border-radius:8px; border:none; background:#f3f4f6; color:#6b7280; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s; }
        .btn-close-custom:hover { background:#e5e7eb; color:#374151; }
        .modal-body-custom { padding:1.5rem 2rem; }
        .modal-footer-custom { padding:1.25rem 2rem; border-top:2px solid #f3f4f6; display:flex; justify-content:flex-end; gap:0.75rem; }
        .info-item { margin-bottom:1rem; }
        .info-item label { font-size:0.8125rem; color:#6b7280; font-weight:600; margin-bottom:0.5rem; display:flex; align-items:center; }
        .info-item label i { color:#3b82f6; }
        .info-value { font-size:1rem; color:#111827; font-weight:600; padding:0.75rem; background:#f9fafb; border-radius:8px; }
        .rfid-status-card { background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%); padding:1.5rem; border-radius:12px; border:2px solid #bae6fd; }
        .status-icon { font-size:2rem; color:#0284c7; margin-right:1rem; }
        .status-title { font-size:0.875rem; color:#075985; font-weight:600; }
        .status-text { font-size:1.125rem; color:#0c4a6e; font-weight:700; }
        .form-group-modal { margin-bottom:0; }
        .form-label-modal { font-weight:600; color:#374151; margin-bottom:0.5rem; font-size:0.9375rem; display:flex; align-items:center; }
        .form-label-modal i { color:#3b82f6; }
        .form-control-modal { width:100%; padding:0.75rem 1rem; border:2px solid #e5e7eb; border-radius:10px; font-size:0.9375rem; transition:all 0.3s ease; background:#f9fafb; }
        .form-control-modal:focus { outline:none; border-color:#3b82f6; background:white; box-shadow:0 0 0 4px rgba(59,130,246,0.1); }
        .input-group .form-control-modal { border-right:none; border-top-right-radius:0; border-bottom-right-radius:0; }
        .input-group .btn { border-top-left-radius:0; border-bottom-left-radius:0; border:2px solid #e5e7eb; border-left:none; }
        .input-group .btn:hover { background:#3b82f6; color:white; border-color:#3b82f6; }
        .delete-modal .modal-content { border:none; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.3); }
        .delete-icon { width:80px; height:80px; background:linear-gradient(135deg,#fee2e2 0%,#fecaca 100%); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto; animation:pulseWarning 2s infinite; }
        .delete-icon i { font-size:3rem; color:#ef4444; }
        @keyframes pulseWarning { 0%,100% { transform:scale(1); box-shadow:0 0 0 0 rgba(239,68,68,0.4); } 50% { transform:scale(1.05); box-shadow:0 0 0 20px rgba(239,68,68,0); } }

        /* Upload */
        .btn-upload { background:linear-gradient(135deg,#10b981 0%,#059669 100%); color:white; font-weight:600; padding:0.75rem 1.5rem; border-radius:10px; border:none; box-shadow:0 4px 12px rgba(16,185,129,0.3); transition:all 0.3s ease; }
        .btn-upload:hover { transform:translateY(-2px); color:white; }
        .btn-download { font-weight:600; padding:0.75rem 1.5rem; border-radius:10px; border:2px solid white; color:white; transition:all 0.3s ease; }
        .btn-download:hover { transform:translateY(-2px); background:white; color:#1e40af; }
        .upload-area { border:2px dashed #d1d5db; border-radius:12px; padding:3rem 2rem; text-align:center; transition:all 0.3s ease; cursor:pointer; }
        .upload-area:hover,.upload-area.drag-over { border-color:#10b981; background:#f0fdf4; }
        .upload-icon { font-size:4rem; color:#10b981; }
        .selected-file { display:flex; align-items:center; gap:1rem; padding:1rem; background:#f0fdf4; border-radius:8px; }
        .selected-file i { font-size:2rem; }
        .file-info { flex:1; text-align:left; }

        /* Responsive */
        @media (max-width: 768px) {
            .students-header h2 { font-size:1.25rem; }
            .student-table th, .student-table td { padding:0.75rem 0.5rem; font-size:0.8rem; }
            .student-avatar { width:32px; height:32px; font-size:0.75rem; }
            .lrn-code { font-size:0.75rem; }
        }
        @media (max-width: 576px) {
            .students-header { padding:1.25rem; }
            .header-icon { width:48px; height:48px; font-size:1.5rem; }
        }
    </style>

    <script>
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        searchInput.addEventListener('input', function() {
            clearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';
        });
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = ''; this.style.display = 'none'; applyFilters();
        });
        document.getElementById('applyFilters').addEventListener('click', applyFilters);
        function applyFilters() {
            const params = new URLSearchParams();
            const s = document.getElementById('searchInput').value;
            const g = document.getElementById('gradeFilter').value;
            const r = document.getElementById('rfidFilter').value;
            if(s) params.append('search',s);
            if(g) params.append('grade',g);
            if(r) params.append('rfid',r);
            window.location.href = '{{ route("students.create") }}' + '?' + params.toString();
        }
        searchInput.addEventListener('keypress', e => { if(e.key==='Enter') applyFilters(); });

        function viewStudent(id) {
            Swal.fire({title:'Loading...',allowOutsideClick:false,showConfirmButton:false,didOpen:()=>Swal.showLoading()});
            fetch(`/students/${id}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'}})
            .then(r=>r.json()).then(data=>{
                Swal.close();
                document.getElementById('modalStudentName').textContent=data.name||'N/A';
                document.getElementById('modalStudentId').textContent=`Student ID: ${data.id}`;
                document.getElementById('modalName').textContent=data.name||'N/A';
                document.getElementById('modalLrn').textContent=data.lrn||'N/A';
                document.getElementById('modalGrade').textContent=data.grade||'N/A';
                document.getElementById('modalRfid').textContent=data.rfid||'Not registered';
                const st=document.getElementById('rfidStatusText');
                st.textContent=data.rfid?'Active & Registered':'Not Registered';
                st.style.color=data.rfid?'#059669':'#dc2626';
                window.currentStudentId=data.id;
                new bootstrap.Modal(document.getElementById('studentModal')).show();
            }).catch(()=>Swal.fire({icon:'error',title:'Error',text:'Failed to load student details.'}));
        }

        function editStudent(id) {
            Swal.fire({title:'Loading...',allowOutsideClick:false,showConfirmButton:false,didOpen:()=>Swal.showLoading()});
            fetch(`/students/${id}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'}})
            .then(r=>r.json()).then(data=>{
                Swal.close();
                document.getElementById('editModalStudentId').textContent=`Editing Student ID: ${data.id}`;
                document.getElementById('editName').value=data.name||'';
                document.getElementById('editLrn').value=data.lrn||'';
                document.getElementById('editGrade').value=data.grade||'';
                document.getElementById('editEmail').value=data.email||'';
                document.getElementById('editRfid').value=data.rfid||'';
                document.getElementById('editStudentForm').action=`/students/${id}`;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }).catch(()=>Swal.fire({icon:'error',title:'Error',text:'Failed to load student details.'}));
        }

        document.getElementById('editFromViewModalBtn')?.addEventListener('click',function(){
            if(!window.currentStudentId) return;
            bootstrap.Modal.getInstance(document.getElementById('studentModal')).hide();
            setTimeout(()=>editStudent(window.currentStudentId),300);
        });

        document.getElementById('editStudentForm')?.addEventListener('submit',function(e){
            e.preventDefault();
            const btn=document.getElementById('saveEditBtn');
            const orig=btn.innerHTML;
            btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            fetch(this.action,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:new FormData(this)})
            .then(r=>r.json()).then(data=>{
                if(data.success){
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    Swal.fire({icon:'success',title:'Student Updated!',text:'Updated successfully',confirmButtonColor:'#3b82f6'}).then(()=>location.reload());
                } else { throw new Error(data.message||'Update failed'); }
            }).catch(err=>{ btn.disabled=false; btn.innerHTML=orig; Swal.fire({icon:'error',title:'Update Failed',text:err.message,confirmButtonColor:'#3b82f6'}); });
        });

        function deleteStudent(id) {
            const row=document.querySelector(`tr[data-student-id="${id}"]`);
            const name=row?row.querySelector('.fw-semibold').textContent:'this student';
            Swal.fire({title:'Are you sure?',html:`<p>Delete: <strong class="text-danger">${name}</strong></p><small>This cannot be undone.</small>`,icon:'warning',showCancelButton:true,confirmButtonText:'Yes, Delete',confirmButtonColor:'#dc2626',cancelButtonColor:'#3b82f6'})
            .then(r=>{
                if(r.isConfirmed){
                    Swal.fire({title:'Deleting...',allowOutsideClick:false,showConfirmButton:false,didOpen:()=>Swal.showLoading()});
                    fetch(`/students/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
                    .then(r=>r.json()).then(data=>{
                        if(data.success){ Swal.fire({icon:'success',title:'Deleted!',text:data.message,timer:1500,showConfirmButton:false}); if(row) row.remove(); }
                        else throw new Error(data.message||'Delete failed');
                    }).catch(err=>Swal.fire({icon:'error',title:'Delete Failed',text:err.message,confirmButtonColor:'#3b82f6'}));
                }
            });
        }

        document.getElementById('updateRfidBtn')?.addEventListener('click',function(){
            if(!window.currentStudentId) return;
            Swal.fire({title:'Update RFID',html:'<input type="text" id="rfidInput" class="form-control form-control-lg" placeholder="Scan or enter RFID" autofocus>',showCancelButton:true,confirmButtonText:'Update',confirmButtonColor:'#3b82f6',preConfirm:()=>{const v=document.getElementById('rfidInput').value;if(!v){Swal.showValidationMessage('Enter RFID');return false;}return v;}})
            .then(r=>{ if(r.isConfirmed) Swal.fire({icon:'success',title:'RFID Updated!',confirmButtonColor:'#3b82f6'}).then(()=>location.reload()); });
        });

        document.addEventListener('DOMContentLoaded',function(){
            const modal=new bootstrap.Modal(document.getElementById('uploadCsvModal'));
            document.getElementById('uploadCsvBtn').addEventListener('click',()=>modal.show());
            document.getElementById('downloadTemplateBtn').addEventListener('click',()=>window.location.href='/students/download-template');
            const csvFile=document.getElementById('csvFile');
            const uploadBtn=document.getElementById('uploadBtn');
            document.getElementById('browseBtn').addEventListener('click',()=>csvFile.click());
            document.getElementById('uploadArea').addEventListener('click',e=>{ if(e.target===document.getElementById('uploadArea')||e.target.closest('.upload-content')) csvFile.click(); });
            const ua=document.getElementById('uploadArea');
            ua.addEventListener('dragover',e=>{e.preventDefault();ua.classList.add('drag-over');});
            ua.addEventListener('dragleave',()=>ua.classList.remove('drag-over'));
            ua.addEventListener('drop',e=>{e.preventDefault();ua.classList.remove('drag-over');const f=e.dataTransfer.files;if(f.length>0&&f[0].name.endsWith('.csv')){csvFile.files=f;handleFileSelect(f[0]);}else Swal.fire({icon:'error',title:'Invalid File',text:'Please upload a CSV file'});});
            csvFile.addEventListener('change',function(){if(this.files.length>0) handleFileSelect(this.files[0]);});
            document.getElementById('removeFileBtn').addEventListener('click',()=>{csvFile.value='';document.querySelector('.upload-content').style.display='block';document.getElementById('selectedFile').style.display='none';document.getElementById('previewSection').style.display='none';uploadBtn.disabled=true;});

            function handleFileSelect(file){
                if(!file.name.endsWith('.csv')){Swal.fire({icon:'error',title:'Invalid File',text:'Please upload a CSV file'});return;}
                if(file.size>5*1024*1024){Swal.fire({icon:'error',title:'File Too Large',text:'Max 5MB'});return;}
                document.querySelector('.upload-content').style.display='none';
                document.getElementById('selectedFile').style.display='flex';
                document.getElementById('fileName').textContent=file.name;
                document.getElementById('fileSize').textContent=formatSize(file.size);
                uploadBtn.disabled=false;
                previewCSV(file);
            }
            function previewCSV(file){
                const r=new FileReader();
                r.onload=function(e){
                    const lines=e.target.result.split('\n').filter(l=>l.trim());
                    if(!lines.length) return;
                    const headers=lines[0].split(',').map(h=>h.trim());
                    document.getElementById('previewHeaders').innerHTML=headers.map(h=>`<th>${h}</th>`).join('');
                    const body=document.getElementById('previewBody'); body.innerHTML='';
                    for(let i=1;i<Math.min(6,lines.length);i++){const tr=document.createElement('tr');tr.innerHTML=lines[i].split(',').map(c=>`<td>${c.trim()}</td>`).join('');body.appendChild(tr);}
                    document.getElementById('previewSection').style.display='block';
                    document.getElementById('previewSummary').textContent=`Total rows: ${lines.length-1} students`;
                };
                r.readAsText(file);
            }
            function formatSize(b){if(b===0)return'0 B';const k=1024,s=['B','KB','MB'],i=Math.floor(Math.log(b)/Math.log(k));return Math.round(b/Math.pow(k,i)*100)/100+' '+s[i];}

            document.getElementById('uploadCsvForm').addEventListener('submit',function(e){
                e.preventDefault();
                if(!csvFile.files.length){Swal.fire({icon:'warning',title:'No File',text:'Select a CSV file'});return;}
                const fd=new FormData(); fd.append('csv_file',csvFile.files[0]);
                uploadBtn.disabled=true; uploadBtn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
                fetch('/students/import-csv',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:fd})
                .then(r=>r.json()).then(data=>{
                    uploadBtn.disabled=false; uploadBtn.innerHTML='<i class="bi bi-upload me-2"></i>Upload & Import';
                    if(data.success){modal.hide();Swal.fire({icon:'success',title:'Import Successful!',html:`<p><strong>${data.imported}</strong> students imported</p>`,confirmButtonColor:'#3b82f6'}).then(()=>location.reload());}
                    else Swal.fire({icon:'error',title:'Import Failed',text:data.message,confirmButtonColor:'#3b82f6'});
                }).catch(()=>{uploadBtn.disabled=false;uploadBtn.innerHTML='<i class="bi bi-upload me-2"></i>Upload & Import';Swal.fire({icon:'error',title:'Upload Error',text:'Please try again.'});});
            });
        });
    </script>
@endsection