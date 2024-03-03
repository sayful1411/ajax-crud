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
        $(document).ready(function() {

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
                            return '<img src="' + data +
                                '" style="max-width: 75px; max-height: 75px;">';
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
                $('.image-preview img').attr('src',
                    '{{ asset(\App\Models\Product::PLACEHOLDER_IMAGE_PATH) }}');
                $('#validationErrors').hide();
                $('#category_id').find('option').not(':first').remove();
                $('#productModalLabel').html("Create Product");

                $.ajax({
                    url: "{{ route('admin.category.index') }}",
                    type: 'GET',
                    dataType: 'json',

                    success: function(response) {
                        var categories = response.data;

                        categories.forEach(function(category) {
                            var decodedName = $('<div/>').html(category.name).text();
                            $('#category_id').append($('<option>', {
                                value: category.id,
                                text: decodedName
                            }));
                        });
                    },

                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });

                $('#productModal').modal('show');
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();

                $('#createProductForm button').prop('disabled', true);
                $('#loader').show();
                $('.overlay').show();

                var formData = new FormData();
                formData.append('category_id', $("#category_id").val());
                formData.append('name', $("#name").val());
                formData.append('price', $("#price").val());
                if ($('#image')[0].files.length > 0) {
                    formData.append('image', $('#image')[0].files[0]);
                }

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
                        $('#validationErrors').hide();
                        $('#createProductForm button').prop('disabled', false);
                        $('#productModal').modal('hide');

                        table.draw();

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
                        $('#createProductForm button').prop('disabled', false);

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

            // edit product
            $('body').on('click', '.editProduct', function() {

                $.ajax({
                    url: "{{ route('admin.category.index') }}",
                    type: 'GET',
                    dataType: 'json',

                    success: function(response) {
                        var categories = response.data;

                        $('#edit_category_id').find('option').not(':first').remove();

                        categories.forEach(function(category) {
                            var decodedName = $('<div/>').html(category.name).text();
                            $('#edit_category_id').append($('<option>', {
                                value: category.id,
                                text: decodedName
                            }));
                        });
                    },

                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });

                var productId = $(this).data('id');
                var url = "{{ route('admin.product.edit', ':product') }}";
                url = url.replace(':product', productId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        $('#editProductModalLabel').html("Edit Product");
                        $('#productId').val(data.id);
                        $('#edit_category_id').val(data.category_id);
                        $('#edit_name').val(data.name);
                        $('#edit_price').val(data.price);
                        $('.image-preview img').attr('src', data.image_url);
                        $('#editProductModal').modal('show');
                    },
                });
            });

            $('#editProductForm').submit(function(e) {
                e.preventDefault();

                productId = $('#productId').val();
                var url = "{{ route('admin.product.update', ':product') }}";
                url = url.replace(':product', productId);

                $('#editProductForm button').prop('disabled', true);
                $('#loader').show();
                $('.overlay').show();

                var editFormData = new FormData();
                editFormData.append('category_id', $("#edit_category_id").val());
                editFormData.append('name', $("#edit_name").val());
                editFormData.append('price', $("#edit_price").val());
                if ($('#edit_image')[0].files.length > 0) {
                    editFormData.append('image', $('#edit_image')[0].files[0]);
                }

                $.ajax({
                    method: 'POST',
                    url: url,
                    processData: false,
                    contentType: false,
                    data: editFormData,
                    success: function(response) {
                        $('#editProductForm').trigger("reset");
                        $('.image-preview img').attr('src',
                            '{{ asset(\App\Models\Product::PLACEHOLDER_IMAGE_PATH) }}');
                        $('#loader').hide();
                        $('.overlay').hide();
                        $('#editValidationErrors').hide()
                        $('#editProductForm button').prop('disabled', false);
                        $('#editProductModal').modal('hide');

                        table.draw();

                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Product Updated",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#loader').hide();
                        $('.overlay').hide();
                        $('#editProductForm button').prop('disabled', false);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            $('#editValidationErrors').html(errorMessage);
                            $('#editValidationErrors').show()
                        }
                    }
                });
            });

            // delete product
            $('body').on('click', '.deleteProduct', function() {
                var productId = $(this).data('id');
                $('#deleteProductId').val(productId);
                $('#deleteProductModal').modal('show');
            });

            $('#deleteProductForm').submit(function(e) {
                e.preventDefault();

                productId = $('#deleteProductId').val();
                var url = "{{ route('admin.product.destroy', ':productId') }}";
                url = url.replace(':productId', productId);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        id: productId,
                    },
                    success: function(response) {
                        $('#deleteProductModal').modal('hide');
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
            });


        });
    </script>
@endpush
