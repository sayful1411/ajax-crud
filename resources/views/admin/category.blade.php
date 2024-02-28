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
                                {{-- @php
                                    $index = ($categories->currentPage() - 1) * $categories->perPage();
                                @endphp
                                @forelse ($categories as $category)
                                    <tr>
                                        <th scope="row">{{ ++$index }}</th>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary text-white mx-2" href="#"
                                                onclick="editCategory({{ $category->id }})">Edit</a>
                                            <a class="btn btn-sm btn-danger text-white mx-2" href="#"
                                                onclick="deleteCategory({{ $category->id }})">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No Category Data Found</td>
                                    </tr>
                                @endforelse --}}
                            </tbody>
                            {{-- <tfoot>
                                <div class="text-center">
                                    <tr>
                                        <td>
                                            {{ $categories->links('pagination::bootstrap-4') }}
                                        </td>
                                    </tr>
                                </div> --}}
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.category.create')

    @include('admin.category.edit')

    @include('admin.category.delete')

@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.min.css">
@endpush

@push('script')
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        // setup csrf token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // create category
        $('#createCategory').click(function() {
            $('#createCategoryForm').trigger("reset");
            $('#categoryModal').modal('show');
        });

        $('#createCategoryForm').submit(function(e) {
            e.preventDefault();

            name = $("#name").val();
            console.log(name);

            $.ajax({
                method: 'POST',
                data: {
                    name: name,
                },
                url: "{{ route('admin.category.store') }}",
                success: function(response) {
                    $('#categoryModal').modal('hide');
                    table.draw();
                    Swal.fire({
                        position: "top-center",
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
                        $('#validationErrors').show()
                    }
                }
            });
        });

        // edit category
        $('body').on('click', '.editCategory', function() {
            var categoryId = $(this).data('id');
            var url = "{{ route('admin.category.edit', ':categoryId') }}";
            url = url.replace(':categoryId', categoryId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    $('#categoryId').val(data.id);
                    $('#category_name').val(data.name);
                    $('#editCategoryModal').modal('show');
                },
            });
        });

        $('#editCategoryForm').submit(function(e) {
            e.preventDefault();

            categoryId = $('#categoryId').val();
            name = $('#category_name').val();
            var url = "{{ route('admin.category.update', ':categoryId') }}";
            url = url.replace(':categoryId', categoryId);

            $.ajax({
                url: url,
                type: 'PUT',
                data: {
                    name: name,
                },
                success: function(response) {
                    $('#editCategoryForm').trigger("reset");
                    $('#editCategoryModal').modal('hide');
                    table.draw();
                    Swal.fire({
                        position: "top-center",
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
                        $('#updateValidationErrors').html(errorMessage);
                        $('#updateValidationErrors').show()
                    }
                }
            });
        });

        // delete category
        $('body').on('click', '.deleteCategory', function() {
            var categoryId = $(this).data('id');
            $('#deleteCategoryId').val(categoryId);
            $('#deleteCategoryModal').modal('show');
        });

        $('#deleteCategoryForm').submit(function(e) {
            e.preventDefault();

            categoryId = $('#deleteCategoryId').val();
            var url = "{{ route('admin.category.destroy', ':categoryId') }}";
            url = url.replace(':categoryId', categoryId);

            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    id: categoryId,
                },
                success: function(response) {
                    $('#deleteCategoryModal').modal('hide');
                    table.draw();
                    Swal.fire({
                        position: "top-center",
                        icon: "success",
                        title: "Category Deleted",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
            });
        });
    </script>
@endpush
