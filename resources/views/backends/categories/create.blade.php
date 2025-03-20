<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Create Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form">
                @include('backends.components.form.categories_form', [
                    'route_create' => 'categories.store',
                ])
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createForm = document.getElementById('createCategoryForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                const parentIdSelect = document.getElementById('parent_id');
                if (parentIdSelect.value === '') {
                    // If "None" is selected, make sure the value is null
                    parentIdSelect.disabled = true;
                }
            });
        }
    });
</script>
