@extends('admin_panel.layout.app')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-weight-bold text-dark">Sales Officers</h3>
                            @can('sales.officers.create')
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal" data-bs-toggle="modal" data-bs-target="#createModal" id="reset">
                                    <i class="fa fa-plus me-1"></i> Create Sales Officer
                                </button>
                            @endcan
                        </div>

                        <div class="border mt-1 shadow-sm rounded" style="background-color: white;">
                            <div class="col-lg-12 m-auto">
                                <div class="table-responsive p-3">
                                    <table id="default-datatable" class="table table-bordered table-striped text-center">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 80px;">ID</th>
                                                <th>Name</th>
                                                <th>Name (Urdu)</th>
                                                <th>Mobile</th>
                                                <th style="width: 150px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($officers as $officer)
                                                <tr id="row-{{ $officer->id }}">
                                                    <td>{{ $officer->id }}</td>
                                                    <td class="font-weight-bold">{{ $officer->name }}</td>
                                                    <td dir="rtl">{{ $officer->name_urdu ?? '-' }}</td>
                                                    <td>{{ $officer->mobile }}</td>
                                                    <td>
                                                        @include('admin_panel.partials.action_buttons', [
                                                            'editRoute' => 'javascript:void(0)',
                                                            'deleteRoute' => route('sales-officer.delete.get', $officer->id),
                                                            'editIsLink' => false,
                                                            'permissions' => [
                                                                'edit' => 'sales.officers.edit',
                                                                'delete' => 'sales.officers.delete',
                                                            ],
                                                            'dataId' => $officer->id,
                                                            'deleteMsg' => 'Are you sure you want to delete this sales officer?',
                                                        ])
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">No Sales Officers found. Click "Create Sales Officer" to add one.</td>
                                                </tr>
                                            @endforelse
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
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="create-form" action="{{ route('sales-officer.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="createModalLabel">Add Sales Officer</h5>
                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter officer name" required />
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">نام (Urdu)</label>
                            <input type="text" name="name_urdu" class="form-control text-end" dir="rtl" placeholder="افسر کا نام" />
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control" placeholder="03001234567" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        @can('sales.officers.create')
                            <button type="submit" class="btn btn-primary" id="createSubmitBtn">Save</button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="edit-form" action="{{ route('sales-officer.store') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="editModalLabel">Edit Sales Officer</h5>
                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder="Enter officer name" required />
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">نام (Urdu)</label>
                            <input type="text" name="name_urdu" id="edit_name_urdu" class="form-control text-end" dir="rtl" placeholder="افسر کا نام" />
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" id="edit_mobile" class="form-control" placeholder="03001234567" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        @can('sales.officers.edit')
                            <button type="submit" class="btn btn-primary" id="editSubmitBtn">Update</button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Fix modal blur warning
            $('.modal').on('hide.bs.modal', function () {
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            });

            // Initialize DataTable if table has rows
            if ($('#default-datatable tbody tr td').length > 1) {
                if (!$.fn.DataTable.isDataTable('#default-datatable')) {
                    $('#default-datatable').DataTable({
                        pageLength: 10,
                        order: [[0, 'desc']]
                    });
                }
            }

            // Click listener on Create button to guarantee modal opens
            $('#reset').on('click', function(e) {
                e.preventDefault();
                $('.create-form')[0].reset();
                $('#createModal').modal('show');
            });

            // Helper to display error alerts
            function showAjaxError(xhr, fallbackMessage) {
                let errorHtml = fallbackMessage;
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        let list = [];
                        $.each(xhr.responseJSON.errors, function(field, messages) {
                            if (Array.isArray(messages)) {
                                list.push(messages.join('<br>'));
                            } else {
                                list.push(messages);
                            }
                        });
                        errorHtml = list.join('<br>');
                    } else if (xhr.responseJSON.message) {
                        errorHtml = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        errorHtml = xhr.responseJSON.error;
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorHtml
                });
            }

            // CREATE FORM SUBMISSION
            $(document).on('submit', '.create-form', function(e) {
                e.preventDefault();
                let $form = $(this);
                let $btn = $form.find('#createSubmitBtn');
                let originalText = $btn.text() || 'Save';

                $btn.prop('disabled', true).text('Saving...');
                let formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#createModal').modal('hide');
                        $form[0].reset();
                        let successMsg = (response && response.message) ? response.message : 'Sales Officer Created Successfully';
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: successMsg
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        showAjaxError(xhr, 'Could not create Sales Officer. Please check the form and try again.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // LOAD EDIT DATA
            $(document).on('click', '.edit-btn', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                if (!id) return;

                $.ajax({
                    type: 'GET',
                    url: "{{ url('sales-officers/edit') }}/" + id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#edit_id').val(data.id);
                        $('#edit_name').val(data.name);
                        $('#edit_name_urdu').val(data.name_urdu || '');
                        $('#edit_mobile').val(data.mobile);
                        $('#editModal').modal('show');
                    },
                    error: function(xhr) {
                        showAjaxError(xhr, 'Could not load Sales Officer details.');
                    }
                });
            });

            // EDIT FORM SUBMISSION
            $(document).on('submit', '.edit-form', function(e) {
                e.preventDefault();
                let $form = $(this);
                let $btn = $form.find('#editSubmitBtn');
                let originalText = $btn.text() || 'Update';

                $btn.prop('disabled', true).text('Updating...');
                let formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#editModal').modal('hide');
                        let successMsg = (response && response.message) ? response.message : 'Sales Officer Updated Successfully';
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: successMsg
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        showAjaxError(xhr, 'Could not update Sales Officer. Please check the form and try again.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>
@endsection
