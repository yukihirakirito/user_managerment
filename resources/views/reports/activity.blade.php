@extends('layouts.app')

@section('title', 'Activity Log')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Activity Log</li>
@endsection

@section('page-header')
    <div>
        <h1 class="h2 fw-bold mb-0">Activity Log</h1>
        <p class="text-muted mb-0">Track all system activities</p>
    </div>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('reports.activity') }}" method="GET" class="d-flex gap-2 flex-wrap">
                    <input type="text" name="search" class="form-control" placeholder="Search..." 
                           value="{{ request('search') }}" style="flex: 1; min-width: 200px;">
                    
                    <select name="action" class="form-select" style="flex: 0 0 150px;">
                        <option value="">All Actions</option>
                        <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                    
                    <a href="{{ route('reports.activity') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </form>
            </div>
        </div>
    </div>

    {{-- Activity Summary Cards --}}
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Created</p>
                        <h3 class="fw-bold mb-0">{{ $activitySummary['created'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-plus-circle text-success" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Updated</p>
                        <h3 class="fw-bold mb-0">{{ $activitySummary['updated'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-pencil-square text-info" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Deleted</p>
                        <h3 class="fw-bold mb-0">{{ $activitySummary['deleted'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-trash text-danger" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Login</p>
                        <h3 class="fw-bold mb-0">{{ $activitySummary['login'] ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-box-arrow-in-right text-primary" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Activity Logs
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>IP Address</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs ?? [] as $log)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        {{ $log->created_at->format('M d, Y H:i:s') }}
                                    </small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="https://ui-avatars.com/api/?name={{ $log->user->name }}" 
                                                 alt="avatar" class="rounded-circle" width="32" height="32">
                                            <div>
                                                <p class="mb-0"><strong>{{ $log->user->name }}</strong></p>
                                                <small class="text-muted">{{ $log->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->action_color ?? 'secondary' }}">
                                        <i class="bi {{ $log->action_icon ?? 'bi-info-circle' }} me-1"></i>
                                        {{ $log->action_text ?? ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->model)
                                        <span class="badge bg-light text-dark">
                                            {{ class_basename($log->model) }}
                                            @if($log->model_id)
                                                <span class="ms-1">#{{ $log->model_id }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $log->ip_address ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($log->changes)
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailsModal{{ $log->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>

                            @if($log->changes)
                                <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Activity Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-3"><strong>Changes:</strong></p>
                                                <pre class="bg-light p-3 rounded" style="font-size: 0.85rem; overflow-x: auto;">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="mt-3">No activity logs found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs && $logs->hasPages())
                <div class="card-footer bg-light">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection