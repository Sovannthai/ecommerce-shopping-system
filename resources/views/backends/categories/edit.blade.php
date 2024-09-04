<div class="modal fade" id="edit-{{ $category->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form">
                    <form action="{{ route('categories.update',['category'=>$category->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row form-group">
                            <div class=" col-sm-12">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}">
                            </div>
                            <div class=" col-sm-12">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $category->description }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-end ml-2">Save</button>
                        <button type="button" class="btn btn-secondary float-end"
                            data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
