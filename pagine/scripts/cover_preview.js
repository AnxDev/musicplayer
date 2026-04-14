function previewImage(event) {
    const reader = new FileReader();
    const imageField = document.getElementById("image-preview");
    const placeholder = document.getElementById("placeholder-text");
    const container = document.getElementById("preview-container");

    reader.onload = function() {
        if (reader.readyState === 2) {
            imageField.src = reader.result;
            imageField.style.display = "block";
            placeholder.style.display = "none";
            container.style.borderStyle = "solid"; // Cambia il tratteggio in linea continua
        }
    }
    
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}