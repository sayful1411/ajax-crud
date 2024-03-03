{{-- edit product modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editProductModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div id="editValidationErrors" class="alert alert-danger" style="display: none;"></div>

            <form action="javascript:void(0)" id="editProductForm" enctype="multipart/form-data">
                <div class="modal-body">

                    <input type="hidden" id="productId" name="productId">

                    <div class="form-group mb-3">
                        <label for="edit_category_id" class="mb-2">Category:</label>
                        <select class="form-control" name="category_id" id="edit_category_id">
                            <option value="">Select Category</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_name" class="mb-2">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" placeholder="Name">
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_price" class="mb-2">Price</label>
                        <input type="text" class="form-control" id="edit_price" name="price" placeholder="Price">
                    </div>

                    <div class="d-flex flex-column form-group mb-3 image-preview">
                        <label class="leading-loose">Image</label>
                        <div for="edit_image" class="d-flex align-items-center justify-content-center mb-2">
                            <img style="width: 24rem; height: 18rem;" class="object-fit-cover rounded"
                                src="{{ asset(\App\Models\Product::PLACEHOLDER_IMAGE_PATH) }}" alt="">
                        </div>
                        <input name="image" id="edit_image" type="file" class="image-upload-input" placeholder="">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>

        </div>
    </div>
</div>
