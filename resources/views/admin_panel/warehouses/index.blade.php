@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid">

                {{-- Page Header --}}
                <div class="page-header row align-items-center mb-3">
                    <div class="page-title col-lg-6">
                        <h4 class="fw-bold mb-1"><i class="fas fa-warehouse text-primary me-2"></i>Warehouse Management</h4>
                        <h6 class="text-muted mb-0">Create, edit, organize warehouses & monitor live storage</h6>
                    </div>
                    <div class="page-btn d-flex justify-content-end col-lg-6 gap-2">
                        <a href="{{ route('warehouse.working_report') }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-line me-1"></i> Warehouse Working Report
                        </a>
                        <a href="{{ url('warehouse_stocks') }}" class="btn btn-outline-success">
                            <i class="fas fa-boxes me-1"></i> Warehouse Stock
                        </a>
                        @canany(['warehouse.create', 'warehouse.edit'])
                            <button class="btn btn-primary shadow-sm" type="button"
                                data-toggle="modal" data-target="#warehouseModal"
                                data-bs-toggle="modal" data-bs-target="#warehouseModal"
                                onclick="clearWarehouse()">
                                <i class="fas fa-plus-circle me-1"></i> Add Warehouse
                            </button>
                        @endcanany
                    </div>
                </div>

                {{-- Summary Metrics --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-primary border-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small fw-bold text-uppercase">Total Warehouses</span>
                                    <h3 class="fw-extrabold text-primary mb-0 mt-1">{{ $totalWarehouses ?? $warehouses->count() }}</h3>
                                </div>
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                                    <i class="fas fa-building fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-success border-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small fw-bold text-uppercase">Total Products Stored</span>
                                    <h3 class="fw-extrabold text-success mb-0 mt-1">{{ number_format($totalProductsStored ?? 0) }}</h3>
                                </div>
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                                    <i class="fas fa-cube fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-info border-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small fw-bold text-uppercase">Total Stock Pieces</span>
                                    <h3 class="fw-extrabold text-info mb-0 mt-1">{{ number_format($totalStockPieces ?? 0) }}</h3>
                                </div>
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                                    <i class="fas fa-layer-group fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Table Card --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-primary"></i>All Warehouses</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle datanew">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Warehouse Name</th>
                                        <th>Location / Address</th>
                                        <th>Stocked Items</th>
                                        <th>Total Pieces</th>
                                        <th>Remarks</th>
                                        <th>Created By</th>
                                        <th class="text-end" style="min-width: 140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($warehouses as $key => $w)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark fs-6">{{ $w->warehouse_name }}</div>
                                                <small class="text-muted">ID: #{{ $w->id }}</small>
                                            </td>
                                            <td>
                                                @if($w->location)
                                                    <span class="badge bg-light text-dark border"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $w->location }}</span>
                                                @else
                                                    <span class="text-muted small">Not specified</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-white px-2 py-1">{{ $w->stocks_count ?? 0 }} Items</span>
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ number_format($w->stocks_sum_total_pieces ?? 0) }} Pcs</strong>
                                            </td>
                                            <td>
                                                <span class="small text-muted">{{ $w->remarks ?: '—' }}</span>
                                            </td>
                                            <td>
                                                <span class="small text-secondary"><i class="fas fa-user-circle me-1"></i>{{ $w->user->name ?? 'System' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('warehouse.working_report', ['warehouse_id' => $w->id]) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="View Working Report">
                                                        <i class="fas fa-chart-bar"></i>
                                                    </a>
                                                    @canany(['warehouse.edit', 'warehouse.create'])
                                                        <button type="button" class="btn btn-sm btn-primary edit-warehouse-btn"
                                                            data-id="{{ $w->id }}" 
                                                            data-name="{{ $w->warehouse_name }}"
                                                            data-location="{{ $w->location }}" 
                                                            data-remarks="{{ $w->remarks }}"
                                                            data-toggle="modal" data-target="#warehouseModal"
                                                            data-bs-toggle="modal" data-bs-target="#warehouseModal"
                                                            title="Edit Warehouse">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endcanany
                                                    @can('warehouse.delete')
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                            data-url="{{ url('warehouse/delete/' . $w->id) }}"
                                                            data-msg="Are you sure you want to delete warehouse '{{ $w->warehouse_name }}'?" 
                                                            data-method="get"
                                                            onclick="logoutAndDeleteFunction(this)"
                                                            title="Delete Warehouse">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
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
    </div>

    {{-- Warehouse Modal (Add / Edit) Compatible with Bootstrap 4 & Bootstrap 5 --}}
    <div class="modal fade" id="warehouseModal" tabindex="-1" role="dialog" aria-labelledby="warehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="{{ url('warehouse/store') }}" method="POST" id="warehouseForm">
                @csrf
                <input type="hidden" name="id" id="warehouse_id">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold" id="warehouseModalLabel">
                            <i class="fas fa-warehouse me-2"></i><span id="modalActionText">Add New Warehouse</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 1.5rem; opacity: 1; line-height: 1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Warehouse Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="warehouse_name" id="warehouse_name"
                                placeholder="e.g. Main Distribution Center / Store A" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Location / City / Address</label>
                            <input class="form-control" name="location" id="location"
                                placeholder="e.g. Warehouse #4, Industrial Area, Karachi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Remarks / Notes</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Optional notes about this storage facility..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fas fa-save me-1"></i> Save Warehouse
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 3500,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Operation Blocked',
                text: "{{ session('error') }}",
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    <script>
        function clearWarehouse() {
            $('#warehouse_id').val('');
            $('#warehouse_name').val('');
            $('#location').val('');
            $('#remarks').val('');
            $('#modalActionText').text('Add New Warehouse');
            $('#warehouseModal').modal('show');
        }

        // Global Event Delegation for Edit Button (works inside DataTables child rows too)
        $(document).on('click', '.edit-warehouse-btn', function(e) {
            e.preventDefault();

            var $btn = $(this);
            var id = $btn.attr('data-id') || $btn.data('id');
            var name = $btn.attr('data-name') || $btn.data('name');
            var location = $btn.attr('data-location') || $btn.data('location') || '';
            var remarks = $btn.attr('data-remarks') || $btn.data('remarks') || '';

            $('#warehouse_id').val(id);
            $('#warehouse_name').val(name);
            $('#location').val(location);
            $('#remarks').val(remarks);
            $('#modalActionText').text('Edit Warehouse: ' + name);

            // Programmatically trigger Bootstrap modal show
            $('#warehouseModal').modal('show');
        });

        $(document).ready(function() {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable({
                    pageLength: 25,
                    responsive: true
                });
            }
        });
    </script>
@endsection
