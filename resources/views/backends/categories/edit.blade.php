<div class="modal fade" id="edit-{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel-{{ $category->id }}"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryLabel-{{ $category->id }}">Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data" id="editCategoryForm-{{ $category->id }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name-{{ $category->id }}">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name-{{ $category->id }}" name="name"
                            value="{{ old('name', $category->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="parent_id-{{ $category->id }}">Parent Category</label>
                        <select class="form-control" id="parent_id-{{ $category->id }}" name="parent_id">
                            <option value="">None (Top Level)</option>
                            @foreach($categories as $parentCategory)
                                @if($parentCategory->id != $category->id)
                                    <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                        {{ $parentCategory->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description-{{ $category->id }}">Description</label>
                        <textarea class="form-control" id="description-{{ $category->id }}" name="description"
                            rows="3">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="image-{{ $category->id }}">Image</label>
                        @if($category->image)
                            <div class="mt-2 mb-2">
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" width="100">
                            </div>
                        @endif
                        <input type="file" class="form-control-file" id="image-{{ $category->id }}" name="image">
                        <small class="form-text text-muted">Leave empty to keep current image</small>
                    </div>

                    <div class="form-group">
                        <label for="status-{{ $category->id }}">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status-{{ $category->id }}" name="status" required>
                            <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editCategoryForm-{{ $category->id }}');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const parentIdSelect = document.getElementById('parent_id-{{ $category->id }}');
                if (parentIdSelect.value === '') {
                    // If "None" is selected, make sure the value is null by disabling the field
                    parentIdSelect.disabled = true;
                }
            });
        }
    });
</script>
