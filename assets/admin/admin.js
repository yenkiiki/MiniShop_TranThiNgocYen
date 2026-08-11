// assets/admin/admin.js
const imageInput = document.getElementById("image");

if (imageInput) {
    imageInput.addEventListener("change", function () {
        const preview = document.getElementById("preview");
        if (preview) {
            preview.innerHTML = "";
            const files = this.files;
            for (let i = 0; i < files.length; i++) {
                const img = document.createElement("img");
                img.src = URL.createObjectURL(files[i]);
                img.width = 200;
                img.className = "img-thumbnail";
                preview.appendChild(img);
            }
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('preview');
            
            if (file && previewContainer) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <div class="d-inline-block position-relative">
                            <img src="${e.target.result}" alt="Logo Preview" class="img-thumbnail rounded shadow-sm" style="max-width: 120px; height: 120px; object-fit: cover;">
                            <div class="small text-success mt-1 fw-bold">Ảnh đã chọn: ${file.name}</div>
                        </div>
                    `;
                }
                reader.readAsDataURL(file);
            }
        });
    }
});