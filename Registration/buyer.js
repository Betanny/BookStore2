var x = document.getElementById("individual-btn"); // Accessing the first element with the name "individual-btn"
var y = document.getElementById("organization-btn"); // Accessing the first element with the name "organization-btn"
var indivCont = document.getElementById("individual-container");
var orgCont = document.getElementById("organization-container");
// var user_type = document.getElementsByName("user_type");
var user_type = document.querySelector("input[name='user_type']");

document.addEventListener("DOMContentLoaded", function() {
    fetch('/Shared Components/header.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });
        fetch('/Shared Components/footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        });
        x.click(); // Simulate click on individual button when the document is loaded

});






    // Get the form element

    // Add event listener for form submission
    

function individual() {
    x.classList.remove('btn');
    x.classList.add('active');
    y.classList.remove('active');
    y.classList.add('btn');
    indivCont.style.display = 'block'; // Set display property to block
    orgCont.style.display = 'none'; // Set display property to none
    // var user_type= "Individual";
    user_type.value="Individual";



}

function organization() {
    y.classList.remove('btn');
    y.classList.add('active');
    x.classList.remove('active');
    x.classList.add('btn');
    indivCont.style.display = 'none'; // Set display property to none
    orgCont.style.display = 'block'; // Set display property to block
    user_type.value="Organization";
}
localStorage.setItem('user_type', user_type.value);




// Add event listener for form submission
document.getElementById("BuyerForm").addEventListener('submit', function(e) {
    // Prevent the default form submission
    e.preventDefault();
    submitForm(user_type.value)
});
function submitForm(user_type){
    var isValid= false;
    switch(user_type){
        case "Individual":
            isValid = validateIndividualForm();
            break;
        case "Organization":
            isValid = validateOrganizationForm();
            break;

    }
  if (isValid){
    document.getElementById("BuyerForm").submit();
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
// Individual form functions
function validateIndividualForm() {
    var isValid = true;
    // Validate fields for Individual form
    isValid = validateField('fname', 'First Name is required') && isValid;
    isValid = validateField('lname', 'Last Name is required') && isValid;
    isValid = validateEmail('email') && isValid;
    isValid = validateField('phone', 'Phone is required') && isValid;
    isValid = validatePhoneNumber('phone') && isValid; // Validate phone number field
    isValid = validateField('county', 'County of residence is required') && isValid;
    isValid = validateField('address', 'Address is required') && isValid;
    isValid = validateField('password', 'Password is required') && isValid;
    isValid = validateField('password2', 'Confirm Password is required') && isValid;
    isValid = validatePassword('password', 'password2') && isValid;

    return isValid;
}

// Organization form functions
function validateOrganizationForm() {
    var isValid = true;
    // Validate fields for Organization form
    isValid = validateField('OrgName', 'Organization Name is required') && isValid;
    isValid = validateEmail('OrgEmail') && isValid;
    isValid = validateField('OrgPhone', 'Organization Phone is required') && isValid;
    isValid = validatePhoneNumber('OrgPhone') && isValid; // Validate phone number field
    isValid = validateField('county', 'Organization County of residence is required') && isValid;
    isValid = validateField('address', 'Organization Address is required') && isValid;
    isValid = validateField('cfname', 'Contact Person First Name is required') && isValid;
    isValid = validateField('clname', 'Contact Person Last Name is required') && isValid;
    isValid = validateEmail('cemail', 'Contact Person Email is required') && isValid;
    isValid = validateField('cphone', 'Contact Person Phone is required') && isValid;
    isValid = validateField('password', 'Password is required') && isValid;
    isValid = validateField('password2', 'Confirm Password is required') && isValid;
    isValid = validatePassword('password', 'password2') && isValid;

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
