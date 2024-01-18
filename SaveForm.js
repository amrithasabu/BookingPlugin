function saveFormData() {
    localStorage.setItem("check_in_date", document.getElementById("check_in_date").value);
    localStorage.setItem("check_out_date", document.getElementById("check_out_date").value);
    localStorage.setItem("no-of-adults", document.getElementById("no-of-adults").value);
    localStorage.setItem("no-of-children", document.getElementById("no-of-children").value);
    return true;
}
window.onload = function() {
    document.getElementById("check_in_date").value = localStorage.getItem("check_in_date") || "";
    document.getElementById("check_out_date").value = localStorage.getItem("check_out_date") || "";
    document.getElementById("no-of-adults").value = localStorage.getItem("no-of-adults") || "";
    document.getElementById("no-of-children").value = localStorage.getItem("no-of-children") || "";
}
