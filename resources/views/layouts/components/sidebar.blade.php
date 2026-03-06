<aside class="sidebar bg-light">
    <nav class="sidebar-nav">
        {{-- Nav Title --}}
        <div class="sidebar-header">
            <span class="text-uppercase text-muted small fw-bold" style="letter-spacing: 0.5px;">
                <i class="bi bi-grid-3x3"></i> Menu
            </span>
        </div>

        {{-- Main Navigation --}}
        <ul class="nav flex-column">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                   href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- User Management Section --}}
            <li class="nav-item mt-3">
                <span class="nav-section-title">User Management</span>
            </li>

            {{-- All Users --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                   href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>All Users</span>
                    <span class="badge bg-secondary ms-auto">{{ \App\Models\User::count() }}</span>
                </a>
            </li>

            {{-- Students --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" 
                   href="{{ route('students.index') }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Students</span>
                    <span class="badge bg-info ms-auto">{{ \App\Models\User::where('user_type', 'student')->count() }}</span>
                </a>
            </li>

            {{-- Lecturers --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('lecturers.*') ? 'active' : '' }}" 
                   href="{{ route('lecturers.index') }}">
                    <i class="bi bi-book"></i>
                    <span>Lecturers</span>
                    <span class="badge bg-success ms-auto">{{ \App\Models\User::where('user_type', 'lecturer')->count() }}</span>
                </a>
            </li>

            {{-- Staff --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" 
                   href="{{ route('staff.index') }}">
                    <i class="bi bi-briefcase"></i>
                    <span>Staff</span>
                    <span class="badge bg-warning text-dark ms-auto">{{ \App\Models\User::where('user_type', 'staff')->count() }}</span>
                </a>
            </li>

            {{-- Reports Section --}}
            <li class="nav-item mt-3">
                <span class="nav-section-title">Reports</span>
            </li>

            {{-- Analytics --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.analytics') ? 'active' : '' }}" 
                   href="{{ route('reports.analytics') }}"
                   title="View system analytics and statistics">
                    <i class="bi bi-bar-chart"></i>
                    <span>Analytics</span>
                </a>
            </li>

            {{-- Activity Log --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.activity') ? 'active' : '' }}" 
                   href="{{ route('reports.activity') }}"
                   title="View user activity logs">
                    <i class="bi bi-clock-history"></i>
                    <span>Activity Log</span>
                </a>
            </li>

            {{-- Export Data --}}
            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#exportModal"
                   title="Export data to CSV or Excel">
                    <i class="bi bi-download"></i>
                    <span>Export Data</span>
                </a>
            </li>

            {{-- Settings Section --}}
            <li class="nav-item mt-3">
                <span class="nav-section-title">Settings</span>
            </li>

            {{-- System Settings --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" 
                    title="Configure system settings">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>

            {{-- Help & Support --}}
            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#helpModal"
                   title="Get help and support">
                    <i class="bi bi-question-circle"></i>
                    <span>Help & Support</span>
                </a>
            </li>
        </ul>
    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer border-top pt-3">
        <div class="px-3">
            <div class="d-grid gap-2">
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Add User
                </a>
            </div>
        </div>
    </div>
</aside>

{{-- Export Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="bi bi-download me-2"></i>Export Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Select what you want to export:</p>
                
                <div class="d-flex flex-column gap-2">
                    {{-- Export Users CSV --}}
                    <a href="" 
                       class="btn btn-outline-primary justify-content-start">
                        <i class="bi bi-file-earmark-csv me-2"></i>
                        <div class="text-start">
                            <strong>Users (CSV)</strong>
                            <small class="text-muted d-block">All user records in CSV format</small>
                        </div>
                    </a>

                    {{-- Export Users Excel --}}
                    <a href="" 
                       class="btn btn-outline-success justify-content-start">
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        <div class="text-start">
                            <strong>Users (Excel)</strong>
                            <small class="text-muted d-block">All user records in Excel format</small>
                        </div>
                    </a>

                    {{-- Export Students --}}
                    <a href="" 
                       class="btn btn-outline-info justify-content-start">
                        <i class="bi bi-mortarboard me-2"></i>
                        <div class="text-start">
                            <strong>Students Report</strong>
                            <small class="text-muted d-block">All student records with details</small>
                        </div>
                    </a>

                    {{-- Export Lecturers --}}
                    <a href="" 
                       class="btn btn-outline-success justify-content-start">
                        <i class="bi bi-book me-2"></i>
                        <div class="text-start">
                            <strong>Lecturers Report</strong>
                            <small class="text-muted d-block">All lecturer records with details</small>
                        </div>
                    </a>

                    {{-- Export Staff --}}
                    <a href="" 
                       class="btn btn-outline-warning justify-content-start">
                        <i class="bi bi-briefcase me-2"></i>
                        <div class="text-start">
                            <strong>Staff Report</strong>
                            <small class="text-muted d-block">All staff records with details</small>
                        </div>
                    </a>

                    {{-- Export Activity Log --}}
                    <a href="" 
                       class="btn btn-outline-secondary justify-content-start">
                        <i class="bi bi-clock-history me-2"></i>
                        <div class="text-start">
                            <strong>Activity Log</strong>
                            <small class="text-muted d-block">System activity records</small>
                        </div>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Help & Support Modal --}}
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">
                    <i class="bi bi-question-circle me-2"></i>Help & Support
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="helpAccordion">
                    {{-- Getting Started --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingGettingStarted">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapseGettingStarted" aria-expanded="true" 
                                    aria-controls="collapseGettingStarted">
                                <i class="bi bi-play-circle me-2"></i>Getting Started
                            </button>
                        </h2>
                        <div id="collapseGettingStarted" class="accordion-collapse collapse show" 
                             aria-labelledby="headingGettingStarted" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p><strong>Welcome to User Management System!</strong></p>
                                <ul>
                                    <li>Create new users from the "Add User" button</li>
                                    <li>Manage users by type: Students, Lecturers, Staff</li>
                                    <li>Filter and search users by name, email, or phone</li>
                                    <li>View detailed user profiles and activity</li>
                                    <li>Export data for reports</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- User Management --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingUserManagement">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapseUserManagement" aria-expanded="false" 
                                    aria-controls="collapseUserManagement">
                                <i class="bi bi-people me-2"></i>User Management
                            </button>
                        </h2>
                        <div id="collapseUserManagement" class="accordion-collapse collapse" 
                             aria-labelledby="headingUserManagement" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p><strong>How to manage users:</strong></p>
                                <ul>
                                    <li><strong>Create User:</strong> Click "Add User" button and fill in the form</li>
                                    <li><strong>Edit User:</strong> Click the edit icon in the users list</li>
                                    <li><strong>View Details:</strong> Click on user name to see full profile</li>
                                    <li><strong>Delete User:</strong> Click delete icon with confirmation</li>
                                    <li><strong>Change Status:</strong> Update user status (Active/Inactive/Suspended)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Reports & Analytics --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingReports">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapseReports" aria-expanded="false" 
                                    aria-controls="collapseReports">
                                <i class="bi bi-bar-chart me-2"></i>Reports & Analytics
                            </button>
                        </h2>
                        <div id="collapseReports" class="accordion-collapse collapse" 
                             aria-labelledby="headingReports" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p><strong>View system analytics:</strong></p>
                                <ul>
                                    <li><strong>Analytics:</strong> View user statistics and charts</li>
                                    <li><strong>Activity Log:</strong> Track all user actions in the system</li>
                                    <li><strong>Export Data:</strong> Download reports in CSV or Excel format</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Troubleshooting --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTroubleshoot">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapseTroubleshoot" aria-expanded="false" 
                                    aria-controls="collapseTroubleshoot">
                                <i class="bi bi-tools me-2"></i>Troubleshooting
                            </button>
                        </h2>
                        <div id="collapseTroubleshoot" class="accordion-collapse collapse" 
                             aria-labelledby="headingTroubleshoot" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p><strong>Common issues and solutions:</strong></p>
                                <ul>
                                    <li><strong>Can't create user:</strong> Check all required fields are filled</li>
                                    <li><strong>Email already exists:</strong> Use a unique email address</li>
                                    <li><strong>User locked out:</strong> Contact administrator</li>
                                    <li><strong>Export not working:</strong> Ensure you have proper permissions</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Support --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingContact">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapseContact" aria-expanded="false" 
                                    aria-controls="collapseContact">
                                <i class="bi bi-envelope me-2"></i>Contact Support
                            </button>
                        </h2>
                        <div id="collapseContact" class="accordion-collapse collapse" 
                             aria-labelledby="headingContact" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <p><strong>Need more help?</strong></p>
                                <ul>
                                    <li><strong>Email:</strong> support@example.com</li>
                                    <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                                    <li><strong>Documentation:</strong> <a href="#" target="_blank">View Docs</a></li>
                                    <li><strong>Report Bug:</strong> <a href="#" target="_blank">Submit Issue</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar {
    position: fixed;
    left: 0;
    top: 56px;
    bottom: 0;
    width: 100%;
    max-width: 250px;
    overflow-y: auto;
    border-right: 1px solid #e5e7eb;
    background-color: #f9fafb;
    z-index: 1000;
    transition: transform 0.3s ease;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}

.sidebar-nav {
    padding: 1.5rem 0;
}

.sidebar-header {
    padding: 0.5rem 1.5rem;
    margin-bottom: 1rem;
}

.nav-section-title {
    display: block;
    padding: 0.5rem 1.5rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.5rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    margin: 0.25rem 0;
    gap: 0.75rem;
}

.nav-link:hover {
    background-color: rgba(59, 130, 246, 0.05);
    color: #3b82f6;
    border-left-color: #3b82f6;
}

.nav-link.active {
    background-color: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border-left-color: #3b82f6;
    font-weight: 500;
}

.nav-link i {
    font-size: 1.1rem;
    min-width: 1.5rem;
    text-align: center;
}

.nav-link .badge {
    margin-left: auto;
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: white;
    padding: 1rem 0;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.modal-content {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover,
.btn-outline-warning:hover,
.btn-outline-secondary:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

.accordion-button {
    padding: 1rem;
    border: none;
}

.accordion-button:not(.collapsed) {
    background-color: rgba(59, 130, 246, 0.05);
    color: #3b82f6;
}

.accordion-button:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
}
</style>