@extends('admin_panel.layout.app')

@section('title', 'Activity Logs - Staff & Branch Monitoring')

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            {{-- Header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="fas fa-shield-alt text-primary me-2"></i>Staff & Branch Activity Logs
                    </h4>
                    <p class="text-muted mb-0 small">
                        @if($isSuperAdmin)
                            Enterprise-wide monitoring across all branches and staff users.
                        @else
                            Branch-scoped monitoring of all staff activities under your branch.
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('activity.logs.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </a>
                </div>
            </div>

            {{-- KPI Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                                <i class="fas fa-list-check fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">TOTAL LOGS</div>
                                <div class="fs-4 fw-bold">{{ number_format($totalLogs) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                                <i class="fas fa-bolt fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">TODAY'S ACTIVITIES</div>
                                <div class="fs-4 fw-bold text-success">{{ number_format($todayLogs) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-info bg-opacity-10 text-info p-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">ACTIVE STAFF TODAY</div>
                                <div class="fs-4 fw-bold text-info">{{ number_format($activeUsersToday) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                                <i class="fas fa-pen-to-square fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">EDITS & DELETIONS</div>
                                <div class="fs-4 fw-bold text-warning">{{ number_format($editsAndDeletes) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Staff Performance / Work Summary --}}
            @if($userSummary->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-user-clock text-primary me-2"></i>Staff Activity Summary</h6>
                    <span class="badge bg-light text-dark">Click any user to filter logs</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th class="text-center">Today</th>
                                    <th class="text-center">Total Actions</th>
                                    <th class="text-center">Sales Actions</th>
                                    <th class="text-center">Purchase Actions</th>
                                    <th>Last Active</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userSummary as $us)
                                <tr>
                                    <td class="fw-bold">{{ $us->user_name }}</td>
                                    <td class="text-center"><span class="badge bg-success">{{ (int)$us->today_actions }}</span></td>
                                    <td class="text-center"><span class="badge bg-primary">{{ (int)$us->total_actions }}</span></td>
                                    <td class="text-center">{{ (int)$us->sales_actions }}</td>
                                    <td class="text-center">{{ (int)$us->purchase_actions }}</td>
                                    <td class="text-muted">{{ $us->last_active_at ? \Carbon\Carbon::parse($us->last_active_at)->diffForHumans() : '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('activity.logs.index', array_merge(request()->except('page'), ['user_id' => $us->user_id])) }}" class="btn btn-xs btn-outline-primary py-0 px-2">
                                            View Logs
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Filters --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('activity.logs.index') }}" class="row g-2 align-items-end">
                        @if($isSuperAdmin)
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Branch</label>
                            <select name="branch_id" class="form-select form-select-sm">
                                <option value="all" {{ $selectedBranchId === 'all' ? 'selected' : '' }}>All Branches</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ (string)$selectedBranchId === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Staff / User</label>
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="all">All Users</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Module</label>
                            <select name="module" class="form-select form-select-sm">
                                <option value="all">All Modules</option>
                                @foreach($modules as $m)
                                    <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">From Date</label>
                            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">To Date</label>
                            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Ref, title, user..." value="{{ request('search') }}">
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('activity.logs.index') }}" class="btn btn-sm btn-light border">Reset</a>
                            <button type="submit" class="btn btn-sm btn-primary px-3"><i class="fas fa-filter me-1"></i>Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Detailed Logs Table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Date & Time</th>
                                    <th>User</th>
                                    @if($isSuperAdmin)
                                    <th>Branch</th>
                                    @endif
                                    <th>Module</th>
                                    <th>Activity</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                    <th>IP</th>
                                    <th class="text-center">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td class="text-nowrap">
                                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $log->user_name ?: ($log->user->name ?? 'System') }}</div>
                                        @if($log->user && $log->user->roles->isNotEmpty())
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 10px;">
                                                {{ $log->user->roles->pluck('name')->implode(', ') }}
                                            </span>
                                        @endif
                                    </td>
                                    @if($isSuperAdmin)
                                    <td>
                                        @if($log->branch)
                                            <span class="badge bg-info bg-opacity-10 text-info">{{ $log->branch->name }}</span>
                                        @else
                                            <span class="text-muted">Enterprise / N/A</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <span class="badge bg-light text-dark border text-uppercase">{{ $log->module }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <i class="fas {{ $log->icon ?: 'fa-bell' }} text-{{ $log->badge_color ?: 'primary' }} me-1"></i>
                                            {{ $log->title }}
                                        </div>
                                        <div class="text-muted text-truncate" style="max-width: 320px;">{{ $log->description }}</div>
                                    </td>
                                    <td>
                                        @if($log->reference_no)
                                            <span class="badge bg-light text-primary border">{{ $log->reference_no }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ $log->amount !== null ? 'Rs. ' . number_format($log->amount, 2) : '—' }}
                                    </td>
                                    <td class="text-muted" style="font-size: 11px;">
                                        {{ $log->metadata['ip'] ?? '—' }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary py-0 px-2 btn-view-log"
                                            data-log='@json($log)'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $isSuperAdmin ? 9 : 8 }}" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No activity logs found for the selected filters.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer bg-white">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- Log Detail Modal --}}
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Activity Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="logDetailBody"></div>
            <div class="modal-footer bg-light">
                <a href="#" id="logActionUrlBtn" class="btn btn-primary btn-sm d-none" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>Open Related Record
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.btn-view-log', function() {
        const log = $(this).data('log');
        const meta = log.metadata || {};
        let changesHtml = '';

        if (meta.changes && Object.keys(meta.changes).length > 0) {
            changesHtml += '<h6 class="fw-bold mt-3 mb-2">Changed Fields (Before &rarr; After)</h6>';
            changesHtml += '<div class="table-responsive"><table class="table table-sm table-bordered small mb-0"><thead class="table-light"><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead><tbody>';
            for (const [field, vals] of Object.entries(meta.changes)) {
                const oldVal = vals.old !== null && vals.old !== undefined ? vals.old : '<em>null</em>';
                const newVal = vals.new !== null && vals.new !== undefined ? vals.new : '<em>null</em>';
                changesHtml += `<tr><td class="fw-semibold">${field}</td><td class="text-danger">${oldVal}</td><td class="text-success">${newVal}</td></tr>`;
            }
            changesHtml += '</tbody></table></div>';
        }

        const html = `
            <div class="row g-3 small">
                <div class="col-md-6"><strong>User:</strong> ${log.user_name || 'System'}</div>
                <div class="col-md-6"><strong>Branch:</strong> ${log.branch ? log.branch.name : 'Enterprise / N/A'}</div>
                <div class="col-md-6"><strong>Module:</strong> <span class="text-uppercase">${log.module}</span></div>
                <div class="col-md-6"><strong>Action Type:</strong> ${log.activity_type}</div>
                <div class="col-md-6"><strong>Reference:</strong> ${log.reference_no || '—'}</div>
                <div class="col-md-6"><strong>Amount:</strong> ${log.amount !== null ? 'Rs. ' + Number(log.amount).toLocaleString() : '—'}</div>
                <div class="col-md-6"><strong>IP Address:</strong> ${meta.ip || '—'}</div>
                <div class="col-md-6"><strong>Time:</strong> ${log.created_at}</div>
                <div class="col-12"><strong>Title:</strong> ${log.title}</div>
                <div class="col-12"><strong>Description:</strong> ${log.description || '—'}</div>
                ${meta.user_agent ? `<div class="col-12 text-muted"><strong>Device / Browser:</strong> ${meta.user_agent}</div>` : ''}
            </div>
            ${changesHtml}
        `;

        $('#logDetailBody').html(html);

        if (log.action_url) {
            $('#logActionUrlBtn').attr('href', log.action_url).removeClass('d-none');
        } else {
            $('#logActionUrlBtn').addClass('d-none');
        }

        $('#logDetailModal').modal('show');
    });
</script>
@endsection
