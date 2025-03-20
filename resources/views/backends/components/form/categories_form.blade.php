<form
    action="{{ isset($route_edit) && isset($row) ? route($route_edit, $row->id) : (isset($route_create) ? route($route_create) : '') }}"
    method="POST">
    @csrf
    @if (isset($route_edit) && isset($row))
        @method('PUT')
    @endif
    <div class="row form-group">
        <div class=" col-sm-12">
            <label for="name">{{ __('Name') }}</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $category->name ?? '' }}"
                required>
        </div>
        <div class=" col-sm-12">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $category->description ?? '' }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-primary float-end ml-2">Save</button>
    <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
</form>
