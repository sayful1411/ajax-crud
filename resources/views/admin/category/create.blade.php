{{-- create category modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="categoryModalLabel">Create Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="validationErrors" class="alert alert-danger" style="display: none;"></div>
            <form id="categoryForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="mb-2">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="createCategory()">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
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
