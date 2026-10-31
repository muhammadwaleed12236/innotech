@extends('admin_panel.layout.app')

@section('content')

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">

            <div class="page-header row">
                <div class="page-title col-lg-6">
                    <h4>Customer Payments & Recoveries</h4>
                    <h6>Manage Customer Receivables & Cheques</h6>
                </div>
                <div class="page-btn d-flex justify-content-end col-lg-6 gap-2">
                    <a href="{{ route('cheques.index') }}" class="btn btn-outline-info mb-2">
                        <i class="fas fa-money-check-alt me-1"></i> Cheque Management
                    </a>
                    <button class="btn btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="clearPaymentForm()">
                        <i class="fas fa-plus me-1"></i> Add Payment
                    </button>
                </div>
            </div>

            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Cheque / Details</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $key => $p)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td><strong>{{ $p->customer->customer_name ?? 'N/A' }}</strong></td>
                                <td><span class="text-primary fw-bold">Rs. {{ number_format($p->amount, 2) }}</span></td>
                                <td>{{ $p->payment_date }}</td>
                                <td>
                                    @if(strtolower($p->payment_method) === 'cheque')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-money-check me-1"></i> Cheque</span>
                                    @elseif(strtolower($p->payment_method) === 'bank')
                                        <span class="badge bg-info text-dark"><i class="fas fa-university me-1"></i> Bank</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-coins me-1"></i> {{ $p->payment_method ?? 'Cash' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->cheque)
                                        <div class="small">
                                            <strong>#{{ $p->cheque->cheque_no }}</strong> - {{ $p->cheque->bank_name }}<br>
                                            <span class="text-muted">Due: {{ $p->cheque->cheque_date ? $p->cheque->cheque_date->format('d M, Y') : '-' }}</span>
                                            <div>
                                                @php $badge = $p->cheque->due_badge; @endphp
                                                <span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $p->note ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('customer.payments.destroy', $p->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this payment?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal">
    <div class="modal-dialog">
        <form action="{{ route('customer.payments.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label fw-bold">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-control" required onchange="fetchCustomerBalance(this.value)">
                            <option value="">Select Customer</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Outstanding Balance</label>
                        <input type="text" id="customer_balance" class="form-control bg-light" readonly>
                    </div>

                    <!-- Adjustment Type -->
                    <div class="mb-2">
                        <label class="form-label fw-bold">Adjustment Type <span class="text-danger">*</span></label>
                        <select name="adjustment_type" class="form-control" required>
                            <option value="minus">- Minus (Payment Received)</option>
                            <option value="plus">+ Plus (Outstanding Increased)</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="payment_amount" step="0.01" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Payment Method</label>
                        <select name="payment_method" id="payment_method_select" class="form-control" onchange="toggleChequeFields(this.value)">
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Simplified Cheque Fields (Only 4 fields: No, Bank Name, Date, Amount) -->
                    <div id="cheque_fields_container" style="display: none;" class="p-3 mb-2 bg-light border rounded">
                        <h6 class="text-primary mb-2"><i class="fas fa-money-check me-1"></i> Cheque Details</h6>
                        <div class="mb-2">
                            <label class="form-label">Cheque Number <span class="text-danger">*</span></label>
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control" placeholder="e.g. 1029384">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="e.g. Meezan Bank, HBL, MCB">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Cheque / Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="cheque_date" id="cheque_date" class="form-control" value="{{ date('Y-m-d') }}">
                            <small class="text-muted">Jis din cheque cash/deposit karwana hai</small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Note / Remarks</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Optional details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function fetchCustomerBalance(customerId) {
        if (!customerId) {
            $('#customer_balance').val('');
            return;
        }
        $.ajax({
            url: '/customer/ledger/' + customerId,
            method: 'GET',
            success: function(response) {
                if (response.closing_balance !== undefined) {
                    $('#customer_balance').val(parseFloat(response.closing_balance).toFixed(2));
                } else {
                    $('#customer_balance').val('0.00');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                $('#customer_balance').val('Error');
            }
        });
    }

    function toggleChequeFields(method) {
        var isCheque = (method === 'Cheque');
        var container = $('#cheque_fields_container');
        if (isCheque) {
            container.slideDown(200);
            $('#cheque_no').prop('required', true);
            $('#bank_name').prop('required', true);
            $('#cheque_date').prop('required', true);
        } else {
            container.slideUp(200);
            $('#cheque_no').prop('required', false);
            $('#bank_name').prop('required', false);
            $('#cheque_date').prop('required', false);
        }
    }

    function clearPaymentForm() {
        $('#paymentModal select[name="customer_id"]').val('');
        $('#paymentModal input[name="payment_date"]').val('{{ date('Y-m-d') }}');
        $('#paymentModal input[name="amount"]').val('');
        $('#payment_method_select').val('Cash');
        $('#paymentModal textarea[name="note"]').val('');
        $('#customer_balance').val('');
        toggleChequeFields('Cash');
    }
</script>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('.datanew')) {
            $('.datanew').DataTable();
        }
    });
</script>
@endpush
