@extends('layouts.app')

@section('title', 'Category | ' . config('app.name'))

@section('content')
    <div class="container-fluid px-4">

        <h1 class="mt-4">Category</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Category</li>
        </ol>

        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                        <a type="button" class="btn btn-primary" href="javascript:void(0)" id="createCategory">
                            Add Category
                        </a>
                    </div>
                    <div class="card-body">
                        <table id="categoryTable" class="table table-bordered data-table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th style="width: 25%" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="categoryModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="javascript:void(0)" id="categoryForm">

                    <div id="validationErrors" class="alert alert-danger" style="display: none;"></div>

                    <input type="hidden" id="categoryId">

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="mb-2">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="createBtn" style="display: none;">Create</button>
                        <button type="button" class="btn btn-primary" id="updateBtn" style="display: none;">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.min.css">
@endpush

@push('script')
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // setup csrf token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // show category lists
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.category.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // create category
        $('#createCategory').click(function() {
            $('#categoryModalLabel').html('Create Category');
            $('#categoryForm').trigger("reset");
            $('#validationErrors').hide();
            $('#createBtn').show();
            $('#categoryModal').modal('show');
        });

        $('#createBtn').click(function(e) {
            e.preventDefault();

            var formData = new FormData($('#categoryForm')[0]);

            $.ajax({
                method: 'POST',
                url: "{{ route('admin.category.store') }}",
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    $('#categoryForm').trigger("reset");
                    $('#validationErrors').hide();
                    $('#categoryModal').modal('hide');

                    table.draw();

                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Category Created",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });

                        $('#validationErrors').html(errorMessage);
                        $('#validationErrors').show();
                    }
                }
            });
        });

        // edit category
        $('body').on('click', '.editCategory', function() {
            var categoryId = $(this).data('id');
            var url = "{{ route('admin.category.edit', ':category') }}";
            var url = url.replace(':category', categoryId);

            $.ajax({
                method: 'GET',
                url: url,
                success: function(data) {
                    $('#categoryId').val(data.id);
                    $('#name').val(data.name);
                    $('#categoryModalLabel').html('Update Category');
                    $('#validationErrors').hide();
                    $('#updateBtn').show();
                    $('#categoryModal').modal('show');
                },
            });
        });

        $('#updateBtn').click(function(e) {
            e.preventDefault();

            var categoryId = $('#categoryId').val();
            var url = "{{ route('admin.category.update', ':categoryId') }}";
            var url = url.replace(':categoryId', categoryId);

            var formData = new FormData($('#categoryForm')[0]);
            formData.append('_method', 'PUT');

            $.ajax({
                method: 'POST',
                url: url,
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    $('#validationErrors').hide();
                    $('#categoryModal').modal('hide');
                    
                    table.draw();

                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Category Updated",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });

                        $('#validationErrors').html(errorMessage);
                        $('#validationErrors').show();
                    }
                }
            });
        });

        // delete category
        $('body').on('click', '.deleteCategory', function(e) {
            var categoryId = $(this).data('id');
            SwalDelete(categoryId);
            e.preventDefault();
        });

        function SwalDelete(categoryId) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('admin.category.destroy', ':category') }}";
                    var url = url.replace(':category', categoryId);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            id: categoryId,
                        },
                        success: function(response) {
                            table.draw();

                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Category Deleted",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                    });
                }
            });
        }
    </script>
@endpush
