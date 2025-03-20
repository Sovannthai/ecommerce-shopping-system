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
                    'row' => $category,
                ])
            </div>
        </div>
    </div>
</div>
