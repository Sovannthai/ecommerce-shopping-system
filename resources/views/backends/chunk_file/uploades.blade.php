@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Upload File</h3>
        </div>
        <div class="card-body">
            <form id="upload-form" action="{{ route('chunks_upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-sm-12">
                        <label for="file">File</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary float-end ml-2" id="upload-button">Upload</button>
                    <button type="button" class="btn btn-secondary float-end" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress mt-4">
        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
            aria-valuemax="100">0%</div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="uploadToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Upload Status</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div class="alert alert-success" role="alert">
                    This is a success alert—check it out!
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('upload-button').addEventListener('click', function() {
            const file = document.getElementById('file').files[0];
            const chunkSize = 1 * 1024 * 1024; // 1MB chunk size
            const totalChunks = Math.ceil(file.size / chunkSize);
            let chunkIndex = 0;

            function uploadChunk() {
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const blob = file.slice(start, end);

                const formData = new FormData();
                formData.append('file', blob);
                formData.append('name', file.name);
                formData.append('chunkIndex', chunkIndex);
                formData.append('totalChunks', totalChunks);
                formData.append('_token', '{{ csrf_token() }}');
                fetch("{{ route('chunks_upload.store') }}", {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            chunkIndex++;
                            const progressPercentage = Math.round((chunkIndex / totalChunks) * 100);
                            const progressBar = document.querySelector('.progress-bar');
                            progressBar.style.width = progressPercentage + '%';
                            progressBar.setAttribute('aria-valuenow', progressPercentage);
                            progressBar.textContent = progressPercentage + '%';

                            if (chunkIndex < totalChunks) {
                                uploadChunk();
                            } else {
                                showToast();
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            }
                        } else {
                            alert('Error: ' + data.error);
                        }
                    });
            }
            uploadChunk();
        });

        function showToast() {
            const toastElement = document.getElementById('uploadToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    </script>
@endsection
