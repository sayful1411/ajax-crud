@extends('layouts.app')

@section('title', 'Products | ' . config('app.name'))

@section('content')
    <div class="container-fluid px-4">

        <h1 class="mt-4">Products</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Products</li>
        </ol>

        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                        <a type="button" class="btn btn-primary" href="javascript:void(0)" id="createProduct">
                            Add Product
                        </a>
                    </div>
                    <div class="card-body">
                        <table id="productTable" class="table table-bordered data-table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Price</th>
                                    <th style="width: 15%" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.product.create')

    @include('admin.product.edit')

    @include('admin.product.delete')

@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.min.css">
@endpush

@push('script')
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // image upload preview
        $('.image-upload-input').change(function() {
            const file = this.files[0];
            const previewer = $(this).closest('.image-preview').find('img');

            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    previewer.attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // show product lists
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.product.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'image_url',
                    name: 'image',
                    render: function(data, type, full, meta) {
                        return '<img src="' + data + '" style="max-width: 75px; max-height: 75px;">';
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'category.name',
                    name: 'category.name'
                },
                {
                    data: 'price',
                    name: 'price'
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

        // create product
        $('#createProduct').click(function() {
            $('#createProductForm').trigger("reset");
            $('#productModalLabel').html("Create Product");

            $.ajax({
                url: "{{ route('admin.category.index') }}",
                type: 'GET',
                dataType: 'json',

                success: function(response) {
                    var categories = response.data;

                    categories.forEach(function(category) {
                        $('#category_id').append($('<option>', {
                            value: category.id,
                            text: category.name
                        }));
                    });
                },

                error: function(xhr, status, error) {
                    console.error(error);
                }
            });

            $('#productModal').modal('show');
        });

        $('#createProductForm').submit(function(e) {
            e.preventDefault();

            $('#createProductForm button[type="submit"]').prop('disabled', true);
            $('#loader').show();
            $('.overlay').show();

            var formData = new FormData($(this)[0]);

            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                url: "{{ route('admin.product.store') }}",

                success: function(response) {
                    $('#createProductForm').trigger("reset");
                    $('.image-preview img').attr('src',
                        '{{ asset(\App\Models\Product::PLACEHOLDER_IMAGE_PATH) }}');
                    $('#loader').hide();
                    $('.overlay').hide();
                    $('#validationErrors').hide()
                    $('#createProductForm button[type="submit"]').prop('disabled', false);
                    $('#productModal').modal('hide');

                    // table.draw();

                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Product Created",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr, status, error) {
                    $('#loader').hide();
                    $('.overlay').hide();
                    $('#createProductForm button[type="submit"]').prop('disabled', false);

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
        // $('body').on('click', '.editCategory', function() {
        //     var categoryId = $(this).data('id');
        //     var url = "{{ route('admin.category.edit', ':categoryId') }}";
        //     url = url.replace(':categoryId', categoryId);

        //     $.ajax({
        //         url: url,
        //         type: 'GET',
        //         success: function(data) {
        //             $('#categoryId').val(data.id);
        //             $('#category_name').val(data.name);
        //             $('#editCategoryModal').modal('show');
        //         },
        //     });
        // });

        // $('#editCategoryForm').submit(function(e) {
        //     e.preventDefault();

        //     categoryId = $('#categoryId').val();
        //     name = $('#category_name').val();
        //     var url = "{{ route('admin.category.update', ':categoryId') }}";
        //     url = url.replace(':categoryId', categoryId);

        //     $.ajax({
        //         url: url,
        //         type: 'PUT',
        //         data: {
        //             name: name,
        //         },
        //         success: function(response) {
        //             $('#editCategoryForm').trigger("reset");
        //             $('#editCategoryModal').modal('hide');
        //             table.draw();
        //             Swal.fire({
        //                 position: "center",
        //                 icon: "success",
        //                 title: "Category Updated",
        //                 showConfirmButton: false,
        //                 timer: 1500
        //             });
        //         },
        //         error: function(xhr, status, error) {
        //             if (xhr.status === 422) {
        //                 var errors = xhr.responseJSON.errors;
        //                 var errorMessage = '';
        //                 $.each(errors, function(key, value) {
        //                     errorMessage += value[0] + '<br>';
        //                 });
        //                 $('#updateValidationErrors').html(errorMessage);
        //                 $('#updateValidationErrors').show()
        //             }
        //         }
        //     });
        // });

        // delete category
        // $('body').on('click', '.deleteCategory', function() {
        //     var categoryId = $(this).data('id');
        //     $('#deleteCategoryId').val(categoryId);
        //     $('#deleteCategoryModal').modal('show');
        // });

        // $('#deleteCategoryForm').submit(function(e) {
        //     e.preventDefault();

        //     categoryId = $('#deleteCategoryId').val();
        //     var url = "{{ route('admin.category.destroy', ':categoryId') }}";
        //     url = url.replace(':categoryId', categoryId);

        //     $.ajax({
        //         url: url,
        //         type: 'DELETE',
        //         data: {
        //             id: categoryId,
        //         },
        //         success: function(response) {
        //             $('#deleteCategoryModal').modal('hide');
        //             table.draw();
        //             Swal.fire({
        //                 position: "center",
        //                 icon: "success",
        //                 title: "Category Deleted",
        //                 showConfirmButton: false,
        //                 timer: 1500
        //             });
        //         },
        //     });
        // });
    </script>
@endpush