@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-weight-bold">Province Management</h3>
                            @can('provinces.create')
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal"
                                    id="reset">
                                    <i class="fas fa-plus mr-1"></i> Add Province
                                </button>
                            @endcan
                        </div>

                        <div class="border mt-1 shadow-sm rounded" style="background-color: white;">
                            <div class="col-lg-12 m-auto">
                                <div class="table-responsive mt-4 mb-4">
                                    <table id="default-datatable" class="table table-bordered table-striped">
                                        <thead class="text-center">
                                            <tr>
                                                <th class="text-center" style="width: 80px;">Id</th>
                                                <th class="text-center">Province Name</th>
                                                <th class="text-center" style="width: 140px;">Status</th>
                                                <th class="text-center" style="width: 160px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach ($provinces as $province)
                                                <tr id="row-{{ $province->id }}">
                                                    <td class="id align-middle">{{ $province->id }}</td>
                                                    <td class="name align-middle font-weight-bold text-left px-3">{{ $province->name }}</td>
                                                    <td class="status align-middle">
                                                        @if ($province->status === 'active')
                                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Active</span>
                                                        @else
                                                            <span class="badge badge-secondary px-2 py-1"><i class="fas fa-pause-circle mr-1"></i>Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        @include('admin_panel.partials.action_buttons', [
                                                            'editRoute' => route('province.edit', $province->id),
                                                            'deleteRoute' => route('province.delete', $province->id),
                                                            'editIsLink' => false,
                                                            'permissions' => [
                                                                'edit' => 'provinces.edit',
                                                                'delete' => 'provinces.delete',
                                                            ],
                                                            'dataId' => $province->id,
                                                            'deleteMsg' => 'Are you sure you want to delete this province?',
                                                        ])
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
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="myform" action="{{ route('province.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="createModalLabel">Add Province</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Province Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Punjab, Sindh" required />
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Status</label>
                            <select name="status" class="form-control">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        @can('provinces.create')
                            <button type="submit" class="btn btn-primary save-btn">
                                <i class="fas fa-save mr-1"></i> Save
                            </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="editform" action="{{ route('province.store') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id" />
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="editModalLabel">Edit Province</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Province Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="edit_name" required />
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Status</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        @can('provinces.edit')
                            <button type="submit" class="btn btn-primary save-btn">
                                <i class="fas fa-sync-alt mr-1"></i> Update
                            </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script>
        // Fix ARIA focus warning on modal close
        $('.modal').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#default-datatable')) {
                $('#default-datatable').DataTable({
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    order: [
                        [0, 'desc']
                    ]
                });
            }

            // CREATE FORM
            $('.myform').submit(function(e) {
                e.preventDefault();
                var form = this;
                var formData = new FormData(form);
                var url = $(form).attr('action');
                var submitBtn = $(form).find('.save-btn');
                submitBtn.prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#createModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message || 'Province created successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false);
                        var errorMsg = 'Failed to save province.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                            errorMsg = xhr.responseJSON.errors[firstKey][0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            });

            // EDIT MODAL DATA
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                var url = $(this).data('url') || ("{{ url('provinces/edit') }}/" + id);
                $.get(url, function(res) {
                    $('#edit_id').val(res.id);
                    $('#edit_name').val(res.name);
                    $('#edit_status').val(res.status || 'active');
                    $('#editModal').modal('show');
                }).fail(function() {
                    Swal.fire('Error', 'Unable to fetch province details.', 'error');
                });
            });

            // EDIT FORM
            $('.editform').submit(function(e) {
                e.preventDefault();
                var form = this;
                var formData = new FormData(form);
                var url = $(form).attr('action');
                var submitBtn = $(form).find('.save-btn');
                submitBtn.prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#editModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message || 'Province updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false);
                        var errorMsg = 'Failed to update province.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                            errorMsg = xhr.responseJSON.errors[firstKey][0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            });

            // DELETE FUNCTION
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var id = $(this).data('id');
                var url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this province? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'GET',
                            url: url,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                $('#row-' + id).fadeOut(300, function() {
                                    $(this).remove();
                                });
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Province has been deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to delete province.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
