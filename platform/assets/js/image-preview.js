function previewImages(input) {
    const previewContainer = document.getElementById('image-preview');
    previewContainer.innerHTML = '';

    if (input.files) {
        Array.from(input.files).forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            const previewDiv = document.createElement('div');
            previewDiv.className = 'relative group';

            reader.onload = function(e) {
                previewDiv.innerHTML = `
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="${e.target.result}" alt="Preview" class="rounded-lg object-cover w-full h-full">
                    </div>
                    <button type="button" class="absolute top-2 right-2 p-1 bg-red-500/80 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                            onclick="removePreview(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
            };

            reader.readAsDataURL(file);
            previewContainer.appendChild(previewDiv);
        });
    }
}

function removePreview(button) {
    const previewDiv = button.closest('.relative');
    if (previewDiv) {
        previewDiv.remove();
        
        // Clear the corresponding file from the input
        const fileInput = document.querySelector('input[type="file"]');
        if (fileInput) {
            const dt = new DataTransfer();
            const files = fileInput.files;
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file !== undefined) {
                    dt.items.add(file);
                }
            }
            
            fileInput.files = dt.files;
        }
    }
}