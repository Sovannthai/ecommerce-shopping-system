<form
    action="{{ isset($route_edit) && isset($row) ? route($route_edit, $row->id) : (isset($route_create) ? route($route_create) : '') }}"
    method="POST" id="categoryForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="_method" value="{{ isset($route_edit) ? 'PUT' : 'POST' }}">
    <div class="row form-group">
        <div class=" col-sm-12">
            <label for="name">{{ __('Name') }}</label>
            <input type="text" class="form-control" id="name" name="name"
                value="{{ $category->name ?? '' }}">
        </div>
        <div class=" col-sm-12">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $category->description ?? '' }}</textarea>
        </div>
        <div class="col-sm-12">
            <label for="photo" class="col-form-label">@lang('Image')</label>
            <input name="image" type="file" class="dropify" data-height="100"
                data-default-file="{{ isset($category) && $category->image ? asset('uploads/all_photo/' . $category->image) : '' }}" />
            <br>
        </div>
    </div>
    <button type="submit" class="btn btn-primary float-end ml-2">Save</button>
    <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
</form>
<script>
    $(document).ready(function() {
        $('#categoryForm').submit(function(e) {
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');
            let formData = new FormData(this);
            if (form.find('input[name="_method"]').val() === "PUT") {
                formData.append('_method', 'PUT');
            }
            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success == true) {
                        toastr.success(response.msg);

                        $(".categoryList").load(location.href + " .categoryList");

                        $('[data-bs-dismiss="modal"]').click();

                        form.trigger("reset");

                        $('.dropify').dropify();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.msg || response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            toastr.error(errors[field][0]);
                        }
                    } else {
                        toastr.error('An unexpected error occurred.');
                    }
                }
            });
        });
    });
</script>
