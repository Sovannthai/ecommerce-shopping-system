{{-- Create Btn --}}
@if (!empty($route_create))
    <a class="btn btn-primary btn-sm float-right text-uppercase btn-modal" href="#"
        data-href="{{ route($route_create) }}" data-toggle="modal" data-container=".{{ $modal_name }}">
        <i class="fa fa-plus-circle"></i>
        {{ __('Add New') }}
    </a>
@endif

{{-- Edit Btn --}}
@if (!empty($route_edit) && !empty($row->id))
    <a href="#" class=" btn btn-outline-info btn-sm btn-edit" data-href="{{ route($route_edit, $row->id) }}"
        data-toggle="modal" data-container=".{{ $modal_name }}" data-toggle="tooltip" data-placement="top"
        title="Edit">
        <i class="mdi mdi-pencil font-size-18"></i> {{ __('Edit') }}
    </a>
@endif

{{-- Delete Btn --}}
@if (!empty($route_delete) && !empty($row->id))
    <form action="{{ route($route_delete, $row->id) }}" method="POST" class="d-inline-block">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-outline-danger btn-sm delete-btn" title="@lang('Delete')">
            <i class="fa fa-trash ambitious-padding-btn text-uppercase">
                @lang('Delete')</i>
        </button>
    </form>
@endif
