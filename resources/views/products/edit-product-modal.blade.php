{{-- Edit Product Modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('products.update', 'id') }}" method="POST" id="editProductForm"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Starting Price</label>
                        <input type="number" class="form-control" id="edit_price" name="starting_price" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">Auction End Time</label>
                        <input type="datetime-local" class="form-control" id="edit_end_time" name="end_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"
                            required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="small mb-1">Image</label>
                        <input type="file" class="edit_image" name="image" id="edit_image" data-max-file-size="3MB" />
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>