@extends('admin_panel.layout.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-primary"></i> Taxation & Tax Types
            </h3>
            <p class="text-muted mb-0 small">
                Manage tax schedules (Exempt, 3rd Schedule Retail MRP, Non-3rd Schedule Trade, Sales Tax & WHT rules).
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('Purchase.home') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-shopping-cart me-1"></i> Purchases
            </a>
            <a href="{{ route('product') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-box me-1"></i> Products
            </a>
            <a href="{{ route('tax-types.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold">
                <i class="fas fa-plus-circle me-1"></i> Add Tax Type
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-xs border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-xs border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-primary">
                <span class="text-muted small fw-semibold">Total Tax Types</span>
                <h4 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total'] }}</h4>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-success">
                <span class="text-muted small fw-semibold">Active Types</span>
                <h4 class="fw-bold text-success mb-0 mt-1">{{ $stats['active'] }}</h4>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-warning">
                <span class="text-muted small fw-semibold">3rd Schedule (Retail / MRP)</span>
                <h4 class="fw-bold text-warning mb-0 mt-1">{{ $stats['third_schedule'] }}</h4>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-info">
                <span class="text-muted small fw-semibold">Standard (Trade Value)</span>
                <h4 class="fw-bold text-info mb-0 mt-1">{{ $stats['standard'] }}</h4>
            </div>
        </div>
        <div class="col-xl-2 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-4 border-secondary">
                <span class="text-muted small fw-semibold">Exempt Items</span>
                <h4 class="fw-bold text-secondary mb-0 mt-1">{{ $stats['exempt'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="fw-bold text-dark mb-0">Configured Tax Schedules</h5>
            <form action="{{ route('tax-types.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code or title..." class="form-control form-control-sm" style="width: 220px;">
                <select name="tax_applied_on" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                    <option value="">All Applied Bases</option>
                    <option value="trade_value" {{ request('tax_applied_on') == 'trade_value' ? 'selected' : '' }}>Trade Value</option>
                    <option value="retail_value" {{ request('tax_applied_on') == 'retail_value' ? 'selected' : '' }}>Retail Value (3rd Sched)</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i></button>
                @if(request()->hasAny(['search', 'tax_applied_on']))
                    <a href="{{ route('tax-types.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters"><i class="fas fa-times"></i></a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3" style="width: 100px;">Tax Code</th>
                        <th>Tax Type Title</th>
                        <th>Tax Applied On</th>
                        <th>Purchase Sales Tax</th>
                        <th>Sale Sales Tax</th>
                        <th>Un-Reg / Further</th>
                        <th>Adv. Tax (WHT)</th>
                        <th>Applied From</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th class="text-end pe-3" style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($taxTypes as $t)
                        <tr class="{{ $t->is_default ? 'bg-light bg-opacity-50' : '' }}">
                            <td class="ps-3 font-monospace fw-bold text-primary">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-1 px-2 font-monospace">
                                    {{ $t->code }}
                                </span>
                                @if($t->is_default)
                                    <span class="badge bg-warning text-dark border ms-1" style="font-size: 9px;" title="Default for new products">Default</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $t->title }}</span>
                                @if($t->notes)
                                    <small class="text-muted d-block text-truncate" style="max-width: 260px;" title="{{ $t->notes }}">{{ $t->notes }}</small>
                                @endif
                            </td>
                            <td>
                                @if($t->isThirdSchedule())
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-1 px-2">
                                        <i class="fas fa-tag me-1"></i> Retail Value (3rd Sched)
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle py-1 px-2">
                                        <i class="fas fa-boxes me-1"></i> Trade Value
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($t->st_purchase_active)
                                    <span class="fw-bold text-dark">{{ number_format($t->st_purchase_rate, 2) }}%</span>
                                    @if($t->mrp_tax_active)
                                        <small class="text-danger d-block" style="font-size: 10px;">(MRP Tax {{ number_format($t->mrp_tax_rate, 2) }}%)</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">0% (Exempt)</span>
                                @endif
                            </td>
                            <td>
                                @if($t->st_sale_active)
                                    <span class="fw-bold text-dark">{{ number_format($t->st_sale_rate, 2) }}%</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">0% / None</span>
                                @endif
                            </td>
                            <td>
                                @if($t->unreg_st_active)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2">
                                        +{{ number_format($t->unreg_st_rate, 2) }}% ({{ ucfirst($t->unreg_st_applied_on) }})
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-muted">Purch:</span> <strong>{{ $t->adv_tax_purchase_active ? number_format($t->adv_tax_purchase_rate, 2) . '%' : '0%' }}</strong><br>
                                    <span class="text-muted">Sale:</span> <span class="text-success">{{ number_format($t->adv_tax_sale_filer_rate, 2) }}%</span> / <span class="text-danger">{{ number_format($t->adv_tax_sale_non_filer_rate, 2) }}%</span>
                                </div>
                            </td>
                            <td class="small text-muted">
                                {{ $t->applied_from ? $t->applied_from->format('d/m/Y') : '—' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $t->products_count ?? 0 }} items</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch" data-id="{{ $t->id }}" {{ $t->status ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('tax-types.edit', $t->id) }}" class="btn btn-outline-warning btn-xs py-1 px-2 rounded" title="Edit Tax Type">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(($t->products_count ?? 0) === 0 && ($t->purchase_items_count ?? 0) === 0 && !$t->is_default)
                                        <form action="{{ route('tax-types.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete Tax Type [{{ $t->code }}]?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-xs py-1 px-2 rounded" title="Delete Tax Type">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x text-light mb-3"></i>
                                <p class="mb-0">No tax types found. Click <strong>Add Tax Type</strong> to create one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    $(document).on('change', '.status-toggle', function() {
        const id = $(this).data('id');
        const isChecked = $(this).is(':checked');
        $.ajax({
            url: `/tax-types/${id}/toggle-status`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    toastr ? toastr.success(res.message) : console.log(res.message);
                }
            },
            error: function() {
                alert('Failed to update status');
            }
        });
    });
</script>
@endpush
@endsection
