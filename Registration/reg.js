const firstname = document.getElementsByName('fname');
const lastname = document.getElementsByName('lname');
const email = document.getElementsByName('email');
const phone = document.getElementsByName('phone');
const county = document.getElementsByName('county');
const address = document.getElementsByName('address');
const password = document.getElementsByName('password');
const password2 = document.getElementsByName('password2');
const orgName = document.getElementsByName('OrgName');
const orgEmail = document.getElementsByName('OrgEmail');
const orgPhone = document.getElementsByName('OrgPhone');
const cfirstName = document.getElementsByName('cfname');
const clastName = document.getElementsByName('clname');
const form = document.getElementsByName('form');

const individualFields = [
    ...firstname,
    ...lastname,
    ...email,
    ...phone,
    ...county,
    ...address,
    ...password,
    ...password2,
    ...cfirstName,
    ...clastName
];

form.addEventListener('submit', e => {
    e.preventDefault();
    const formId = e.target.id;
    const userType = e.target.querySelector('[name="userType"]').value;

    if (formId) {
        validateInputs(formId, userType);
    }
});

const setError = (element, message) => {
    const inputfield = element.parentElement;
    const errorDisplay = inputfield.querySelector('.error');
    errorDisplay.innerText = message;
    inputfield.classList.add('error');
    inputfield.classList.remove('success');
}

const setSuccess = (element) => {
    const inputfield = element.parentElement;
    const errorDisplay = inputfield.querySelector('.error');
    inputfield.classList.remove('error');
    inputfield.classList.add('success');
}

const validateInputs = (formId, userType) => {
    switch (formId) {
        case 'BuyerForm':
            validateBuyer(userType);
            break;
        case 'SellerForm':
            validateSeller(userType);
            break;
        case 'LoginForm':
            validateLogin();
            break;
    }
}

const validateFields = (element, value, field) => {
    const emailRegex = /^\S+@\S+\.\S+$/;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{10,}$/;
    const phoneRegex = /^\d{10}$/;

    switch (field) {
        case 'email':
            if (!emailRegex.test(value)) {
                setError(element, `${field} must be a valid email address`);
            } else {
                setSuccess(element);
            }
            break;
        case 'password':
            if (!passwordRegex.test(value)) {
                setError(element, `${field} must be at least 10 characters long and contain mixed characters`);
            } else {
                setSuccess(element);
            }
            break;
        case 'phone':
            if (!phoneRegex.test(value)) {
                setError(element, `${field} must contain exactly 10 digits`);
            } else {
                setSuccess(element);
            }
            break;
        default:
            if (value.length < 3) {
                setError(element, `${field} must be at least 3 characters long`);
            } else {
                setSuccess(element);
            }
            break;
    }
}

function validateBuyer(userType) {
    if (userType === 'individual') {
        individualFields.forEach(field => {
            validateFields(field, field.value, field.name);
        });
    }
}

function validateSeller(userType) {
    // Implement validation for seller based on userType
}

function validateLogin() {
    // Implement login validation
}
