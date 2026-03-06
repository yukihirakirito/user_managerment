@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<style>
.alert {
    border: none;
    border-left: 4px solid transparent;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background-color: #f0fdf4;
    border-left-color: #10b981;
    color: #065f46;
}

.alert-danger {
    background-color: #fef2f2;
    border-left-color: #ef4444;
    color: #7f1d1d;
}

.alert-warning {
    background-color: #fffbeb;
    border-left-color: #f59e0b;
    color: #78350f;
}

.alert-info {
    background-color: #f0f9ff;
    border-left-color: #0ea5e9;
    color: #0c4a6e;
}

.alert ul {
    padding-left: 1.5rem;
}

.alert li {
    margin-bottom: 0.5rem;
}

.alert i {
    font-size: 1.1rem;
}

.btn-close {
    opacity: 0.7;
}

.btn-close:hover {
    opacity: 1;
}
</style>