<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form">
                @include('backends.components.form.categories_form',
                [
                    'route_edit' => 'categories.update',
                    'row'        => $category,
                    'category'   => $category,
                ])
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
