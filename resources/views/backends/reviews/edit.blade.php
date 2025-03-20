<div class="modal fade" id="edit-{{ $review->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-{{ $review->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel-{{ $review->id }}">Edit Review #{{ $review->id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reviews.update', $review->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_id_{{ $review->id }}">Product <span class="text-danger">*</span></label>
                                <select class="form-control @error('product_id') is-invalid @enderror" id="product_id_{{ $review->id }}" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}" {{ $review->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id_{{ $review->id }}">Customer <span class="text-danger">*</span></label>
                                <select class="form-control @error('user_id') is-invalid @enderror" id="user_id_{{ $review->id }}" name="user_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}" {{ $review->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rating_{{ $review->id }}">Rating <span class="text-danger">*</span></label>
                                <select class="form-control @error('rating') is-invalid @enderror" id="rating_{{ $review->id }}" name="rating" required>
                                    <option value="">Select Rating</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ $review->rating == $i ? 'selected' : '' }}>
                                            {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_{{ $review->id }}">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status_{{ $review->id }}" name="status" required>
                                    <option value="pending" {{ $review->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $review->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $review->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="review_{{ $review->id }}">Review</label>
                        <textarea class="form-control @error('review') is-invalid @enderror" id="review_{{ $review->id }}" name="review" rows="4">{{ $review->review }}</textarea>
                        @error('review')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
