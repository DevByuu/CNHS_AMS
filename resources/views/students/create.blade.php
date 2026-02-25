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
                            <p class="header-sub mb-0">View and manage student records</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                        <button class="btn-action-header btn-add" id="addStudentBtn">
                            <i class="bi bi-person-plus-fill me-2"></i>Add Student
                        </button>
                        <button class="btn-action-header btn-import" id="uploadCsvBtn">
                            <i class="bi bi-upload me-2"></i>Import CSV
                        </button>
                        <button class="btn-action-header btn-template" id="downloadTemplateBtn">
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
                        <input type="text" class="form-control dark-input" id="searchInput"
                            placeholder="Search by name, LRN, or RFID..." value="{{ request('search') }}">
                        <button class="btn-clear" id="clearSearch" style="display:none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-2">
                    <select class="form-select dark-select" id="gradeFilter">
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
                    <select class="form-select dark-select" id="rfidFilter">
                        <option value="">RFID Status</option>
                        <option value="with"    {{ request('rfid') == 'with'    ? 'selected' : '' }}>With RFID</option>
                        <option value="without" {{ request('rfid') == 'without' ? 'selected' : '' }}>Without RFID</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <button class="btn-filter w-100" id="applyFilters">
                        <i class="bi bi-funnel me-2"></i>Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Batch Delete Toolbar (hidden until selections made) -->
        <div class="batch-toolbar" id="batchToolbar" style="display:none;">
            <div class="d-flex align-items-center gap-3">
                <span class="batch-count"><span id="selectedCount">0</span> student(s) selected</span>
                <button class="btn-batch-delete" id="batchDeleteBtn">
                    <i class="bi bi-trash-fill me-2"></i>Delete Selected
                </button>
                <button class="btn-batch-cancel" id="batchCancelBtn">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table student-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th width="40">
                                <label class="check-wrap">
                                    <input type="checkbox" id="selectAll" class="form-check-input dark-check">
                                    <span class="check-box"></span>
                                </label>
                            </th>
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
                                <td>
                                    <label class="check-wrap">
                                        <input type="checkbox" class="form-check-input dark-check row-checkbox" value="{{ $student->id }}">
                                        <span class="check-box"></span>
                                    </label>
                                </td>
                                <td class="row-num">{{ $students->firstItem() + $index }}</td>
                                <td>
                                    <div class="student-name-cell">
                                        <div class="student-avatar">{{ substr($student->name, 0, 2) }}</div>
                                        <div>
                                            <div class="fw-semibold" style="color:#111827;">{{ $student->name }}</div>
                                            @if($student->email)
                                                <small style="color:#6b7280;">{{ $student->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><code class="lrn-code">{{ $student->lrn }}</code></td>
                                <td><span class="badge-grade">{{ $student->grade }}</span></td>
                                <td>
                                    @if($student->rfid)
                                        <span class="badge-rfid-active">
                                            <i class="bi bi-check-circle-fill me-1"></i>Registered
                                        </span>
                                    @else
                                        <span class="badge-rfid-inactive">
                                            <i class="bi bi-x-circle-fill me-1"></i>Not Set
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button class="btn-row btn-view" onclick="viewStudent({{ $student->id }})" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn-row btn-edit" onclick="editStudent({{ $student->id }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-row btn-delete" onclick="deleteStudent({{ $student->id }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0 mt-3">No students found</p>
                                        <small>Try adjusting your search or filters</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="table-footer">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <p class="pagination-info mb-0">
                            Showing <strong>{{ $students->firstItem() }}</strong> to
                            <strong>{{ $students->lastItem() }}</strong> of
                            <strong>{{ $students->total() }}</strong> results
                        </p>
                        <nav aria-label="Student pagination">
                            <ul class="pagination mb-0">
                                @if($students->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $students->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a></li>
                                @endif

                                @foreach($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                                    @if($page == $students->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @elseif($page == 1 || $page == $students->lastPage() || abs($page - $students->currentPage()) <= 1)
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @elseif(abs($page - $students->currentPage()) == 2)
                                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                    @endif
                                @endforeach

                                @if($students->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $students->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── MODALS ── --}}

    <!-- View Modal -->
    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-dark">
                <div class="modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon"><i class="bi bi-person-circle"></i></div>
                        <div>
                            <h5 class="modal-title mb-0" id="modalStudentName">Student Details</h5>
                            <small style="color:#6b7280;" id="modalStudentId"></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
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
                            <button class="btn-modal-sm" id="updateRfidBtn"><i class="bi bi-pencil me-1"></i>Update RFID</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn-modal-primary" id="editFromViewModalBtn"><i class="bi bi-pencil me-2"></i>Edit Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-dark">
                <div class="modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon modal-icon-warning"><i class="bi bi-pencil-square"></i></div>
                        <div>
                            <h5 class="modal-title mb-0">Edit Student</h5>
                            <small style="color:#6b7280;" id="editModalStudentId"></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
                <form id="editStudentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body-custom">
                        <div class="row g-4">
                            <div class="col-md-6"><div class="form-group-modal"><label class="form-label-modal"><i class="bi bi-person me-2"></i>Student Name *</label><input type="text" class="form-control-modal" id="editName" name="name" required></div></div>
                            <div class="col-md-6"><div class="form-group-modal"><label class="form-label-modal"><i class="bi bi-hash me-2"></i>LRN *</label><input type="text" class="form-control-modal" id="editLrn" name="lrn" maxlength="12" required></div></div>
                            <div class="col-md-6"><div class="form-group-modal"><label class="form-label-modal"><i class="bi bi-mortarboard me-2"></i>Grade Level *</label><select class="form-control-modal" id="editGrade" name="grade" required><option value="">Select grade level</option><option value="Grade 7">Grade 7</option><option value="Grade 8">Grade 8</option><option value="Grade 9">Grade 9</option><option value="Grade 10">Grade 10</option><option value="Grade 11">Grade 11</option><option value="Grade 12">Grade 12</option></select></div></div>
                            <div class="col-md-6"><div class="form-group-modal"><label class="form-label-modal"><i class="bi bi-envelope me-2"></i>Email Address</label><input type="email" class="form-control-modal" id="editEmail" name="email"></div></div>
                            <div class="col-md-12"><div class="form-group-modal"><label class="form-label-modal"><i class="bi bi-credit-card me-2"></i>RFID Number</label><div class="input-group-modal"><input type="text" class="form-control-modal" id="editRfid" name="rfid" placeholder="Optional"><button class="btn-scan" type="button" id="scanRfidBtn"><i class="bi bi-upc-scan me-1"></i>Scan Card</button></div></div></div>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                        <button type="submit" class="btn-modal-primary" id="saveEditBtn"><i class="bi bi-check-circle me-2"></i>Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CSV Upload Modal -->
    <div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-dark">
                <div class="modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon modal-icon-green"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                        <div>
                            <h5 class="modal-title mb-0">Import Students from CSV</h5>
                            <small style="color:#6b7280;">Upload a CSV file to bulk import students</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
                <form id="uploadCsvForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body-custom">
                        <div class="alert-info-dark mb-4">
                            <h6 class="mb-2"><i class="bi bi-info-circle me-2" style="color:#2563eb;"></i>CSV Format Requirements</h6>
                            <ul class="mb-0 small" style="color:#374151;">
                                <li><strong style="color:#111827;">Required columns:</strong> name, lrn, grade</li>
                                <li><strong style="color:#111827;">Optional columns:</strong> email, rfid</li>
                                <li><strong style="color:#111827;">Grade format:</strong> "Grade 7", "Grade 8", etc.</li>
                                <li><strong style="color:#111827;">Example:</strong> Download the template to see the correct format</li>
                            </ul>
                        </div>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="csvFile" name="csv_file" accept=".csv" class="d-none">
                            <div class="upload-content">
                                <i class="bi bi-cloud-upload upload-icon"></i>
                                <h5 class="mt-3" style="color:#111827;">Drop CSV file here or click to browse</h5>
                                <p style="color:#6b7280;">Maximum file size: 5MB</p>
                                <button type="button" class="btn-modal-primary mt-2" id="browseBtn"><i class="bi bi-folder2-open me-2"></i>Browse Files</button>
                            </div>
                            <div class="selected-file" id="selectedFile" style="display:none;">
                                <i class="bi bi-file-earmark-spreadsheet-fill" style="font-size:2rem;color:#10b981;"></i>
                                <div class="file-info"><strong id="fileName" style="color:#111827;"></strong><small style="color:#6b7280;" id="fileSize"></small></div>
                                <button type="button" class="btn-row btn-delete" id="removeFileBtn"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                        <div id="previewSection" style="display:none;">
                            <hr style="border-color:#f3f4f6; margin:1.5rem 0;">
                            <h6 class="mb-3" style="color:#374151;"><i class="bi bi-eye me-2"></i>Preview (First 5 rows)</h6>
                            <div class="table-responsive">
                                <table class="table student-table table-sm" id="previewTable">
                                    <thead><tr id="previewHeaders"></tr></thead>
                                    <tbody id="previewBody"></tbody>
                                </table>
                            </div>
                            <div id="previewSummary" style="color:#6b7280;font-size:0.85rem;"></div>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                        <button type="submit" class="btn-modal-green" id="uploadBtn" disabled><i class="bi bi-upload me-2"></i>Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({ icon:'success', title:'Success!', text:"{{ session('success') }}", timer:2000, showConfirmButton:false });
        </script>
    @endif

    <style>
        /* ── Base ── */
        .students-container { animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        /* ── Header ── */
        .students-header { background:linear-gradient(135deg,#1e40af 0%,#1e3a8a 100%); box-shadow:0 10px 30px rgba(30,64,175,0.3); padding:2rem; border-radius:16px; color:white; }
        .header-sub { color:rgba(255,255,255,0.5); font-size:0.9rem; }
        .header-icon { width:64px; height:64px; background:rgba(255,255,255,0.2); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:2rem; }
        .students-header h2 { font-weight:700; font-size:1.75rem; }

        /* ── Header Buttons ── */
        .btn-action-header {
            font-weight:600; padding:0.75rem 1.5rem; border-radius:10px;
            border:none; font-size:0.875rem;
            cursor:pointer; transition:all 0.3s; display:flex; align-items:center;
            white-space:nowrap;
        }
        .btn-import { background:linear-gradient(135deg,#10b981 0%,#059669 100%); color:white; box-shadow:0 4px 12px rgba(16,185,129,0.3); }
        .btn-import:hover { transform:translateY(-2px); color:white; }
        .btn-template { font-weight:600; border-radius:10px; border:2px solid white; color:white; background:transparent; }
        .btn-template:hover { transform:translateY(-2px); background:white; color:#1e40af; }

        /* ── Stat Mini Cards ── */
        .stat-mini-card { background:white; padding:1.25rem; border-radius:12px; display:flex; align-items:center; gap:1rem; box-shadow:0 2px 12px rgba(0,0,0,0.08); border-left:4px solid; transition:all 0.3s ease; }
        .stat-mini-card:hover { transform:translateY(-4px); box-shadow:0 4px 16px rgba(0,0,0,0.12); }
        .stat-blue  { border-color:#3b82f6; }
        .stat-green { border-color:#10b981; }
        .stat-orange{ border-color:#f59e0b; }
        .stat-purple{ border-color:#8b5cf6; }
        .stat-mini-card .stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0; }
        .stat-blue  .stat-icon { background:rgba(59,130,246,0.1);  color:#3b82f6; }
        .stat-green .stat-icon { background:rgba(16,185,129,0.1);  color:#10b981; }
        .stat-orange .stat-icon{ background:rgba(245,158,11,0.1);  color:#f59e0b; }
        .stat-purple .stat-icon{ background:rgba(139,92,246,0.1);  color:#8b5cf6; }
        .stat-label { font-size:0.75rem; color:#6b7280; font-weight:500; }
        .stat-value { font-size:1.5rem; font-weight:700; color:#111827; }

        /* ── Filter Card ── */
        .filter-card { background:white; padding:1.5rem; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); }
        .search-box { position:relative; }
        .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:1.125rem; z-index:1; }
        .dark-input {
            padding-left:3rem !important; padding-right:3rem !important;
            border:2px solid #e5e7eb !important; border-radius:10px !important;
            background:white !important; color:#111827 !important;
            transition:all 0.3s ease;
        }
        .dark-input::placeholder { color:#9ca3af !important; }
        .dark-input:focus { border-color:#3b82f6 !important; box-shadow:0 0 0 4px rgba(59,130,246,0.1) !important; outline:none !important; }
        .btn-clear { position:absolute; right:0.5rem; top:50%; transform:translateY(-50%); background:none; border:none; color:#9ca3af; cursor:pointer; }
        .dark-select {
            border:2px solid #e5e7eb !important; border-radius:10px !important;
            background:white !important; color:#111827 !important;
        }
        .dark-select:focus { border-color:#3b82f6 !important; box-shadow:0 0 0 4px rgba(59,130,246,0.1) !important; outline:none !important; }
        .btn-filter {
            background:white; color:#2563eb;
            border:2px solid #2563eb; border-radius:10px;
            padding:0.6rem 1rem; font-weight:600;
            cursor:pointer; transition:all 0.3s; display:flex; align-items:center; justify-content:center;
        }
        .btn-filter:hover { background:#2563eb; color:white; }

        /* ── Table ── */
        .table-card { background:white; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); overflow:hidden; }
        .student-table { margin:0; }
        .student-table thead { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); }
        .student-table thead th { font-weight:700; color:#374151; border-bottom:2px solid #e5e7eb; padding:1.25rem 1rem; font-size:0.875rem; text-transform:uppercase; letter-spacing:0.05em; }
        .student-table td { vertical-align:middle; border-bottom:1px solid #f3f4f6; padding:0.875rem 1rem; color:#374151; }
        .student-row:hover td { background:#f9fafb; }
        .row-num { color:#9ca3af; font-size:0.875rem; }
        .student-name-cell { display:flex; align-items:center; gap:0.75rem; }
        .student-avatar { width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.875rem; text-transform:uppercase; flex-shrink:0; }
        .lrn-code { background:#f3f4f6; padding:0.25rem 0.625rem; border-radius:6px; font-size:0.8125rem; font-weight:600; color:#6b7280; }
        .badge-grade { background:linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%); color:white; padding:0.375rem 0.875rem; border-radius:6px; font-weight:600; font-size:0.8125rem; }
        .badge-rfid-active { background:rgba(16,185,129,0.1); color:#059669; padding:0.375rem 0.875rem; border-radius:6px; font-weight:600; font-size:0.8125rem; border:1px solid rgba(16,185,129,0.2); }
        .badge-rfid-inactive { background:rgba(239,68,68,0.1); color:#dc2626; padding:0.375rem 0.875rem; border-radius:6px; font-weight:600; font-size:0.8125rem; border:1px solid rgba(239,68,68,0.2); }
        .action-buttons { display:flex; gap:0.375rem; justify-content:center; }
        .btn-row { width:32px; height:32px; padding:0; border-radius:6px; border:none; display:flex; align-items:center; justify-content:center; transition:all 0.2s ease; font-size:0.875rem; cursor:pointer; }
        .btn-view   { background:rgba(59,130,246,0.1);  color:#3b82f6; }
        .btn-view:hover   { background:#3b82f6; color:white; transform:scale(1.1); }
        .btn-edit   { background:rgba(245,158,11,0.1);  color:#d97706; }
        .btn-edit:hover   { background:#f59e0b; color:white; transform:scale(1.1); }
        .btn-delete { background:rgba(239,68,68,0.1);   color:#dc2626; }
        .btn-delete:hover { background:#ef4444; color:white; transform:scale(1.1); }
        .empty-state { color:#9ca3af; }
        .empty-state i { font-size:4rem; }

        /* ── Pagination ── */
        .table-footer { padding:1.25rem 1.5rem; border-top:1px solid #f3f4f6; }
        .pagination-info { color:#6b7280; font-size:0.875rem; }
        .pagination-info strong { color:#111827; }

        /* ── Modal ── */
        .modal-dark { background:white !important; border:none !important; border-radius:16px !important; box-shadow:0 20px 60px rgba(0,0,0,0.3) !important; color:#111827; }
        .modal-header-custom { padding:1.5rem 2rem; border-bottom:2px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; }
        .modal-title { font-weight:700; font-size:1.125rem; color:#111827; }
        .modal-icon { width:48px; height:48px; background:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:1.35rem; margin-right:1rem; flex-shrink:0; border:none; }
        .modal-icon-warning { background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%); }
        .modal-icon-green   { background:linear-gradient(135deg,#10b981 0%,#059669 100%); }
        .modal-icon-add     { background:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%); }
        .btn-close-modal { width:34px; height:34px; border-radius:8px; border:none; background:#f3f4f6; color:#6b7280; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s; }
        .btn-close-modal:hover { background:#e5e7eb; color:#374151; }
        .modal-body-custom { padding:1.5rem 2rem; }
        .modal-footer-custom { padding:1.25rem 2rem; border-top:2px solid #f3f4f6; display:flex; justify-content:flex-end; gap:0.75rem; }

        .info-item { margin-bottom:1rem; }
        .info-item label { font-size:0.8125rem; color:#6b7280; font-weight:600; margin-bottom:0.5rem; display:flex; align-items:center; }
        .info-item label i { color:#3b82f6; }
        .info-value { font-size:1rem; color:#111827; font-weight:600; padding:0.75rem; background:#f9fafb; border-radius:8px; }

        .rfid-status-card { background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%); padding:1.5rem; border-radius:12px; border:2px solid #bae6fd; }
        .status-icon { font-size:2rem; color:#0284c7; margin-right:1rem; }
        .status-title { font-size:0.875rem; color:#075985; font-weight:600; }
        .status-text  { font-size:1.125rem; color:#0c4a6e; font-weight:700; }

        .form-group-modal { margin-bottom:0; }
        .form-label-modal { font-weight:600; color:#374151; margin-bottom:0.5rem; font-size:0.9375rem; display:flex; align-items:center; }
        .form-label-modal i { color:#3b82f6; }
        .form-control-modal { width:100%; padding:0.75rem 1rem; border:2px solid #e5e7eb; border-radius:10px; font-size:0.9375rem; transition:all 0.3s ease; background:#f9fafb; color:#111827; }
        .form-control-modal::placeholder { color:#9ca3af; }
        .form-control-modal:focus { outline:none; border-color:#3b82f6; background:white; box-shadow:0 0 0 4px rgba(59,130,246,0.1); }
        select.form-control-modal option { background:white; color:#111827; }
        .input-group-modal { display:flex; }
        .input-group-modal .form-control-modal { border-top-right-radius:0; border-bottom-right-radius:0; border-right:none; }
        .btn-scan { padding:0 1rem; background:white; color:#3b82f6; border:2px solid #e5e7eb; border-left:none; border-top-right-radius:10px; border-bottom-right-radius:10px; font-weight:600; font-size:0.875rem; cursor:pointer; transition:all 0.2s; white-space:nowrap; }
        .btn-scan:hover { background:#3b82f6; color:white; border-color:#3b82f6; }

        /* Modal Buttons */
        .btn-modal-primary { background:linear-gradient(135deg,#3b82f6,#2563eb); color:white; border:none; border-radius:10px; padding:0.6rem 1.5rem; font-weight:600; cursor:pointer; transition:all 0.25s; box-shadow:0 4px 12px rgba(59,130,246,0.3); display:inline-flex; align-items:center; }
        .btn-modal-primary:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(59,130,246,0.4); }
        .btn-modal-secondary { background:#f3f4f6; color:#6b7280; border:none; border-radius:10px; padding:0.6rem 1.5rem; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; }
        .btn-modal-secondary:hover { background:#e5e7eb; color:#374151; }
        .btn-modal-sm { background:#eff6ff; color:#3b82f6; border:1px solid #bfdbfe; border-radius:8px; padding:0.4rem 0.9rem; font-weight:600; font-size:0.8rem; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; }
        .btn-modal-sm:hover { background:#3b82f6; color:white; border-color:#3b82f6; }
        .btn-modal-green { background:linear-gradient(135deg,#10b981,#059669); color:white; border:none; border-radius:10px; padding:0.6rem 1.5rem; font-weight:600; cursor:pointer; transition:all 0.25s; box-shadow:0 4px 12px rgba(16,185,129,0.3); display:inline-flex; align-items:center; }
        .btn-modal-green:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(16,185,129,0.4); }
        .btn-modal-green:disabled { opacity:0.45; pointer-events:none; }

        /* Upload Area */
        .alert-info-dark { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:1rem 1.25rem; color:#1e40af; }
        .alert-info-dark strong { color:#1e3a8a; }
        .upload-area { border:2px dashed #d1d5db; border-radius:12px; padding:3rem 2rem; text-align:center; transition:all 0.3s ease; cursor:pointer; background:white; }
        .upload-area:hover, .upload-area.drag-over { border-color:#10b981; background:#f0fdf4; }
        .upload-icon { font-size:4rem; color:#10b981; }
        .selected-file { display:flex; align-items:center; gap:1rem; padding:1rem; background:#f0fdf4; border-radius:8px; border:1px solid #bbf7d0; }
        .file-info { flex:1; text-align:left; display:flex; flex-direction:column; gap:0.2rem; }

        .btn-add {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white; box-shadow: 0 4px 14px rgba(59,130,246,0.35);
        }
        .btn-add:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(59,130,246,0.5); }

        /* ── Batch Toolbar ── */
        .batch-toolbar {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px; padding: 0.875rem 1.25rem;
            margin-bottom: 1rem;
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .batch-count { color: #dc2626; font-weight: 700; font-size: 0.875rem; }
        .btn-batch-delete {
            background: #ef4444; color: white;
            border: none; border-radius: 8px;
            padding: 0.45rem 1rem; font-weight: 600; font-size: 0.875rem;
            cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center;
            box-shadow: 0 2px 8px rgba(239,68,68,0.3);
        }
        .btn-batch-delete:hover { background: #dc2626; box-shadow: 0 4px 14px rgba(239,68,68,0.4); transform:translateY(-1px); }
        .btn-batch-cancel {
            background: white; color: #6b7280;
            border: 1px solid #e5e7eb; border-radius: 8px;
            padding: 0.45rem 0.875rem; font-weight: 600; font-size: 0.875rem;
            cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center;
        }
        .btn-batch-cancel:hover { color: #374151; border-color: #d1d5db; background: #f9fafb; }

        /* ── Checkbox ── */
        .check-wrap { display: flex; align-items: center; cursor: pointer; margin: 0; }
        .dark-check { display: none; }
        .check-box {
            width: 17px; height: 17px;
            border: 2px solid #d1d5db;
            border-radius: 4px; background: white;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: border-color 0.2s, background 0.2s;
        }
        .dark-check:checked + .check-box {
            background: #2563eb; border-color: #2563eb;
        }
        .dark-check:checked + .check-box::after {
            content: ''; width: 5px; height: 9px;
            border: 2px solid white; border-top: none; border-left: none;
            transform: rotate(45deg) translateY(-1px);
        }
        .student-row.selected td { background: #eff6ff !important; }

        /* ── Add Modal Icon ── */
        .modal-icon-add { background:linear-gradient(135deg,#3b82f6,#2563eb); color:white; border:none; }

        /* ── Field Error ── */
        .field-error { display: block; color: #dc2626; font-size: 0.78rem; margin-top: 0.35rem; font-weight: 500; min-height: 1rem; }
        @media (max-width:768px) {
            .students-header h2 { font-size:1.25rem; }
            .student-table th, .student-table td { padding:0.6rem 0.5rem; font-size:0.8rem; }
            .student-avatar { width:32px; height:32px; font-size:0.75rem; }
        }
        @media (max-width:576px) {
            .students-header { padding:1.25rem; }
            .header-icon { width:48px; height:48px; font-size:1.5rem; }
        }
    </style>

    <script>
        // ── Search / Filters ──
        const searchInput   = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        searchInput.addEventListener('input', function () {
            clearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';
        });
        clearSearchBtn.addEventListener('click', function () {
            searchInput.value = ''; this.style.display = 'none'; applyFilters();
        });
        document.getElementById('applyFilters').addEventListener('click', applyFilters);
        function applyFilters() {
            const p = new URLSearchParams();
            const s = searchInput.value, g = document.getElementById('gradeFilter').value, r = document.getElementById('rfidFilter').value;
            if (s) p.append('search', s); if (g) p.append('grade', g); if (r) p.append('rfid', r);
            window.location.href = '{{ route("students.create") }}' + '?' + p.toString();
        }
        searchInput.addEventListener('keypress', e => { if (e.key === 'Enter') applyFilters(); });

        // ── SweetAlert dark theme helper ──
        const swalDark = { background:'#ffffff', color:'#111827', confirmButtonColor:'#2563eb' };

        // ── View Student ──
        function viewStudent(id) {
            Swal.fire({ title:'Loading...', allowOutsideClick:false, showConfirmButton:false, didOpen:()=>Swal.showLoading(), ...swalDark });
            fetch(`/students/${id}`, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'} })
            .then(r=>r.json()).then(data => {
                Swal.close();
                document.getElementById('modalStudentName').textContent = data.name || 'N/A';
                document.getElementById('modalStudentId').textContent   = `Student ID: ${data.id}`;
                document.getElementById('modalName').textContent  = data.name  || 'N/A';
                document.getElementById('modalLrn').textContent   = data.lrn   || 'N/A';
                document.getElementById('modalGrade').textContent  = data.grade || 'N/A';
                document.getElementById('modalRfid').textContent  = data.rfid  || 'Not registered';
                const st = document.getElementById('rfidStatusText');
                st.textContent  = data.rfid ? 'Active & Registered' : 'Not Registered';
                st.style.color  = data.rfid ? '#34d399' : '#f87171';
                window.currentStudentId = data.id;
                new bootstrap.Modal(document.getElementById('studentModal')).show();
            }).catch(() => Swal.fire({ icon:'error', title:'Error', text:'Failed to load student details.', ...swalDark }));
        }

        // ── Edit Student ──
        function editStudent(id) {
            Swal.fire({ title:'Loading...', allowOutsideClick:false, showConfirmButton:false, didOpen:()=>Swal.showLoading(), ...swalDark });
            fetch(`/students/${id}`, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'} })
            .then(r=>r.json()).then(data => {
                Swal.close();
                document.getElementById('editModalStudentId').textContent = `Editing Student ID: ${data.id}`;
                document.getElementById('editName').value  = data.name  || '';
                document.getElementById('editLrn').value   = data.lrn   || '';
                document.getElementById('editGrade').value = data.grade || '';
                document.getElementById('editEmail').value = data.email || '';
                document.getElementById('editRfid').value  = data.rfid  || '';
                document.getElementById('editStudentForm').action = `/students/${id}`;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }).catch(() => Swal.fire({ icon:'error', title:'Error', text:'Failed to load student details.', ...swalDark }));
        }

        document.getElementById('editFromViewModalBtn')?.addEventListener('click', function () {
            if (!window.currentStudentId) return;
            bootstrap.Modal.getInstance(document.getElementById('studentModal')).hide();
            setTimeout(() => editStudent(window.currentStudentId), 300);
        });

        document.getElementById('editStudentForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('saveEditBtn'), orig = btn.innerHTML;
            btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            fetch(this.action, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body:new FormData(this) })
            .then(r=>r.json()).then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    Swal.fire({ icon:'success', title:'Student Updated!', text:'Updated successfully', ...swalDark }).then(() => location.reload());
                } else throw new Error(data.message || 'Update failed');
            }).catch(err => { btn.disabled=false; btn.innerHTML=orig; Swal.fire({ icon:'error', title:'Update Failed', text:err.message, ...swalDark }); });
        });

        // ── Delete Student ──
        function deleteStudent(id) {
            const row  = document.querySelector(`tr[data-student-id="${id}"]`);
            const name = row ? row.querySelector('.fw-semibold').textContent : 'this student';
            Swal.fire({
                title:'Are you sure?',
                html:`<p>Delete: <strong style="color:#f87171;">${name}</strong></p><small style="color:#7a90b8;">This cannot be undone.</small>`,
                icon:'warning', showCancelButton:true,
                confirmButtonText:'Yes, Delete', confirmButtonColor:'#dc2626', cancelButtonColor:'#2563eb',
                ...swalDark
            }).then(r => {
                if (r.isConfirmed) {
                    Swal.fire({ title:'Deleting...', allowOutsideClick:false, showConfirmButton:false, didOpen:()=>Swal.showLoading(), ...swalDark });
                    fetch(`/students/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
                    .then(r=>r.json()).then(data => {
                        if (data.success) { Swal.fire({ icon:'success', title:'Deleted!', text:data.message, timer:1500, showConfirmButton:false, ...swalDark }); if (row) row.remove(); }
                        else throw new Error(data.message || 'Delete failed');
                    }).catch(err => Swal.fire({ icon:'error', title:'Delete Failed', text:err.message, ...swalDark }));
                }
            });
        }

        // ── Update RFID ──
        document.getElementById('updateRfidBtn')?.addEventListener('click', function () {
            if (!window.currentStudentId) return;
            Swal.fire({
                title:'Update RFID',
                html:'<input type="text" id="rfidInput" class="swal2-input" placeholder="Scan or enter RFID" autofocus>',
                showCancelButton:true, confirmButtonText:'Update', ...swalDark,
                preConfirm: () => { const v=document.getElementById('rfidInput').value; if(!v){Swal.showValidationMessage('Enter RFID');return false;} return v; }
            }).then(r => { if (r.isConfirmed) Swal.fire({ icon:'success', title:'RFID Updated!', ...swalDark }).then(() => location.reload()); });
        });

        // ── Add Student ──
        document.addEventListener('DOMContentLoaded', function () {
            const addModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
            document.getElementById('addStudentBtn').addEventListener('click', () => {
                document.getElementById('addStudentForm').reset();
                document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
                document.querySelectorAll('.form-control-modal').forEach(el => el.style.borderColor = '');
                addModal.show();
            });

            document.getElementById('addStudentForm').addEventListener('submit', function (e) {
                e.preventDefault();
                // Clear previous errors
                document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
                document.querySelectorAll('.form-control-modal').forEach(el => el.style.borderColor = '');

                const btn = document.getElementById('saveAddBtn'), orig = btn.innerHTML;
                btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                fetch(this.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(this)
                })
                .then(r => r.json()).then(data => {
                    btn.disabled = false; btn.innerHTML = orig;
                    if (data.success || data.id) {
                        addModal.hide();
                        Swal.fire({ icon:'success', title:'Student Added!', text: data.message || 'Student registered successfully.', ...swalDark })
                            .then(() => location.reload());
                    } else if (data.errors) {
                        // Show field-level validation errors
                        Object.entries(data.errors).forEach(([field, msgs]) => {
                            const errEl = document.getElementById(`add${field.charAt(0).toUpperCase()+field.slice(1)}Error`);
                            const inputEl = document.getElementById(`add${field.charAt(0).toUpperCase()+field.slice(1)}`);
                            if (errEl) errEl.textContent = msgs[0];
                            if (inputEl) inputEl.style.borderColor = 'rgba(239,68,68,0.5)';
                        });
                    } else {
                        throw new Error(data.message || 'Failed to add student');
                    }
                }).catch(err => {
                    btn.disabled = false; btn.innerHTML = orig;
                    Swal.fire({ icon:'error', title:'Error', text: err.message, ...swalDark });
                });
            });
        });

        // ── Batch Select / Delete ──
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll   = document.getElementById('selectAll');
            const toolbar     = document.getElementById('batchToolbar');
            const countEl     = document.getElementById('selectedCount');

            function getChecked() {
                return [...document.querySelectorAll('.row-checkbox:checked')];
            }

            function updateToolbar() {
                const checked = getChecked();
                if (checked.length > 0) {
                    toolbar.style.display = 'block';
                    countEl.textContent = checked.length;
                } else {
                    toolbar.style.display = 'none';
                }
                // Highlight selected rows
                document.querySelectorAll('.student-row').forEach(row => {
                    const cb = row.querySelector('.row-checkbox');
                    row.classList.toggle('selected', cb && cb.checked);
                });
                // Sync select-all indeterminate state
                const all = document.querySelectorAll('.row-checkbox');
                selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
                selectAll.checked = all.length > 0 && checked.length === all.length;
            }

            // Select All
            selectAll?.addEventListener('change', function () {
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                updateToolbar();
            });

            // Individual checkboxes
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', updateToolbar);
            });

            // Cancel batch
            document.getElementById('batchCancelBtn')?.addEventListener('click', () => {
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                selectAll.checked = false;
                selectAll.indeterminate = false;
                updateToolbar();
            });

            // Batch Delete
            document.getElementById('batchDeleteBtn')?.addEventListener('click', () => {
                const ids = getChecked().map(cb => cb.value);
                if (!ids.length) return;

                Swal.fire({
                    title: 'Delete Selected Students?',
                    html: `<p>You are about to delete <strong style="color:#dc2626;">${ids.length}</strong> student(s).</p><small style="color:#6b7280;">This action cannot be undone.</small>`,
                    icon: 'warning', showCancelButton: true,
                    confirmButtonText: `Delete ${ids.length} Student(s)`,
                    confirmButtonColor: '#dc2626', cancelButtonColor: '#2563eb',
                    ...swalDark
                }).then(result => {
                    if (!result.isConfirmed) return;

                    Swal.fire({ title:'Deleting...', allowOutsideClick:false, showConfirmButton:false, didOpen:()=>Swal.showLoading(), ...swalDark });

                    fetch('/students/batch-delete', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ ids })
                    })
                    .then(r => r.json()).then(data => {
                        if (data.success) {
                            // Remove rows from DOM
                            ids.forEach(id => {
                                const row = document.querySelector(`tr[data-student-id="${id}"]`);
                                if (row) row.remove();
                            });
                            toolbar.style.display = 'none';
                            selectAll.checked = false;
                            Swal.fire({ icon:'success', title:'Deleted!', text: data.message || `${ids.length} student(s) deleted.`, timer:1800, showConfirmButton:false, ...swalDark });
                        } else throw new Error(data.message || 'Batch delete failed');
                    }).catch(err => Swal.fire({ icon:'error', title:'Delete Failed', text:err.message, ...swalDark }));
                });
            });
        });

        
        document.addEventListener('DOMContentLoaded', function () {
            const modal   = new bootstrap.Modal(document.getElementById('uploadCsvModal'));
            document.getElementById('uploadCsvBtn').addEventListener('click', () => modal.show());
            document.getElementById('downloadTemplateBtn').addEventListener('click', () => window.location.href = '/students/download-template');

            const csvFile  = document.getElementById('csvFile');
            const uploadBtn = document.getElementById('uploadBtn');

            document.getElementById('browseBtn').addEventListener('click', () => csvFile.click());
            document.getElementById('uploadArea').addEventListener('click', e => {
                if (e.target === document.getElementById('uploadArea') || e.target.closest('.upload-content')) csvFile.click();
            });

            const ua = document.getElementById('uploadArea');
            ua.addEventListener('dragover',  e => { e.preventDefault(); ua.classList.add('drag-over'); });
            ua.addEventListener('dragleave', () => ua.classList.remove('drag-over'));
            ua.addEventListener('drop', e => {
                e.preventDefault(); ua.classList.remove('drag-over');
                const f = e.dataTransfer.files;
                if (f.length > 0 && f[0].name.endsWith('.csv')) { csvFile.files = f; handleFileSelect(f[0]); }
                else Swal.fire({ icon:'error', title:'Invalid File', text:'Please upload a CSV file', ...swalDark });
            });
            csvFile.addEventListener('change', function () { if (this.files.length > 0) handleFileSelect(this.files[0]); });
            document.getElementById('removeFileBtn').addEventListener('click', () => {
                csvFile.value = '';
                document.querySelector('.upload-content').style.display = 'block';
                document.getElementById('selectedFile').style.display   = 'none';
                document.getElementById('previewSection').style.display = 'none';
                uploadBtn.disabled = true;
            });

            function handleFileSelect(file) {
                if (!file.name.endsWith('.csv')) { Swal.fire({ icon:'error', title:'Invalid File', text:'Please upload a CSV file', ...swalDark }); return; }
                if (file.size > 5*1024*1024)    { Swal.fire({ icon:'error', title:'File Too Large', text:'Max 5MB', ...swalDark }); return; }
                document.querySelector('.upload-content').style.display = 'none';
                document.getElementById('selectedFile').style.display   = 'flex';
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = formatSize(file.size);
                uploadBtn.disabled = false;
                previewCSV(file);
            }

            function previewCSV(file) {
                const r = new FileReader();
                r.onload = function (e) {
                    const lines   = e.target.result.split('\n').filter(l => l.trim());
                    if (!lines.length) return;
                    const headers = lines[0].split(',').map(h => h.trim());
                    document.getElementById('previewHeaders').innerHTML = headers.map(h => `<th>${h}</th>`).join('');
                    const body = document.getElementById('previewBody'); body.innerHTML = '';
                    for (let i = 1; i < Math.min(6, lines.length); i++) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = lines[i].split(',').map(c => `<td>${c.trim()}</td>`).join('');
                        body.appendChild(tr);
                    }
                    document.getElementById('previewSection').style.display = 'block';
                    document.getElementById('previewSummary').textContent   = `Total rows: ${lines.length - 1} students`;
                };
                r.readAsText(file);
            }

            function formatSize(b) {
                if (b === 0) return '0 B';
                const k = 1024, s = ['B','KB','MB'], i = Math.floor(Math.log(b)/Math.log(k));
                return Math.round(b/Math.pow(k,i)*100)/100 + ' ' + s[i];
            }

            document.getElementById('uploadCsvForm').addEventListener('submit', function (e) {
                e.preventDefault();
                if (!csvFile.files.length) { Swal.fire({ icon:'warning', title:'No File', text:'Select a CSV file', ...swalDark }); return; }
                const fd = new FormData(); fd.append('csv_file', csvFile.files[0]);
                uploadBtn.disabled = true; uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
                fetch('/students/import-csv', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:fd })
                .then(r=>r.json()).then(data => {
                    uploadBtn.disabled = false; uploadBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Upload & Import';
                    if (data.success) {
                        modal.hide();
                        Swal.fire({ icon:'success', title:'Import Successful!', html:`<p><strong>${data.imported}</strong> students imported</p>`, ...swalDark }).then(() => location.reload());
                    } else Swal.fire({ icon:'error', title:'Import Failed', text:data.message, ...swalDark });
                }).catch(() => {
                    uploadBtn.disabled = false; uploadBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Upload & Import';
                    Swal.fire({ icon:'error', title:'Upload Error', text:'Please try again.', ...swalDark });
                });
            });
        });
    </script>
    <!-- ── ADD STUDENT MODAL ── -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-dark">
                <div class="modal-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon modal-icon-add"><i class="bi bi-person-plus-fill"></i></div>
                        <div>
                            <h5 class="modal-title mb-0">Add New Student</h5>
                            <small style="color:#6b7280;">Fill in the details to register a new student</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                </div>
                <form id="addStudentForm" method="POST" action="{{ route('students.store') ?? '/students' }}">
                    @csrf
                    <div class="modal-body-custom">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-modal">
                                    <label class="form-label-modal"><i class="bi bi-person me-2"></i>Student Name *</label>
                                    <input type="text" class="form-control-modal" id="addName" name="name" placeholder="e.g. Juan dela Cruz" required>
                                    <span class="field-error" id="addNameError"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modal">
                                    <label class="form-label-modal"><i class="bi bi-hash me-2"></i>LRN *</label>
                                    <input type="text" class="form-control-modal" id="addLrn" name="lrn" maxlength="12" placeholder="12-digit LRN" required>
                                    <span class="field-error" id="addLrnError"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modal">
                                    <label class="form-label-modal"><i class="bi bi-mortarboard me-2"></i>Grade Level *</label>
                                    <select class="form-control-modal" id="addGrade" name="grade" required>
                                        <option value="">Select grade level</option>
                                        <option value="Grade 7">Grade 7</option>
                                        <option value="Grade 8">Grade 8</option>
                                        <option value="Grade 9">Grade 9</option>
                                        <option value="Grade 10">Grade 10</option>
                                        <option value="Grade 11">Grade 11</option>
                                        <option value="Grade 12">Grade 12</option>
                                    </select>
                                    <span class="field-error" id="addGradeError"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modal">
                                    <label class="form-label-modal"><i class="bi bi-envelope me-2"></i>Email Address</label>
                                    <input type="email" class="form-control-modal" id="addEmail" name="email" placeholder="Optional">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-modal">
                                    <label class="form-label-modal"><i class="bi bi-credit-card me-2"></i>RFID Number</label>
                                    <div class="input-group-modal">
                                        <input type="text" class="form-control-modal" id="addRfid" name="rfid" placeholder="Scan or enter RFID (Optional)">
                                        <button class="btn-scan" type="button" id="addScanRfidBtn"><i class="bi bi-upc-scan me-1"></i>Scan Card</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                        <button type="submit" class="btn-modal-primary" id="saveAddBtn"><i class="bi bi-person-check-fill me-2"></i>Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection