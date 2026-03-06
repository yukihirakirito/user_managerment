<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid px-4">
        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 me-2 fs-5"></i>
            <span class="fw-bold text-uppercase" style="letter-spacing: 0.5px;">UMS</span>
            <span class="badge bg-primary ms-2">Pro</span>
        </a>

        {{-- Navbar Toggler (Mobile) --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Navbar Content --}}
        <div class="collapse navbar-collapse" id="navbarContent">
            {{-- Search Bar --}}
            <div class="ms-auto me-4 d-none d-lg-block" style="max-width: 300px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search" class="form-control border-0 bg-white" 
                           placeholder="Search users..." aria-label="Search">
                </div>
            </div>

            {{-- Right Side: Icons & Dropdown --}}
            <ul class="navbar-nav ms-auto gap-3">
                {{-- Notifications --}}
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="transform: translate(-50%, -50%);">
                            3
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">New user registered</a></li>
                        <li><a class="dropdown-item" href="#">System update completed</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-primary" href="#">View all</a></li>
                    </ul>
                </li>

                {{-- User Menu --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=3b82f6&color=fff" 
                             alt="User avatar" class="rounded-circle" width="32" height="32">
                        <span class="d-none d-sm-inline text-truncate">Admin User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-person me-2"></i>Profile
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.navbar-brand {
    font-size: 1.1rem;
    letter-spacing: 1px;
}

.input-group-text {
    border-right: none !important;
}

.input-group .form-control {
    border-left: none !important;
}

.input-group .form-control:focus {
    border-color: #e5e7eb;
    box-shadow: none;
    background-color: white;
}

.dropdown-menu {
    border: none;
    border-radius: 8px;
    min-width: 250px;
}
</style>