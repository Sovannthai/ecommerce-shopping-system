@extends('backends.master')

@section('contents')
<style>
    img{
        border-radius: 50%;
    }
</style>
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Products</label>
            <a href="{{ route('products.create') }}"
                class="btn btn-primary float-right text-uppercase btn-sm" data-value="veiw">
                <i class="fas fa-plus"> @lang('Add')</i></a>
        </div>
        <div class="card-body">

            <table id="basic-datatables" class="table text-nowrap table-hover table-responsive-lg">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span>
                                    @if($product->main_image)
                                        <a class="example-image-link" href="{{ asset($product->main_image) }}"
                                            data-lightbox="lightbox-{{ $product->id }}">
                                            <img class="example-image image-thumbnail"
                                                src="{{ asset($product->main_image) }}" alt="{{ $product->name }}"
                                                width="50px" height="50px" style="cursor:pointer" />
                                        </a>
                                    @else
                                        <div class="bg-light text-center p-2 rounded">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </span>
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>
                                @if($product->discount_price)
                                    <span class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-danger">${{ number_format($product->discount_price, 2) }}</span>
                                @else
                                    ${{ number_format($product->price, 2) }}
                                @endif
                            </td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ ucfirst($product->status) }}</td>
                            <td>
                                <a href="{{ route('products.show', $product->id) }}"
                                    class="btn btn-outline-info btn-sm">View</a>
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="btn btn-outline-primary btn-sm">Edit</a>
                                <form action="{{ route('products.destroy', ['product' => $product->id]) }}"
                                    method="POST" class="delete-btn" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
