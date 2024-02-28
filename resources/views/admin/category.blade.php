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
                        <a type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                            Add Category
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th style="width: 25%" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $index = ($categories->currentPage() - 1) * $categories->perPage();
                                @endphp
                                @forelse ($categories as $category)
                                    <tr>
                                        <th scope="row">{{ ++$index }}</th>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary text-white mx-2" href="">Edit</a>
                                            <a class="btn btn-sm btn-danger text-white mx-2" href="">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No Category Data Found</td>
                                    </tr>
                                @endforelse
                                <tr>
                                    <td class="text-center">
                                        {{ $categories->links('pagination::bootstrap-4') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.category.create')


@endsection

@push('script')
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
        // setup csrf token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // create category
        function createCategory() {
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
                    location.reload();
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
        }
    </script>
@endpush
