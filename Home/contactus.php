<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="/Images/Logo/Logo2.png" type="image/png">

</head>

<body>

    <?php
    // Include the header dispatcher file to handle inclusion of the appropriate header
    include "../Shared Components\headerdispatcher.php"
        ?>
    <div class="contactus-container">

        <div class="upper-container">
            <h4>Get In Touch</h4>
            <p>Have questions or need assistance with your orders?</p>
            <p>Reach out to us via email, phone, or the contact form below.</p>


            <div class="contact-container">
                <div class="contact-info">
                    <div class="contact">
                        <div class="icon">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <div class="info">
                            <h5>Email Address</h5>
                            <p>smartcbcinfo@gmail.com</p>
                        </div>
                    </div>
                    <div class="contact">
                        <div class="icon">
                            <i class="fa-solid fa-mobile"></i>
                        </div>
                        <div class="info">
                            <h5>Phone Number</h5>
                            <p>+254765895467</p>
                        </div>
                    </div>
                    <div class="contact">
                        <div class="icon">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <div class="info">
                            <h5>Email Address</h5>
                            <p>smartcbcinfo@gmail.com</p>
                        </div>
                    </div>


                </div>

                <div class="contact-form">
                    <p>Please fill in your details</p>
                    <div class="inputcontrol">
                        <label for="Name">Name</label>
                        <input type="text" class="inputfield" name="name" />
                        <div class="error"></div>
                    </div>
                    <div class="inputcontrol">
                        <label for="Email">Email</label>
                        <input type="text" class="inputfield" name="email" />
                        <div class="error"></div>
                    </div>
                    <div class="inputcontrol">
                        <label for="Message">Message</label>
                        <textarea class="inputfield" name="message" style="height: 90px;"></textarea>
                        <div class="error"></div>
                    </div>
                    <button type="submit" id="message" class="message-btn">Send Message</button>

                </div>


            </div>

            <div class="faqs-container">
                <h2>Frequently Asked Questions (FAQs)</h2>
                <div class="faq">
                    <div class="question" onclick="toggleAnswer(1)"><i class="fa-solid fa-plus"></i>How can authors,
                        publishers, and manufacturers post their products on your platform?</div>
                    <div class="answer" id="answer1">Authors, publishers, and manufacturers can post their products by
                        [provide steps or instructions here].</div>
                </div>

                <div class="faq">
                    <div class="question" onclick="toggleAnswer(2)"><i class="fa-solid fa-plus"></i>What are the
                        benefits of utilizing the direct selling feature for authors, publishers, and manufacturers?
                    </div>
                    <div class="answer" id="answer2">The direct selling feature offers benefits such as [list benefits
                        here].</div>
                </div>

                <div class="faq">
                    <div class="question" onclick="toggleAnswer(3)"><i class="fa-solid fa-plus"></i>How does the bulk
                        ordering process work, and what are the advantages for clients who order in bulk?</div>
                    <div class="answer" id="answer3">The bulk ordering process involves [explain the process]. Clients
                        who order in bulk enjoy advantages such as [list advantages here].</div>
                </div>

                <div class="faq">
                    <div class="question" onclick="toggleAnswer(4)"><i class="fa-solid fa-plus"></i>Can clients order
                        single items, and how does the pricing compare to bulk orders?</div>
                    <div class="answer" id="answer4">Yes, clients can order single items. The pricing for single items
                        may differ from bulk orders and is typically [explain pricing difference here].</div>
                </div>

                <div class="faq">
                    <div class="question" onclick="toggleAnswer(5)"><i class="fa-solid fa-plus"></i>Are there any
                        specific requirements or criteria for products to be listed on your platform?</div>
                    <div class="answer" id="answer5">Yes, products listed on our platform must meet certain requirements
                        or criteria, including [list requirements or criteria here].</div>
                </div>
                <!--  
    <div class="faq">
        <div class="question" onclick="toggleAnswer(6)">6. What measures are in place to ensure the quality and authenticity of products listed on your platform?</div>
        <div class="answer" id="answer6">We have stringent measures in place to ensure the quality and authenticity of products, such as [describe quality control measures here].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(7)">7. How do clients place orders, and what payment options are available?</div>
        <div class="answer" id="answer7">Clients can place orders by [explain order placement process]. Payment options available include [list payment options here].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(8)">8. What is the process for shipping and delivery, especially for bulk orders?</div>
        <div class="answer" id="answer8">The shipping and delivery process involves [explain shipping and delivery process, especially for bulk orders].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(9)">9. Are there any discounts or special offers available for schools or educational institutions ordering from your platform?</div>
        <div class="answer" id="answer9">Yes, we offer discounts or special offers for schools or educational institutions ordering from our platform. These offers may include [describe discounts or special offers].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(10)">10. How does your platform support communication between sellers and clients, particularly for inquiries or custom orders?</div>
        <div class="answer" id="answer10">Our platform facilitates communication between sellers and clients through [describe communication channels or features].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(11)">11. Do you offer any additional services, such as book recommendations or educational resources, to support CBC schools?</div>
        <div class="answer" id="answer11">Yes, we offer additional services such as book recommendations or educational resources to support CBC schools. These services include [describe additional services offered].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(12)">12. What steps do you take to ensure fair pricing for both sellers and clients on your platform?</div>
        <div class="answer" id="answer12">We ensure fair pricing for both sellers and clients on our platform through [describe pricing policies or mechanisms].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(13)">13. Are there any geographical limitations or restrictions on where orders can be delivered within Kenya?</div>
        <div class="answer" id="answer13">Yes, there may be geographical limitations or restrictions on where orders can be delivered within Kenya. Please [provide information on delivery restrictions or limitations].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(14)">14. How often are new products added to your platform, and how do you notify clients about these additions?</div>
        <div class="answer" id="answer14">New products are added to our platform [describe frequency]. We notify clients about these additions through [describe notification channels or methods].</div>
    </div>
    
    <div class="faq">
        <div class="question" onclick="toggleAnswer(15)">15. Can clients provide feedback or reviews on products purchased through your platform, and how is this feedback utilized?</div>
        <div class="answer" id="answer15">Yes, clients can provide feedback or reviews on products purchased through our platform. This feedback is utilized to [describe how feedback is utilized, such as improving services or product selection].</div>
    </div> -->

            </div>

        </div>









    </div>















</body>

<script>
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

});

function toggleAnswer(id) {
    var answer = document.getElementById("answer" + id);
    if (answer.style.display === "none") {
        answer.style.display = "block";
    } else {
        answer.style.display = "none";
    }
}
window.onload = function() {
    var answers = document.querySelectorAll('.answer');
    answers.forEach(function(answer) {
        answer.style.display = "none";
    });
};
</script>

</html>