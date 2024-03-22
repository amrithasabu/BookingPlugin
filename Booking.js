function saveFormData() {
    localStorage.setItem("upload_csv", document.getElementById("upload_csv").value);
    return true;
}
window.onload = function() {
    document.getElementById("upload_csv").value = localStorage.getItem("upload_csv") || "";
}