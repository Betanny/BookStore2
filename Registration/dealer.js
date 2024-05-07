var x = document.getElementById("author-btn");
var y = document.getElementById("publisher-btn");
var z = document.getElementById("manufacturer-btn");
var indivCont = document.getElementById("individual-container");
var orgCont = document.getElementById("organization-container");
var prodCont = document.getElementById("products-container");
var user_type = document.querySelector("input[name='user_type']");

document.addEventListener("DOMContentLoaded", function() {
    fetch('/Shared Components/header.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });
        fetch('/Shared Components/footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        });
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
    
    var inputField = document.getElementsByName(fieldName)[0];
    var inputControl = inputField.parentElement;
    var errorDisplay = inputControl.querySelector('.error');
    var email = inputField.value.trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        var errorMessage = "Email is required";
        inputControl.classList.add('error');
        inputControl.classList.remove('success');
        errorDisplay.textContent = errorMessage;
        
        return false;
    } else if (!emailPattern.test(email)) {
        var errorMessage = "Invalid email format";
        inputControl.classList.add('error');
        inputControl.classList.remove('success');
        errorDisplay.textContent = errorMessage;
        return false;
    } else {
        errorDisplay.textContent = "";
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
    isValid = validatePhoneNumber('phone') && isValid; // Validate phone number field
    isValid = validateField('username', 'Username is required') && isValid;
    isValid = validateField('gender', 'Gender is required') && isValid;
    isValid = validateField('nationality', 'Nationality is required') && isValid;
    isValid = validateField('address', 'Address is required') && isValid;
    isValid = validateField('biography', 'Biography is required') && isValid;
    isValid = validateField('password', 'Password is required') && isValid;
    isValid = validateField('password2', 'Confirm Password is required') && isValid;
    isValid = validatePassword('password', 'password2') && isValid;

    return isValid;
}

// Publisher form functions
function validatePublisherForm() {
    var isValid = true;
    // Validate fields for Publisher form
    isValid = validateField('OrgName', 'Organization Name is required') && isValid;
    isValid = validateEmail('OrgEmail') && isValid;
    isValid = validateField('OrgPhone', 'Organization Phone is required') && isValid;
    isValid = validatePhoneNumber('OrgPhone') && isValid; // Validate phone number field
    isValid = validateField('org-address', 'Organization Address is required') && isValid;
    isValid = validateField('cfname', 'Contact Person First Name is required') && isValid;
    isValid = validateField('clname', 'Contact Person Last Name is required') && isValid;
    isValid = validateEmail('cemail') && isValid;
    isValid = validateField('cphone', 'Contact Person Phone is required') && isValid;
    isValid = validatePhoneNumber('cphone') && isValid; // Validate phone number field
    isValid = validateField('org-password', 'Password is required') && isValid;
    isValid = validateField('org-password2', 'Confirm Password is required') && isValid;
    isValid = validatePassword('org-password', 'org-password2') && isValid;

    return isValid;
}

// Manufacturer form functions
function validateManufacturerForm() {
    var isValid = true;
    // Validate fields for Manufacturer form
    isValid = validateField('OrgName', 'Organization Name is required') && isValid;
    isValid = validateEmail('OrgEmail') && isValid;
    isValid = validateField('OrgPhone', 'Organization Phone is required') && isValid;
    isValid = validatePhoneNumber('OrgPhone') && isValid; // Validate phone number field
    isValid = validateField('org-address', 'Organization Address is required') && isValid;
    isValid = validateField('cfname', 'Contact Person First Name is required') && isValid;
    isValid = validateField('clname', 'Contact Person Last Name is required') && isValid;
    isValid = validateEmail('cemail') && isValid;
    isValid = validateField('cphone', 'Contact Person Phone is required') && isValid;
    isValid = validatePhoneNumber('cphone') && isValid; // Validate phone number field
    isValid = validateField('org-password', 'Password is required') && isValid;
    isValid = validateField('org-password2', 'Confirm Password is required') && isValid;
    isValid = validatePassword('org-password', 'org-password2') && isValid;

    return isValid;
}


function validatePassword(fieldName1, fieldName2) {
    var password1 = document.getElementsByName(fieldName1)[0].value.trim();
    var password2 = document.getElementsByName(fieldName2)[0].value.trim();
    var inputField = document.getElementsByName(fieldName1)[0];
    var inputControl = inputField.parentElement;
    var errorDisplay = inputControl.querySelector('.error');
    var fieldValue = inputField.value.trim();
    var numberPattern = /\d/;
    var specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/;
    
    // Check if passwords match
    if (password1 !== password2) {
        var errorMessage = "Passwords do not match";
        inputControl.classList.add('error');
        inputControl.classList.remove('success');
        errorDisplay.textContent = errorMessage;
        return false;
    }
    else if (password1.length < 8) {
        var errorMessage = "Password must be at least 8 characters long";
        inputControl.classList.add('error');
        inputControl.classList.remove('success');
        errorDisplay.textContent = errorMessage;
        return false;
    }
    
    // Password complexity check
    else if (!numberPattern.test(password1) || !specialCharPattern.test(password1)) {
        var errorMessage = "Password must contain at least one number and one special character";
        inputControl.classList.add('error');
        inputControl.classList.remove('success');
        errorDisplay.textContent = errorMessage;
        return false;
    }
    else{
    errorDisplay.textContent = "";
    return true;
    }



}

// Function to validate phone number
function validatePhoneNumber(fieldName) {
    var inputField = document.getElementsByName(fieldName)[0];
    var inputControl = inputField.parentElement;
    var errorDisplay = inputControl.querySelector('.error');
    var phoneNumber = inputField.value.trim();
    var phoneNumberPattern = /^\d+$/; // Regular expression to match only digits
    
    // Check if the phone number contains only digits
    if (!phoneNumberPattern.test(phoneNumber)) {
        var errorMessage = "Phone number should only contain digits";
        inputControl.classList.add('error');
        inputControl.classList.remove('success');
        errorDisplay.textContent = errorMessage;
        return false;
    } else {
        errorDisplay.textContent = "";
        return true;
    }
}
