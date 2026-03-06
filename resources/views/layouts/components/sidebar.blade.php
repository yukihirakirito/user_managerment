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
                    <span class="badge bg-secondary ms-auto">0</span>
                </a>
            </li>

            {{-- Students --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" 
                   href="{{ route('students.index') }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Students</span>
                    <span class="badge bg-info ms-auto">0</span>
                </a>
            </li>

            {{-- Lecturers --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('lecturers.*') ? 'active' : '' }}" 
                   href="{{ route('lecturers.index') }}">
                    <i class="bi bi-book"></i>
                    <span>Lecturers</span>
                    <span class="badge bg-success ms-auto">0</span>
                </a>
            </li>

            {{-- Staff --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" 
                   href="{{ route('staff.index') }}">
                    <i class="bi bi-briefcase"></i>
                    <span>Staff</span>
                    <span class="badge bg-warning text-dark ms-auto">0</span>
                </a>
            </li>

            {{-- Reports Section --}}
            <li class="nav-item mt-3">
                <span class="nav-section-title">Reports</span>
            </li>

            {{-- Analytics --}}
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-bar-chart"></i>
                    <span>Analytics</span>
                </a>
            </li>

            {{-- Export --}}
            <li class="nav-item">
                <a class="nav-link" href="#">
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
                <a class="nav-link" href="#">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>

            {{-- Help & Support --}}
            <li class="nav-item">
                <a class="nav-link" href="#">
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
    background-color: #ffffff;
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
</style>