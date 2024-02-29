var x = document.getElementById("author-btn");
var y = document.getElementById("publisher-btn");
var z = document.getElementById("manufacturer-btn");
var indivCont = document.getElementById("individual-container");
var orgCont = document.getElementById("organization-container");
var prodCont = document.getElementById("products-container");
var user_type = document.querySelector("input[name='user_type']");

document.addEventListener("DOMContentLoaded", function () {
    x.click();
});

function author() {
    x.classList.remove('btn');
    x.classList.add('active');
    y.classList.remove('active');
    y.classList.add('btn');
    z.classList.remove('active');
    z.classList.add('btn');
    indivCont.style.display = 'block';
    orgCont.style.display = 'none';
    user_type.value = "Author";
}

function publisher() {
    y.classList.remove('btn');
    y.classList.add('active');
    x.classList.remove('active');
    x.classList.add('btn');
    z.classList.remove('active');
    z.classList.add('btn');
    indivCont.style.display = 'none';
    orgCont.style.display = 'block';
    prodCont.style.display = 'none';
    user_type.value = "Publisher";
}

function manufacturer() {
    z.classList.remove('btn');
    z.classList.add('active');
    x.classList.remove('active');
    x.classList.add('btn');
    y.classList.remove('active');
    y.classList.add('btn');
    indivCont.style.display = 'none';
    orgCont.style.display = 'block';
    prodCont.style.display = 'block';
    user_type.value = "Manufacturer";
}





// Common functions for form validation
document.getElementById("SellerForm").addEventListener('submit', function (e) {
    e.preventDefault();
    // Determine user type based on UI state
    var userType = user_type.value;
    submitForm(userType);
});
function submitForm(userType) {
    var isValid = false;
    switch (userType) {
        case "Author":
            isValid = validateAuthorForm();
            break;
        case "Publisher":
            isValid = validatePublisherForm();
            break;
        case "Manufacturer":
            isValid = validateManufacturerForm();
            break;
    }
    if (isValid) {
        document.getElementById("SellerForm").submit();
    }
}
function validateField(fieldName, errorMessage) {
    var inputField = document.getElementsByName(fieldName)[0];
    var inputControl = inputField.parentElement;
    var errorDisplay = inputControl.querySelector('.error');
    var fieldValue = inputField.value.trim();
    if (fieldValue === '') {
        inputControl.classList.add('error');
        inputControl.classList.remove('success');
        errorDisplay.textContent = errorMessage;
        return false;
    } else {
        errorDisplay.textContent = "";
        return true;
    }
}

function validateEmail(fieldName) {
    var emailInput = document.getElementsByName(fieldName)[0];
    var emailControl = emailInput.parentElement;
    var emailError = emailControl.querySelector('.error');
    var email = emailInput.value.trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        emailError.textContent = "Email is required";
        return false;
    } else if (!emailPattern.test(email)) {
        emailError.textContent = "Invalid email format";
        return false;
    } else {
        emailError.textContent = "";
        return true;
    }
}

// Author form functions
function validateAuthorForm() {
    var isValid = true;
    // Validate fields for Author form
    isValid = validateField('fname', 'First Name is required') && isValid;
    isValid = validateField('lname', 'Last Name is required') && isValid;
    isValid = validateEmail('email') && isValid;
    isValid = validateField('phone', 'Phone is required') && isValid;
    isValid = validateField('username', 'Username is required') && isValid;
    isValid = validateField('gender', 'Gender is required') && isValid;
    isValid = validateField('nationality', 'Nationality is required') && isValid;
    isValid = validateField('address', 'Address is required') && isValid;
    isValid = validateField('biography', 'Biography is required') && isValid;
    isValid = validateField('password', 'Password is required') && isValid;
    isValid = validateField('password2', 'Confirm Password is required') && isValid;
    return isValid;
}

// Publisher form functions
function validatePublisherForm() {
    var isValid = true;
    // Validate fields for Publisher form
    isValid = validateField('OrgName', 'Organization Name is required') && isValid;
    isValid = validateEmail('OrgEmail') && isValid;
    isValid = validateField('OrgPhone', 'Organization Phone is required') && isValid;
    isValid = validateField('address1', 'Organization Address is required') && isValid;
    isValid = validateField('website1', 'Organization Website is required') && isValid;
    isValid = validateField('cfname', 'Contact Person First Name is required') && isValid;
    isValid = validateField('clname', 'Contact Person Last Name is required') && isValid;
    isValid = validateEmail('cemail') && isValid;
    isValid = validateField('cphone', 'Contact Person Phone is required') && isValid;
    return isValid;
}

// Manufacturer form functions
function validateManufacturerForm() {
    var isValid = true;
    // Validate fields for Manufacturer form
    isValid = validateField('OrgName', 'Organization Name is required') && isValid;
    isValid = validateEmail('OrgEmail') && isValid;
    isValid = validateField('OrgPhone', 'Organization Phone is required') && isValid;
    isValid = validateField('address1', 'Organization Address is required') && isValid;
    isValid = validateField('website1', 'Organization Website is required') && isValid;
    isValid = validateField('cfname', 'Contact Person First Name is required') && isValid;
    isValid = validateField('clname', 'Contact Person Last Name is required') && isValid;
    isValid = validateEmail('cemail') && isValid;
    isValid = validateField('cphone', 'Contact Person Phone is required') && isValid;
    return isValid;
}
