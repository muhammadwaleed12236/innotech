@extends('admin_panel.layout.app')

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">

            <!-- Page Title & Actions -->
            <div class="page-header row align-items-center mb-3">
                <div class="page-title col-md-6">
                    <h4 class="mb-1"><i class="fas fa-money-check-alt text-primary me-2"></i> Cheque Management</h4>
                    <p class="text-muted mb-0">Track received cheques, due date alerts & automated bank clearance</p>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <a href="{{ route('customer.payments') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Customer Payments
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#receiveChequeModal">
                        <i class="fas fa-plus me-1"></i> Receive New Cheque
                    </button>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-check-circle me-1"></i> Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Daily Alerts & Stat Cards -->
            <div class="row g-3 mb-4">
                <!-- In Hand -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm border-start border-4 border-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">In-Hand (Tijori/Drawer)</span>
                                    <h4 class="mb-0 mt-1 fw-bold text-warning">{{ $stats['in_hand_count'] }} <small class="fs-6 text-muted">Cheques</small></h4>
                                    <div class="small text-muted mt-1">Rs. {{ number_format($stats['in_hand_amount'], 2) }}</div>
                                </div>
                                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                                    <i class="fas fa-wallet fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Due Today -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm border-start border-4 border-info h-100 {{ $stats['due_today_count'] > 0 ? 'bg-light-warning' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Due Today (Bank Mein Jama)</span>
                                    <h4 class="mb-0 mt-1 fw-bold text-info">{{ $stats['due_today_count'] }} <small class="fs-6 text-muted">Cheques</small></h4>
                                    <div class="small text-muted mt-1">Rs. {{ number_format($stats['due_today_amount'], 2) }}</div>
                                </div>
                                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                                    <i class="fas fa-calendar-day fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overdue -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm border-start border-4 border-danger h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Overdue Cheques</span>
                                    <h4 class="mb-0 mt-1 fw-bold text-danger">{{ $stats['overdue_count'] }} <small class="fs-6 text-muted">Cheques</small></h4>
                                    <div class="small text-muted mt-1">Rs. {{ number_format($stats['overdue_amount'], 2) }}</div>
                                </div>
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cleared in Bank -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Cleared (Bank Received)</span>
                                    <h4 class="mb-0 mt-1 fw-bold text-success">{{ $stats['cleared_count'] }} <small class="fs-6 text-muted">Cheques</small></h4>
                                    <div class="small text-muted mt-1">Rs. {{ number_format($stats['cleared_amount'], 2) }}</div>
                                </div>
                                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                    <i class="fas fa-check-double fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Tabs -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-2">
                    <div class="row align-items-center g-2">
                        <div class="col-md-8">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link {{ $status === 'all' && $filterDue === 'all' ? 'active' : '' }}" href="{{ route('cheques.index') }}">
                                        All Cheques
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $status === 'in_hand' ? 'active' : '' }}" href="{{ route('cheques.index', ['status' => 'in_hand']) }}">
                                        In-Hand <span class="badge bg-secondary ms-1">{{ $stats['in_hand_count'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $filterDue === 'today' ? 'active' : '' }}" href="{{ route('cheques.index', ['due' => 'today']) }}">
                                        Due Today <span class="badge bg-warning text-dark ms-1">{{ $stats['due_today_count'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $filterDue === 'upcoming' ? 'active' : '' }}" href="{{ route('cheques.index', ['due' => 'upcoming']) }}">
                                        Upcoming (3 Days) <span class="badge bg-info text-dark ms-1">{{ $stats['upcoming_count'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $filterDue === 'pdc' ? 'active' : '' }}" href="{{ route('cheques.index', ['due' => 'pdc']) }}">
                                        PDC (Post-Dated) <span class="badge bg-primary ms-1">{{ $stats['pdc_count'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $filterDue === 'overdue' ? 'active' : '' }}" href="{{ route('cheques.index', ['due' => 'overdue']) }}">
                                        Overdue <span class="badge bg-danger ms-1">{{ $stats['overdue_count'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $status === 'cleared' ? 'active' : '' }}" href="{{ route('cheques.index', ['status' => 'cleared']) }}">
                                        Cleared <span class="badge bg-success ms-1">{{ $stats['cleared_count'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $status === 'bounced' ? 'active' : '' }}" href="{{ route('cheques.index', ['status' => 'bounced']) }}">
                                        Bounced <span class="badge bg-danger ms-1">{{ $stats['bounced_count'] }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('cheques.index') }}" method="GET">
                                @if($status !== 'all') <input type="hidden" name="status" value="{{ $status }}"> @endif
                                @if($filterDue !== 'all') <input type="hidden" name="due" value="{{ $filterDue }}"> @endif
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Cheque No, Bank, Party..." value="{{ request('search') }}">
                                    <button class="btn btn-sm btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cheques Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Cheque No</th>
                                    <th>Customer / Party</th>
                                    <th>Customer Bank</th>
                                    <th>Due / Maturity Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Deposit / Cleared To</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cheques as $key => $c)
                                <tr>
                                    <td>{{ $cheques->firstItem() + $key }}</td>
                                    <td>
                                        <span class="fw-bold font-monospace text-primary">
                                            <i class="fas fa-money-check me-1"></i> #{{ $c->cheque_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $c->customer->customer_name ?? 'N/A' }}</strong>
                                        @if($c->note)
                                            <div class="small text-muted">{{ Str::limit($c->note, 30) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-university me-1 text-secondary"></i> {{ $c->bank_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $c->cheque_date ? $c->cheque_date->format('d M, Y') : '-' }}</div>
                                        @php $badge = $c->due_badge; @endphp
                                        <span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold fs-6 text-dark">Rs. {{ number_format($c->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($c->status === 'in_hand')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-hand-holding-usd me-1"></i> In-Hand</span>
                                        @elseif($c->status === 'cleared')
                                            @php
                                                $isCashAcc = $c->depositAccount && stripos($c->depositAccount->title, 'cash') !== false;
                                            @endphp
                                            <span class="badge bg-success">
                                                <i class="fas {{ $isCashAcc ? 'fa-money-bill-wave' : 'fa-check-circle' }} me-1"></i> 
                                                {{ $isCashAcc ? 'Encashed (Cash)' : 'Cleared (Bank)' }}
                                            </span>
                                            <div class="small text-muted mt-1">{{ $c->cleared_at ? $c->cleared_at->format('d M, Y') : '' }}</div>
                                        @elseif($c->status === 'bounced')
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Bounced</span>
                                            @if($c->bounce_reason)
                                                <div class="small text-danger mt-1">{{ $c->bounce_reason }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($c->depositAccount)
                                            <span class="text-success small fw-bold">
                                                @if(stripos($c->depositAccount->title, 'cash') !== false)
                                                    <i class="fas fa-money-bill-wave me-1 text-success"></i> {{ $c->depositAccount->title }}
                                                @else
                                                    <i class="fas fa-university me-1 text-primary"></i> {{ $c->depositAccount->title }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted small">In Hand</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($c->status !== 'cleared')
                                            <!-- Mark as Cleared / Encash Button -->
                                            <button class="btn btn-sm btn-success me-1" title="Clear in Bank or Encash to Cash" 
                                                    onclick="openClearModal('{{ $c->id }}', '{{ $c->cheque_no }}', '{{ $c->amount }}', '{{ $c->bank_name }}')">
                                                <i class="fas fa-check-double me-1"></i> Clear / Cash
                                            </button>

                                            <!-- Mark as Bounced Button -->
                                            <button class="btn btn-sm btn-outline-danger me-1" title="Mark Bounced"
                                                    onclick="openBounceModal('{{ $c->id }}', '{{ $c->cheque_no }}', '{{ $c->amount }}')">
                                                <i class="fas fa-times me-1"></i> Bounce
                                            </button>
                                        @else
                                            <span class="badge bg-light text-success border border-success me-1">
                                                <i class="fas fa-check-double"></i> Realized
                                            </span>
                                        @endif

                                        <form action="{{ route('cheques.destroy', $c->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure to delete this cheque record?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-2 d-block text-secondary"></i>
                                        No cheques found for this filter.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $cheques->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal: Receive Cheque (Simplified 4 Fields: No, Bank Name, Date, Amount) -->
<div class="modal fade" id="receiveChequeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('cheques.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-money-check me-1 text-primary"></i> Receive New Cheque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer / Party <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cheque Number <span class="text-danger">*</span></label>
                            <input type="text" name="cheque_no" class="form-control" placeholder="e.g. 1029384" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Customer Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control" placeholder="e.g. Meezan Bank, HBL, MCB" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cheque / Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="cheque_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            <small class="text-muted">Jis din bank mein lagega</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Amount (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Note / Reference</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Cheque</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Mark as Cleared in Bank or Encashed to Cash -->
<div class="modal fade" id="clearChequeModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="clearChequeForm" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-double me-2"></i> Clear / Encash Cheque</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Cheque:</strong> <span id="clear_cheque_title">-</span>
                            </div>
                            <div class="text-end">
                                <strong>Amount:</strong> <span class="fs-6 fw-bold text-success">Rs. <span id="clear_cheque_amount">0.00</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deposit / Encash Into <span class="text-danger">*</span></label>
                        <select name="deposit_account_id" class="form-select" required>
                            <option value="">-- Choose Account (Bank or Cash) --</option>
                            @if(isset($cashAccounts) && $cashAccounts->isNotEmpty())
                                <optgroup label="💵 Cash Accounts (Encash to Cash in Hand)">
                                    @foreach($cashAccounts as $acc)
                                        <option value="{{ $acc->id }}">💵 {{ $acc->title }} (Current Bal: Rs. {{ number_format($acc->current_balance ?? 0, 2) }})</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if(isset($bankAccounts) && $bankAccounts->isNotEmpty())
                                <optgroup label="🏦 Bank Accounts (Deposit in Bank)">
                                    @foreach($bankAccounts as $acc)
                                        <option value="{{ $acc->id }}">🏦 {{ $acc->title }} (Current Bal: Rs. {{ number_format($acc->current_balance ?? 0, 2) }})</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i> Cheque bank account mein jama karein ya counter se <strong>Cash in Hand</strong> nikalwayein (Selected account ka balance barh jayega).
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Clearance / Encashed Date <span class="text-danger">*</span></label>
                        <input type="date" name="cleared_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Clearing Remarks / Reference</label>
                        <input type="text" name="clearing_note" class="form-control" placeholder="Optional deposit slip no, counter receipt or transaction ID">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Confirm & Update Balance
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Mark as Bounced -->
<div class="modal fade" id="bounceChequeModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="bounceChequeForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-1"></i> Mark Cheque as Bounced</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger py-2">
                        <strong>Cheque:</strong> <span id="bounce_cheque_title">-</span><br>
                        <strong>Amount:</strong> Rs. <span id="bounce_cheque_amount">0.00</span><br>
                        <small class="text-dark"><strong>Note:</strong> Customer ki udhaar balance wapas barh jayegi (Reversal).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Bounce Date <span class="text-danger">*</span></label>
                        <input type="date" name="bounced_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Bounce Reason <span class="text-danger">*</span></label>
                        <select name="bounce_reason" class="form-control" required>
                            <option value="Insufficient Balance">Insufficient Balance (Fund nahi tha)</option>
                            <option value="Signature Mismatch">Signature Mismatch (Dastakhat nahi milte)</option>
                            <option value="Post Dated / Stale Cheque">Date Issue (Post Dated / Stale Cheque)</option>
                            <option value="Payment Stopped by Drawer">Payment Stopped by Drawer (Customer ne roki)</option>
                            <option value="Account Closed">Account Closed</option>
                            <option value="Other">Other / Miscellaneous</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Bounce & Reverse Balance</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openClearModal(id, chequeNo, amount, bank) {
        $('#clear_cheque_title').text('#' + chequeNo + ' (' + bank + ')');
        $('#clear_cheque_amount').text(parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#clearChequeForm').attr('action', '/cheques/' + id + '/clear');
        var modal = new bootstrap.Modal(document.getElementById('clearChequeModal'));
        modal.show();
    }

    function openBounceModal(id, chequeNo, amount) {
        $('#bounce_cheque_title').text('#' + chequeNo);
        $('#bounce_cheque_amount').text(parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#bounceChequeForm').attr('action', '/cheques/' + id + '/bounce');
        var modal = new bootstrap.Modal(document.getElementById('bounceChequeModal'));
        modal.show();
    }
</script>
@endsection
