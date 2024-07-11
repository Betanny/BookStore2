<?php
// Include database connection file
require_once '../Shared Components/dbconnection.php';
include '../Shared Components/logger.php';

// Start session
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../Registration/login.php");
    exit();
}

// Get user ID and category from session
$user_id = $_SESSION['user_id'];
$category = $_SESSION['category'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="seller.css">
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

</head>

<body>
    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components/headerdispatcher.php"
        ?>
    <div class="productsadd-container">
        <section class="progress-bar">
            <ul class="progress-bar-steps">
                <li id="step1" class="progress-step current-item">
                    <span class="progress-count">1</span>
                    <span class="progress-label">Basic Details</span>
                </li>
                <li id="step2" class="progress-step">
                    <span class="progress-count">2</span>
                    <span class="progress-label ">Additional Details</span>
                </li>
                <li id="step3" class="progress-step">
                    <span class="progress-count">3</span>
                    <span class="progress-label">Pricing</span>
                </li>
                <li id="step4" class="progress-step">
                    <span class="progress-count">4</span>
                    <span class="progress-label">Upload Images</span>
                </li>
            </ul>

        </section>
        <div class="form">
            <form action="addproducts.php" method="post" id="Add-products" enctype="multipart/form-data">
                <input type="hidden" name="user_type">
                <div class="products-container">

                    <div id="basic-details" class="step-container">
                        <div id="contHeader" class="sec-cont">
                            <h4>Basic Details</h4>
                        </div>
                        <div class="input-box">
                            <div class="inputcontrol">
                                <label for="BookTitle">Book Title</label>
                                <input type="text" class="inputfield" name="booktitle" />
                                <div class="error"></div>
                            </div>
                        </div>
                        <div class="two-forms">
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="Author">Author</label>
                                    <input type="text" class="inputfield" name="author" />
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="Publisher">Publisher</label>
                                    <input type="text" class="inputfield" name="publisher" />
                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="two-forms">
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="Genre">Genre</label>
                                    <select class="dropbtn" id="book-categories" name="genre">
                                        <option style="display: none;" value=""></option>
                                        <option value="science">Science and Technology</option>
                                        <option value="mathematics">Mathematics</option>
                                        <option value="social_studies">Social Studies</option>
                                        <option value="languages">Languages</option>
                                        <option value="religious_education">Religious Education</option>
                                        <option value="practicals">Practical and Creative Subjects</option>
                                        <option value="physical_health">Physical Education and Health</option>
                                        <option value="environmental">Environmental Studies</option>
                                    </select>
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="Languages">languages</label>
                                    <select class="dropbtn" name="languages">
                                        <option value="english">English</option>
                                        <option value="kiswahili">Kiswahili</option>
                                        <option value="indigenous_luo">Luo</option>
                                        <option value="indigenous_kikuyu">Kikuyu</option>
                                        <option value="indigenous_kalenjin">Kalenjin</option>
                                        <option value="indigenous_luhya">Luhya</option>
                                        <option value="indigenous_kamba">Kamba</option>
                                        <option value="foreign_french">French</option>
                                        <option value="foreign_german">German</option>
                                        <option value="foreign_chinese">Chinese</option>
                                    </select>

                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="two-forms">
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="ISBN">ISBN number</label>
                                    <input type="text" class="inputfield" name="ISBN" />
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="Edition">Edition</label>
                                    <input type="text" class="inputfield" name="edition" placeholder="1st Edition" />
                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="additional-details" class="step-container">
                        <div id="contHeader" class="sec-cont">
                            <h4>Additional Details</h4>
                        </div>
                        <div class="two-forms">
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="subjects">Subject</label>
                                    <select class="inputfield" id="subjects" name="subjects">

                                    </select>
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="Grade">Grade</label>
                                    <select class="dropbtn" name="grade">
                                        <option value="baby_class">Baby Class</option>
                                        <option value="pre_primary_1">Pre-Primary 1</option>
                                        <option value="pre_primary_2">Pre-Primary 2</option>
                                        <option value="grade_1">Grade 1</option>
                                        <option value="grade_2">Grade 2</option>
                                        <option value="grade_3">Grade 3</option>
                                        <option value="grade_4">Grade 4</option>
                                        <option value="grade_5">Grade 5</option>
                                        <option value="grade_6">Grade 6</option>
                                        <option value="grade_7">Grade 7</option>
                                        <option value="grade_8">Grade 8</option>
                                        <option value="form_1">Form 1</option>
                                        <option value="form_2">Form 2</option>
                                        <option value="form_3">Form 3</option>
                                        <option value="form_4">Form 4</option>
                                        <option value="form_5">Form 5</option>
                                        <option value="form_6">Form 6</option>
                                        <option value="year_1">Year 1</option>
                                        <option value="year_2">Year 2</option>
                                        <option value="year_3">Year 3</option>
                                        <option value="year_4">Year 4</option>
                                        <option value="year_5">Year 5</option>
                                        <option value="year_6">Year 6</option>
                                        <option value="year_7">Year 7</option>
                                        <option value="year_8">Year 8</option>
                                        <option value="year_9">Year 9</option>
                                        <option value="year_10">Year 10</option>
                                        <option value="year_11">Year 11</option>
                                        <option value="year_12">Year 12</option>
                                        <option value="year_13">Year 13</option>

                                    </select>
                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="two-forms">
                            <div class="form-group">
                                <div class="inputcontrol radio-container">
                                    <label for="series">Is it part of a series?</label>
                                    <input type="radio" class="rdlbutton" id="series" name="series" value="yes"
                                        onclick="toggleTextBox(true)"> Yes
                                    <input type="radio" class="rdlbutton" id="series" name="series" value="no"
                                        onclick="toggleTextBox(false)"> No
                                    <div id="textBoxContainer" style="display:none;">
                                        <label for="textBox">Which One:</label>
                                        <input type="text" class="inputfield" id="textBox" name="textBox">
                                    </div>
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inputcontrol radio-container">
                                    <label for="covertype">Cover Type</label>
                                    <input type="radio" class="rdlbutton" id="covertype" name="covertype"
                                        value="Hardcover"> Hardcover
                                    <input type="radio" class="rdlbutton" id="covertype" name="covertype"
                                        value="SoftCover"> SoftCover
                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="two-forms">
                            <div class="form-group">
                                <div class="inputcontrol radio-container">
                                    <label for="damaged">Is it part of a damaged?</label>
                                    <input type="radio" class="rdlbutton" id="damaged" name="damaged" value="yes"
                                        onclick="toggleTextBox(true)"> Yes
                                    <input type="radio" class="rdlbutton" id="damaged" name="damaged" value="no"
                                        onclick="toggleTextBox(false)"> No
                                    <div id="textBoxContainer" style="display:none;">
                                        <label for="textBox">What are issues</label>
                                        <input type="text" id="textBox" name="textBox">
                                    </div>
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="pages">Number of Pages</label>
                                    <input type="text" class="inputfield" name="pages" />
                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="input-box">
                            <div class="inputcontrol">
                                <label for="details">Describe the book</label>
                                <textarea class="inputfield" name="details" style="height: 100px;"></textarea>
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>

                    <div id="pricing" class="step-container">
                        <div id="contHeader" class="sec-cont">
                            <h4>Pricing</h4>
                        </div>
                        <!--Retail-->
                        <div class="input-box">
                            <div class="inputcontrol">
                                <label for="retailprice">Retail Price(1 copy)</label>
                                <input type="text" class="inputfield" name="retailprice" />
                                <div class="error"></div>
                            </div>
                        </div>
                        <h4>Bulk(More than one)</h4>
                        <div class="two-forms">
                            <div class="input-box">
                                <div class="inputcontrol">
                                    <label for="priceinbulk">Price</label>
                                    <input type="text" class="inputfield" name="priceinbulk" />
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="inputcontrol">
                                    <label for="mininbulk">Minimum number of copies in Bulk</label>
                                    <input type="text" class="inputfield" name="mininbulk" />
                                    <div class="error"></div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div id="uploading-images" class="step-container">
                        <h4>Front page and Back Page</h4>
                        <div class="front-back-container">
                            <div class="image-container">
                                <div class="inputcontrol">
                                    <label class="regular-label">Add the Front Cover of your book</label>
                                    <label class="img-label" for="front-cover">Upload front cover Image</label>
                                    <input type="file" id="front-cover" name="Front-cover"
                                        accept=".jpg,.jpeg,.png,gif" />
                                    <img class="book-images" id="front-image-preview" src="">
                                    <div class="error"></div>
                                </div>
                            </div>

                            <div class="image-container">
                                <div class="inputcontrol">
                                    <label class="regular-label">Add the Back Cover of your book</label>
                                    <label class="img-label" for="back-cover">Select Image</label>
                                    <input type="file" id="back-cover" name="Back-cover" accept=".jpg,.jpeg,.png,gif">
                                    <img class="book-images" id="back-image-preview" src="">
                                    <div class="error"></div>
                                </div>
                            </div>

                        </div>
                        <h4>Please upload other relevant images of your Book</h4>
                        <h5>If the book is damaged please include the damaged page</h5>
                        <div class="other-pages-container">
                            <div class="image-container">
                                <div class="inputcontrol">
                                    <label class="regular-label no-asterisk">Relevant page</label>
                                    <label class="img-label no-asterisk" for="relevant-page1">Upload Image</label>
                                    <input type="file" id="relevant-page1" name="relevant-page1"
                                        accept=".jpg,.jpeg,.png,gif">
                                    <img class="book-images" id="relevant-page1-preview" src="">
                                </div>
                            </div>

                            <div class="image-container">
                                <div class="inputcontrol">
                                    <label class="regular-label no-asterisk">Relevant page</label>
                                    <label class="img-label no-asterisk" for="relevant-page2">Select Image</label>
                                    <input type="file" id="relevant-page2" name="relevant-page2"
                                        accept=".jpg,.jpeg,.png,gif">
                                    <img class="book-images" id="relevant-page2-preview" src="">
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="image-container">
                                <div class="inputcontrol">
                                    <label class="regular-label no-asterisk">Relevant page</label>
                                    <label class="img-label no-asterisk" for="relevant-page3">Select Image</label>
                                    <input type="file" id="relevant-page3" name="relevant-page3"
                                        accept=".jpg,.jpeg,.png,gif">
                                    <img class="book-images" id="relevant-page3-preview" src="">
                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>





                    </div>
                    <div class="scroll-sect">
                        <button type="button" class="button" id="prev" onclick="previousStep()">Previous</button>
                        <button type="button" class="button" id="next" onclick="nextStep()">Next</button>
                        <button type="submit" class="button" name="submit" id="submit">Submit</button>

                    </div>
                </div>


            </form>
        </div>
    </div>


</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        handleImageChange(document.getElementById('front-cover'), 'front-image-preview');
        handleImageChange(document.getElementById('back-cover'), 'back-image-preview');
        handleImageChange(document.getElementById('relevant-page1'), 'relevant-page1-preview');
        handleImageChange(document.getElementById('relevant-page2'), 'relevant-page2-preview');
        handleImageChange(document.getElementById('relevant-page3'), 'relevant-page3-preview');

        // You can add more image inputs and their respective preview IDs here if needed

    });

    // Function to handle image changes for any image input element
    function handleImageChange(inputElement, imagePreviewId) {
        inputElement.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(imagePreviewId).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }





    // // //Subjects based on book category
    // // Function to add subjects to the dropdown
    // function addSubjectsToDropdown(subjects) {
    //     var subjectsDropdown = document.getElementById('subjects');

    //     // Clear existing options
    //     subjectsDropdown.innerHTML = '';

    //     // Add new options based on the subjects array
    //     subjects.forEach(function(subject) {
    //         var option = document.createElement('option');
    //         option.value = subject.toLowerCase().replace(/\s/g, '_'); // Convert subject name to lowercase and replace spaces with underscores
    //         option.textContent = subject; // Set the visible text (e.g., "Algebra")
    //         subjectsDropdown.appendChild(option);
    //     });
    // }

    // // Event listener for the book category selection change
    // document.addEventListener('DOMContentLoaded', function() {
    //     document.getElementById('book-categories').addEventListener('change', function() {
    //         var bookCategory = this.value;

    //         // Call the function to populate subjects based on the selected book category
    //         switch(bookCategory) {
    //             case 'mathematics':
    //                 addSubjectsToDropdown(['Algebra', 'Geometry', 'Trigonometry', 'Calculus']);
    //                 break;
    //             case 'science':
    //                 addSubjectsToDropdown(['Physics', 'Chemistry', 'Biology', 'Environmental Science']);
    //                 break;
    //             case 'social_studies':
    //                 addSubjectsToDropdown(['History', 'Geography', 'Civics', 'Economics']);
    //                 break;
    //             case 'languages':
    //                 addSubjectsToDropdown(['English Language', 'Kiswahili Language', 'French', 'Spanish']);
    //                 break;
    //             case 'religious_education':
    //                 addSubjectsToDropdown(['Bible Studies', 'Quran Studies', 'Hindu Scriptures']);
    //                 break;
    //             case 'practicals':
    //                 addSubjectsToDropdown(['Home Science', 'Art & Craft', 'Agriculture']);
    //                 break;
    //             case 'physical_health':
    //                 addSubjectsToDropdown(['Physical Education', 'Health Education', 'Hygiene & Nutrition']);
    //                 break;
    //             case 'environmental':
    //                 addSubjectsToDropdown(['Ecology', 'Conservation', 'Sustainability']);
    //                 break;
    //             default:
    //                 // If no book category matches, display a default message or behavior
    //                 var defaultOption = document.createElement('option');
    //                 defaultOption.value = "";
    //                 defaultOption.text = "No subjects available";
    //                 subjectsDropdown.appendChild(defaultOption);
    //         }
    //     });
    // });


    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('book-categories').addEventListener('change', function () {
            var bookCategory = this.value;
            var subjectsDropdown = document.getElementById('subjects');

            // Clear existing options
            subjectsDropdown.innerHTML = '';

            // Populate subjects based on the selected book category
            switch (bookCategory) {
                case 'mathematics':
                    addSubjectsToDropdown(['Mathematics']);
                    break;
                case 'science':
                    addSubjectsToDropdown(['Physics', 'Chemistry', 'Biology', 'Environmental Science']);
                    break;
                case 'social_studies':
                    addSubjectsToDropdown(['History', 'Geography', 'Civics', 'Economics']);
                    break;
                case 'languages':
                    addSubjectsToDropdown(['English Language', 'Kiswahili Language', 'French', 'Spanish']);
                    break;
                case 'religious_education':
                    addSubjectsToDropdown(['Bible Studies', 'Quran Studies', 'Hindu Scriptures']);
                    break;
                case 'practicals':
                    addSubjectsToDropdown(['Home Science', 'Art & Craft', 'Agriculture']);
                    break;
                case 'physical_health':
                    addSubjectsToDropdown(['Physical Education', 'Health Education',
                        'Hygiene & Nutrition'
                    ]);
                    break;
                case 'environmental':
                    addSubjectsToDropdown(['Ecology', 'Conservation', 'Sustainability']);
                    break;
                default:
                    // If no book category matches, display a default message or behavior
                    var defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.text = 'No subjects available. Please go back and pick the correct Genre';
                    subjectsDropdown.appendChild(defaultOption);
                    break;
            }
        });

        function addSubjectsToDropdown(subjects) {
            var subjectsDropdown = document.getElementById('subjects');
            subjects.forEach(function (subject) {
                var option = document.createElement('option');
                option.value = subject.toLowerCase();
                option.textContent = subject;
                subjectsDropdown.appendChild(option);
            });
        }
    });


    function toggleTextBox(show) {
        var textBoxContainer = document.getElementById("textBoxContainer");
        textBoxContainer.style.display = show ? "block" : "none";
    }




    var step1 = document.getElementById("basic-details");
    var step2 = document.getElementById("additional-details");
    var step3 = document.getElementById("pricing");
    var step4 = document.getElementById("uploading-images");
    var prevbtn = document.getElementById("prev");
    var nextbtn = document.getElementById("next");
    var submitbtn = document.getElementById("submit");
    var currentStep = 1; // Current step of the form

    document.addEventListener("DOMContentLoaded", function () {
        showStep(currentStep);
    });

    function previousStep() {
        if (currentStep > 1) {
            currentStep--;
        }
        showStep(currentStep);
    }



    function nextStep() {
        var isValid = validateCurrentStep();
        console.log('isValid:', isValid);
        console.log('currentStep:', currentStep);
        if (isValid) {
            if (currentStep < 4) {
                currentStep++;
                console.log('Moving to step', currentStep);
                showStep(currentStep);
            } else if (currentStep === 4) {
                console.log('Submitting form');
                // If the current step is the last step, submit the form
                document.getElementById('Add-products').submit();
            }
        }
    }



    function showStep(step) {
        var progressSteps = document.querySelectorAll('.progress-step');
        progressSteps.forEach(step => {
            step.classList.remove('current-item');
        });
        switch (step) {
            case 1:
                step1.style.display = "block";
                step2.style.display = "none";
                step3.style.display = "none";
                step4.style.display = "none";
                prevbtn.style.display = "none";
                submitbtn.style.display = "none"
                document.getElementById('step1').classList.add('current-item');

                break;
            case 2:
                step1.style.display = "none";
                step2.style.display = "block";
                step3.style.display = "none";
                step4.style.display = "none";
                prevbtn.style.display = "block";
                nextbtn.style.display = "block";
                submitbtn.style.display = "none"
                document.getElementById('step2').classList.add('current-item');




                break;
            case 3:
                step1.style.display = "none";
                step2.style.display = "none";
                step3.style.display = "block";
                step4.style.display = "none";
                prevbtn.style.display = "block";
                nextbtn.style.display = "block";
                submitbtn.style.display = "none"
                document.getElementById('step3').classList.add('current-item');




                break;
            case 4:
                step1.style.display = "none";
                step2.style.display = "none";
                step3.style.display = "none";
                step4.style.display = "block";
                nextbtn.style.display = "none";
                prevbtn.style.display = "block";
                submitbtn.style.display = "block"
                document.getElementById('step4').classList.add('current-item');



                break;
            default:
                break;
        }
    }



    /*--------------------Form validation --------------------*/
    //     document.getElementById("Add-products").addEventListener('submit', function (e) {
    //     e.preventDefault();
    //     // Determine user type based on UI state
    //     validateFileUploads();
    // });

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

    function validateRequiredNumber(fieldName, errorMessage) {
        var inputField = document.getElementsByName(fieldName)[0];
        var fieldValue = inputField.value.trim();
        var inputControl = inputField.parentElement;
        var errorDisplay = inputControl.querySelector('.error');
        var numberPattern = /^\d+$/; // Regular expression to match only digits

        // Check if the field is empty or not a valid number
        if (fieldValue === '' || !numberPattern.test(fieldValue)) {
            inputControl.classList.add('error');
            inputControl.classList.remove('success');
            errorDisplay.textContent = errorMessage;
            return false;
        } else {
            errorDisplay.textContent = "";
            return true;
        }
    }

    function validateFileUpload(fieldName, errorMessage) {
        var fileInput = document.getElementsByName(fieldName);
        var inputControl = fileInput.parentElement;
        // var errorDisplay = inputControl.querySelector('.error');
        var files = fileInput.files;

        if (files.length === 0) {
            // errorDisplay.textContent = errorMessage;
            return false;
        } else {
            // errorDisplay.textContent = "";
            return true;
        }
    }
    // function validateFileUpload(fieldName, errorMessage) {
    //     var fileInputs = document.getElementsByName(fieldName);
    //     if (fileInputs.length === 0) {
    //         console.error("No elements found with name:", fieldName);
    //         return false;
    //     }

    //     var fileInput = fileInputs[0]; // Assuming you only have one file input with the given name
    //     var errorDisplay = fileInput.parentElement.querySelector('.error');

    //     if (!errorDisplay) {
    //         console.error("Error display element not found.");
    //         return false;
    //     }

    //     var files = fileInput.files;

    //     if (files.length === 0) {
    //         errorDisplay.textContent = errorMessage;
    //         return false;
    //     } else {
    //         errorDisplay.textContent = "";
    //         return true;
    //     }
    // }



    function validateCurrentStep() {
        switch (currentStep) {
            case 1:
                return validateBasicDetails();
            case 2:
                return validateAdditionalDetails();
            case 3:
                return validatePricing();
            case 4:
                return validateFileUploads();
            default:
                return true;
        }
    }

    function validateBasicDetails() {
        var isValid = true;
        isValid = validateField('booktitle', 'Book Title is required') && isValid;
        isValid = validateField('author', 'Author is required') && isValid;
        isValid = validateField('publisher', 'Publisher is required') && isValid;
        isValid = validateField('ISBN', 'ISBN number is required') && isValid;
        isValid = validateRequiredNumber('ISBN', 'ISBN must be a number') && isValid;
        isValid = validateField('edition', 'Edition is required') && isValid;
        return isValid;
    }

    function validateAdditionalDetails() {
        var isValid = true;
        isValid = validateField('pages', 'Number of Pages is required') && isValid;
        isValid = validateRequiredNumber('pages', 'Number of pages must be a number') && isValid;
        isValid = validateField('details', 'Description is required') && isValid;
        isValid = validateField('subjects', 'Subject is required') && isValid;
        isValid = validateField('grade', 'Grade is required') && isValid;
        return isValid;
    }

    function validatePricing() {
        var isValid = true;
        isValid = validateField('retailprice', 'Retail Price is required') && isValid;
        isValid = validateRequiredNumber('retailprice', 'Retail Price must be a number') && isValid;
        isValid = validateField('priceinbulk', 'Price in Bulk is required') && isValid;
        isValid = validateRequiredNumber('priceinbulk', 'Price in Bulk must be a number') && isValid;
        isValid = validateField('mininbulk', 'Minimum number of copies in Bulk is required') && isValid;
        isValid = validateRequiredNumber('mininbulk', 'Minimum number of copies in Bulk must be a number') && isValid;
        return isValid;
    }

    function validateFileUploads() {
        var isValid = true;
        isValid = validateFileUpload('Front-cover', 'Front cover image is required') && isValid;
        isValid = validateFileUpload('Back-cover', 'Back cover image is required') && isValid;
        return isValid;
    }
</script>

</html>