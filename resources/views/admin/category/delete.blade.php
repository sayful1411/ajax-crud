{{-- create category modal --}}
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteCategoryModalLabel">Create Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteCategoryForm">
                <div class="modal-body">
                    <input type="hidden" id="deleteCategoryId">
                    <p>Are you sure? Do you really want to delete?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <<script>
        function deleteCategory(categoryId) {
            $('#deleteCategoryId').val(categoryId);
            $('#deleteCategoryModal').modal('show');
        }

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
                    location.reload();
                },
            });
        });
    </script>
@endpush
