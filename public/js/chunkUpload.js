document.getElementById('uploadForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    if (file) {
        uploadFile(file);
    }
});

function uploadFile(file) {
    const chunkSize = 2 * 1024 * 1024; // 2MB per chunk
    const totalChunks = Math.ceil(file.size / chunkSize);

    for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
        const start = chunkIndex * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('file', chunk);
        formData.append('file_name', file.name);
        formData.append('chunk_index', chunkIndex);
        formData.append('total_chunks', totalChunks);

        fetch('/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const progress = (chunkIndex + 1) / totalChunks * 100;
            updateProgress(progress);
            console.log(data.message);

            // Handle final response (e.g., file path if needed)
            if (progress === 100) {
                console.log('Upload completed!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function updateProgress(percent) {
    const progressBar = document.getElementById('progress');
    progressBar.style.width = percent + '%';
    progressBar.textContent = Math.round(percent) + '%';
}
