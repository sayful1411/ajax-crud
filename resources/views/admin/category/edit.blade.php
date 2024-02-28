{{-- edit category modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editCategoryForm">

                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="categoryId">
                    <div class="form-group">
                        <label for="name" class="mb-2">Name</label>
                        <input type="text" class="form-control" id="category_name" name="name">
                        <div id="updateValidationErrors" class="text-danger" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('script')
    <script>
        function editCategory(categoryId) {
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
        }

        $('#editCategoryForm').submit(function(e) {
            e.preventDefault();

            categoryId = $('#categoryId').val();
            name = $('#category_name').val();
            console.log(name);
            var url = "{{ route('admin.category.update', ':categoryId') }}";
            url = url.replace(':categoryId', categoryId);

            $.ajax({
                url: url,
                type: 'PUT',
                data: {
                    name: name,
                },
                success: function(response) {
                    $('#editCategoryModal').modal('hide');
                    location.reload();
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
    </script>
@endpush
