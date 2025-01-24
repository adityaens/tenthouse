
// Preview Images Multiple
function previewImages(input, previewContainerId) {
    const previewContainer = document.getElementById(previewContainerId);
    previewContainer.innerHTML = ""; // Clear any previous previews

    if (input.files) {
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                // Create an image element for preview
                const img = document.createElement("img");
                img.src = e.target.result;
                img.style.maxWidth = "150px"; // Set a max width for the preview images
                img.style.margin = "5px";
                img.style.border = "1px solid #ddd";
                img.style.borderRadius = "5px";

                previewContainer.appendChild(img);
            };

            reader.readAsDataURL(file);
        });
    }
}