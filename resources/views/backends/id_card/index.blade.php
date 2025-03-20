<form action="{{ route('id-card.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="id_card_image" required>
    <button type="submit">Upload</button>
</form>