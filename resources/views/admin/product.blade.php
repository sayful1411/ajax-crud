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

    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="productModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div id="validationErrors" class="alert alert-danger" style="display: none;"></div>

                <form action="javascript:void(0)" id="productForm" enctype="multipart/form-data">
                    <div class="modal-body">

                        <input type="hidden" id="productId" name="productId">

                        <div class="form-group mb-3">
                            <label for="category_id" class="mb-2">Category:</label>
                            <select class="form-control" name="category_id" id="category_id">
                                <option value="">Select Category</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="name" class="mb-2">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                        </div>

                        <div class="form-group mb-3">
                            <label for="price" class="mb-2">Price</label>
                            <input type="text" class="form-control" id="price" name="price" placeholder="Price">
                        </div>

                        <div class="d-flex flex-column form-group mb-3 image-preview">
                            <label class="leading-loose">Image</label>
                            <div for="image" class="d-flex align-items-center justify-content-center mb-2">
                                <img style="width: 24rem; height: 18rem;" class="object-fit-cover rounded"
                                    src="{{ asset(\App\Models\Product::PLACEHOLDER_IMAGE_PATH) }}" alt="">
                            </div>
                            <input name="image" id="image" type="file" class="image-upload-input" placeholder="">
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
        $(document).ready(function() {

            // setup csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

            // category lists
            function getCategoryList() {
                $.ajax({
                    method: 'GET',
                    url: "{{ route('admin.category.index') }}",
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
            }

            // create product
            $('#createProduct').click(function() {
                $('#productModalLabel').html("Create Product");
                $('#validationErrors').hide();
                $('#productForm').trigger("reset");
                $('#category_id').find('option').not(':first').remove();
                $('.image-preview img').attr('src',
                    '{{ asset(\App\Models\Product::PLACEHOLDER_IMAGE_PATH) }}');
                getCategoryList();
                $('#createBtn').show();
                $('#updateBtn').hide();
                $('#productModal').modal('show');
            });

            $('#createBtn').click(function(e) {
                e.preventDefault();

                $('#productForm button').prop('disabled', true);
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
                    url: "{{ route('admin.product.store') }}",
                    processData: false,
                    contentType: false,
                    data: formData,

                    success: function(response) {
                        $('#loader').hide();
                        $('.overlay').hide();
                        $('#productForm button').prop('disabled', false);
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
                        $('#productForm button').prop('disabled', false);

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

            // edit product
            $('body').on('click', '.editProduct', function() {
                $('#category_id').find('option').not(':first').remove();
                getCategoryList();

                var productId = $(this).data('id');
                var url = "{{ route('admin.product.edit', ':product') }}";
                var url = url.replace(':product', productId);

                $.ajax({
                    method: 'GET',
                    url: url,
                    success: function(data) {
                        $('#productModalLabel').html("Edit Product");
                        $('#validationErrors').hide();
                        $('#productId').val(data.id);
                        $('#category_id').val(data.category_id);
                        $('#name').val(data.name);
                        $('#price').val(data.price);
                        $('.image-preview img').attr('src', data.image_url);
                        $('#updateBtn').show();
                        $('#createBtn').hide();
                        $('#productModal').modal('show');
                    },
                });
            });

            $('#updateBtn').click(function(e) {
                e.preventDefault();

                productId = $('#productId').val();
                var url = "{{ route('admin.product.update', ':product') }}";
                var url = url.replace(':product', productId);

                $('#productForm button').prop('disabled', true);
                $('#loader').show();
                $('.overlay').show();

                var formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('category_id', $("#category_id").val());
                formData.append('name', $("#name").val());
                formData.append('price', $("#price").val());
                if ($('#image')[0].files.length > 0) {
                    formData.append('image', $('#image')[0].files[0]);
                }

                $.ajax({
                    method: 'POST',
                    url: url,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(response) {
                        $('#loader').hide();
                        $('.overlay').hide();
                        $('#validationErrors').hide();
                        $('#productForm button').prop('disabled', false);
                        $('#productModal').modal('hide');

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
                        $('#productForm button').prop('disabled', false);

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

            // delete product
            $('body').on('click', '.deleteProduct', function(e) {
                var productId = $(this).data('id');
                SwalDelete(productId);
                e.preventDefault();
            });

            function SwalDelete(productId) {
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
                        var url = "{{ route('admin.product.destroy', ':product') }}";
                        var url = url.replace(':product', productId);

                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                id: productId,
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
            
        });
    </script>
@endpush
