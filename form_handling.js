function saveFormData() {
    // Save form data to localStorage
    localStorage.setItem("check_in_date", document.getElementById("check_in_date").value);
    localStorage.setItem("check_out_date", document.getElementById("check_out_date").value);
    localStorage.setItem("no-of-adults", document.getElementById("no-of-adults").value);
    localStorage.setItem("no-of-children", document.getElementById("no-of-children").value);
    localStorage.setItem("first-name", document.getElementById("first-name").value);
    localStorage.setItem("last-name", document.getElementById("last-name").value);
    localStorage.setItem("email", document.getElementById("email").value);
    localStorage.setItem("phone", document.getElementById("phone").value);
    localStorage.setItem("coupon_code", document.getElementById("coupon_code").value);
    

    return true; // Allow the form to be submitted
}

// Retrieve and populate form data on page load
window.onload = function() {
    document.getElementById("check_in_date").value = localStorage.getItem("check_in_date") || "";
    document.getElementById("check_out_date").value = localStorage.getItem("check_out_date") || "";
    document.getElementById("no-of-adults").value = localStorage.getItem("no-of-adults") || "";
    document.getElementById("no-of-children").value = localStorage.getItem("no-of-children") || "";
    document.getElementById("first-name").value = localStorage.getItem("first-name") || "";
    document.getElementById("last-name").value = localStorage.getItem("last-name") || "";
    document.getElementById("email").value = localStorage.getItem("email") || "";
    document.getElementById("phone").value = localStorage.getItem("phone") || "";
    document.getElementById("coupon_code").value = localStorage.getItem("coupon_code") || "";
};
