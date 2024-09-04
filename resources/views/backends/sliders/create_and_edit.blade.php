<div class="modal fade create" id="sliderModal-{{ $slider->id ?? 'create' }}" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ isset($slider) ? 'Edit Slider' : 'Create Slider' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ isset($slider) ? route('sliders.update', $slider->id) : route('sliders.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($slider))
                        @method('PUT')
                    @endif

                    <div class="row form-group">
                        <div class="col-sm-12">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="{{ old('title') ?? $slider->title }}" required>
                        </div>
                        <div class="col-sm-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') ?? $slider->description }}</textarea>
                        </div>
                        <div class="col-sm-12">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            @if (isset($slider) && $slider->image)
                                <img src="{{ asset('uploads/all_photo/' . $slider->image) }}" alt="{{ $slider->title }}"
                                    width="100" class="mt-2">
                            @endif
                        </div>
                        <div class="col-sm-12">
                            <label for="link">Link</label>
                            <input type="text" class="form-control" id="link" name="link"
                                value="{{ old('link') ?? $slider->link }}">
                        </div>
                        <div class="col-sm-12">
                            <label for="order">Order</label>
                            <input type="number" class="form-control" id="order" name="order"
                                value="{{ old('order') ?? $slider->order }}">
                        </div>
                        <div class="col-sm-12">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1"
                                    {{ (old('status') ?? ($slider->status ?? 1)) == 1 ? 'selected' : '' }}>Active
                                </option>
                                <option value="0"
                                    {{ (old('status') ?? ($slider->status ?? 1)) == 0 ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                        class="btn btn-primary float-end ml-2">{{ isset($slider) ? 'Update' : 'Save' }}</button>
                    <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade create" id="create" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Slider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-sm-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-sm-12">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                        <div class="col-sm-12">
                            <label for="link">Link</label>
                            <input type="text" class="form-control" id="link" name="link">
                        </div>
                        <div class="col-sm-12">
                            <label for="order">Order</label>
                            <input type="number" class="form-control" id="order" name="order">
                        </div>
                        <div class="col-sm-12">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1">Active
                                </option>
                                <option value="0">Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary float-end ml-2">Save</button>
                    <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
