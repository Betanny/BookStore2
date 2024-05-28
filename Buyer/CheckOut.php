<?php require_once '../Shared Components/dbconnection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    //    Redirect to login page if not logged in 
    header("Location: ../Registration/login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
try {
    // Fetch cart data with product details from the database 
    $cartdatastmt = $db->prepare("
    SELECT c.*, b.title, b.front_page_image, b.price, b.priceinbulk, b.mininbulk, b.seller_id
    FROM cart c
    JOIN books b ON c.product_id = b.bookid
    WHERE c.client_id = :client_id
    ");
    $cartdatastmt->bindParam(':client_id', $_SESSION['user_id']);
    $cartdatastmt->execute();
    $cartItems = $cartdatastmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0;
    $discount = $totaldiscount = 0;
    $total = 0;

    $noOfItems = count($cartItems);

    // Calculate subtotal
    foreach ($cartItems as $item) {
        // Calculate total price for each item
        $totalprice = $item['quantity'] * $item['price'];
        // Add total price to subtotal
        if ($item['quantity'] >= $item['mininbulk']) {
            $discount = ($item['price'] * $item['quantity']) - ($item['priceinbulk'] * $item['quantity']);

        } else {
            $discount = 0;
        }
        $subtotal += $totalprice;
        $totaldiscount += $discount;
        $total = $subtotal - $discount;
    }




} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    // Redirect to an error page
    // header("Location: review_error.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="buyer.css">
    <link rel="stylesheet" href="/Shared Components/style.css">
    <link rel="stylesheet" href="/Registration/Stylesheet.css">
    <link rel="stylesheet" href="/Home/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Select Book</title>
</head>

<body>
    <div id="header-container"></div>
    <form id="order-form" action="place_order.php" method="post">
        <input type="hidden" name="cart_items" value='<?php echo json_encode($cartItems); ?>'>


        <div class="checkout-container">

            <div class="shoppingcart">

                <div class="table">
                    <div class="cart-header">
                        <div class="Product-cell">Product</div>
                        <div class="reg-cell">Quantity</div>
                        <div class="reg-cell">Type(Retail/Bulk)</div>
                        <div class="reg-cell">UnitPrice</div>
                        <div class="reg-cell">TotalPrice</div>
                    </div>
                    <div class="cart-details">

                        <?php foreach ($cartItems as $item): ?>
                        <input type="hidden" id="product_id" name="product_id"
                            value="<?php echo $item['product_id']; ?>">
                        <input type="hidden" id="seller_id" name="seller_id" value="<?php echo $item['seller_id']; ?>">


                        <div class=" cart-row">
                            <div class="Product-cell">
                                <div class="product_image">
                                    <img src="<?php echo str_replace('D:\xammp2\htdocs\BookStore2', '', $item['front_page_image']); ?>"
                                        alt=" Product Image">
                                </div>
                                <div class="product-name">
                                    <?php echo $item['title']; ?>
                                </div>
                            </div>

                            <div class="quantity reg-cell">
                                <!-- Pass the cart ID to JavaScript -->
                                <input type="number" id="quantity_<?php echo $item['cart_id']; ?>" class="input" min="1"
                                    placeholder="<?php echo $item['quantity']; ?>"
                                    onchange="updateQuantity(<?php echo $item['cart_id']; ?>)">
                            </div>


                            <div class="reg-cell">
                                <?php if ($item['quantity'] < $item['mininbulk']): ?>Retail<?php else: ?>Bulk<?php endif; ?>

                            </div>
                            <div class="reg-cell">
                                <?php echo $item['price']; ?>
                            </div>
                            <div class="reg-cell">
                                <?php echo $item['quantity'] * $item['price']; ?>
                            </div>
                            <!-- <i class="fa-solid fa-trash-can"
                                    onclick="deleteCartItem(<?php echo $item['cart_id']; ?>)"></i> -->
                            <div class="icon-cell">
                                <a href="#" class="delete-link" data-table="cart"
                                    data-pk="<?php echo $item['cart_id']; ?>" data-pk-name=" cart_id">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>


                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class=" cart-total">
                    <div class="return-container">
                        <button type="button" id="return-button" onclick="backToProducts();" class="return-button"><i
                                class="fa-solid fa-angle-left"></i>Continue
                            Shopping</button>
                    </div>
                    <div class="totals">
                        <p>SubTotal:
                            <?php echo $subtotal; ?>
                        </p>
                        <p> Discount:
                            <?php echo $totaldiscount; ?>
                        </p>
                        <h4>
                            Total:
                            <?php echo $total ?>
                        </h4>
                    </div>
                </div>


            </div>


            <div class="billing-container">

                <div class="payment-details">
                    <h4>Payment Info.</h4>
                    <h5>Payment Method</h5>
                    <div class="payment-buttons">
                        <button type="button" class="payment-button" id="mpesaButton"
                            onclick="togglePaymentFields('mpesa')">
                            <img src="/Images/Dummy/mpesa.png" alt="Mpesa">
                        </button>
                        <button type="button" class="payment-button" id="airtelmoneyButton"
                            onclick="togglePaymentFields('airtelmoney')">
                            <img src="airtelmoney_icon.png" alt="Airtel Money">
                        </button>
                        <button type="button" class="payment-button" id="cardButton"
                            onclick="togglePaymentFields('card')">
                            <img src="card_icon.png" alt="Card">
                        </button>
                    </div>
                    <input type="hidden" id="paymentMethod" name="paymentMethod" value="">
                    <input type="hidden" id="paymentNumber" name="paymentNumber" value="">



                    <div id="mpesaFields" class="input-box" style="display:none;">
                        <div class="inputcontrol">
                            <div class="error"></div>
                            <label for="mpesaNumber">Mpesa Number</label>
                            <input type="text" class="inputfield" id="mpesaNumber" name="mpesaNumber">
                        </div>

                        <div class="inputcontrol">
                            <div class="error"></div>
                            <label for="mpesaName">Mpesa Name</label>
                            <input type="text" class="inputfield" id="mpesaName" name="mpesaName">
                        </div>
                        <!--             
                    <div class="inputcontrol">
                        <label for="tillNumber">Till Number</label>
                        <input type="text" class="inputfield" id="tillNumber" name="tillNumber">
                        <div class="error"></div>
                    </div> -->
                    </div>

                    <div id="airtelmoneyFields" class="input-box" style="display:none;">
                        <div class="inputcontrol">
                            <div class="error"></div>
                            <label for="airtelNumber">Airtel Money Number</label>
                            <input type="text" class="inputfield" id="airtelNumber" name="airtelNumber">
                        </div>

                        <div class="inputcontrol">
                            <div class="error"></div>
                            <label for="airtelName">Airtel Money Name</label>
                            <input type="text" class="inputfield" id="airtelName" name="airtelName">
                        </div>

                        <!-- <div class="inputcontrol">
                        <label for="tillNumberAirtel">Till Number</label>
                        <input type="text" class="inputfield" id="tillNumberAirtel" name="tillNumberAirtel">
                        <div class="error"></div>
                    </div> -->
                    </div>

                    <div id="cardFields" class="input-box" style="display:none;">
                        <div class="inputcontrol">
                            <div class="error"></div>
                            <label for="cardName">Name on Card</label>
                            <input type="text" class="inputfield" id="cardName" name="cardName">
                        </div>

                        <div class="inputcontrol">
                            <div class="error"></div>
                            <label for="cardNumber">Card Number</label>
                            <input type="text" class="inputfield" id="cardNumber" name="cardNumber">
                        </div>
                    </div>




                </div>
                <div class="delivery-details">

                    <h4>Delivery Info.</h4>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="RecipientName">Recipient Name</label>
                            <input type="text" class="inputfield" name="RecipientName" />
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="shipping_address">Delivery Address</label>
                            <input type="text" class="inputfield" name="shipping_address" />
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="ContactNumber">Contact Number</label>
                            <input type="text" class="inputfield" name="ContactNumber" />
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="DeliveryMethod" class="no-asterisk">Delivery Method</label>
                            <div class="delivery-filter">
                                <select id="deliveryType" onchange="showCountyDropdown()">
                                    <option value="personal">Personal</option>
                                    <option value="company">Company</option>
                                </select>

                                <div id="companySection" class="companySection" style="display:none;">
                                    <label for="County" class="no-asterisk">County</label>
                                    <select id="county" onchange="showPrice()">
                                        <option value="">Select a county</option>
                                        <option value="Mombasa (bus)">Mombasa (bus)</option>
                                        <option value="Mombasa (train economy)">Mombasa (train economy)</option>
                                        <option value="Kwale">Kwale</option>
                                        <option value="Kilifi">Kilifi</option>
                                        <option value="Tana River">Tana River</option>
                                        <option value="Lamu">Lamu</option>
                                        <option value="Taita-Taveta">Taita-Taveta</option>
                                        <option value="Garissa">Garissa</option>
                                        <option value="Wajir">Wajir</option>
                                        <option value="Mandera">Mandera</option>
                                        <option value="Marsabit">Marsabit</option>
                                        <option value="Isiolo">Isiolo</option>
                                        <option value="Meru">Meru</option>
                                        <option value="Tharaka-Nithi">Tharaka-Nithi</option>
                                        <option value="Embu">Embu</option>
                                        <option value="Kitui">Kitui</option>
                                        <option value="Machakos">Machakos</option>
                                        <option value="Makueni">Makueni</option>
                                        <option value="Nyandarua">Nyandarua</option>
                                        <option value="Nyeri">Nyeri</option>
                                        <option value="Kirinyaga">Kirinyaga</option>
                                        <option value="Murang'a">Murang'a</option>
                                        <option value="Kiambu">Kiambu</option>
                                        <option value="Turkana">Turkana</option>
                                        <option value="West Pokot">West Pokot</option>
                                        <option value="Samburu">Samburu</option>
                                        <option value="Trans-Nzoia">Trans-Nzoia</option>
                                        <option value="Uasin Gishu">Uasin Gishu</option>
                                        <option value="Elgeyo-Marakwet">Elgeyo-Marakwet</option>
                                        <option value="Nandi">Nandi</option>
                                        <option value="Baringo">Baringo</option>
                                        <option value="Laikipia">Laikipia</option>
                                        <option value="Nakuru">Nakuru</option>
                                        <option value="Narok">Narok</option>
                                        <option value="Kajiado">Kajiado</option>
                                        <option value="Kericho">Kericho</option>
                                        <option value="Bomet">Bomet</option>
                                        <option value="Kakamega">Kakamega</option>
                                        <option value="Vihiga">Vihiga</option>
                                        <option value="Bungoma">Bungoma</option>
                                        <option value="Busia">Busia</option>
                                        <option value="Siaya">Siaya</option>
                                        <option value="Kisumu">Kisumu</option>
                                        <option value="Homa Bay">Homa Bay</option>
                                        <option value="Migori">Migori</option>
                                        <option value="Kisii">Kisii</option>
                                        <option value="Nyamira">Nyamira</option>
                                        <option value="Nairobi">Nairobi</option>
                                    </select>

                                    <div id="priceSection" class="priceSection">
                                        <h4>Price: </h4>

                                        <h4 id="price"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-box">
                        <div class="inputcontrol">
                            <label for="Instructions" class="no-asterisk">Delivery Instructions</label>
                            <textarea class="inputfield" style="height: 70px;" class="inputfield"
                                name="Instructions"></textarea>
                            <div class="error"></div>
                        </div>
                    </div>
                    <input type="hidden" id="deliveryprice">
                    <br>
                    <h4>
                        Total price:
                        <span id="totalPrice"></span> KSh
                    </h4>


                </div>
                <div class="return-container">
                    <button id="order-button" class="order-button">Place Order</button>
                </div>
            </div>
        </div>
    </form>

    <script>
    // Define a function to submit the form



    // Define a function to toggle payment fields
    function togglePaymentFields(paymentMethod) {
        console.log("Function called with payment method:", paymentMethod);
        document.getElementById('paymentMethod').value = paymentMethod;
        console.log(paymentMethod);



        var mpesaFields = document.getElementById("mpesaFields");
        var airtelmoneyFields = document.getElementById("airtelmoneyFields");
        var cardFields = document.getElementById("cardFields");

        mpesaFields.style.display = "none";
        airtelmoneyFields.style.display = "none";
        cardFields.style.display = "none";

        if (paymentMethod === "mpesa") {
            mpesaFields.style.display = "block";
            document.getElementById('paymentMethod').value = 'mpesa';
            document.getElementById('paymentNumber') = document.getElementById('mpesaNumber');
            console.log("mpesa");

        } else if (paymentMethod === "airtelmoney") {
            airtelmoneyFields.style.display = "block";
            document.getElementById('paymentMethod').value = 'airtelmoney';
            document.getElementById('paymentNumber').value = document.getElementById('airtelNumber').value;


        } else if (paymentMethod === "card") {
            cardFields.style.display = "block";
            document.getElementById('paymentMethod').value = 'card';
            document.getElementById('paymentNumber').value = document.getElementById('cardNumber').value;


        }
        // document.getElementById('paymentMethod').value = paymentMethod;



    }

    document.addEventListener("DOMContentLoaded", function() {
        fetch('header.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('header-container').innerHTML = data;
            });




        function updateSubtotalAndTotal() {
            var total = 0;
            var totalElements = document.querySelectorAll('.reg-cell[data-type="total-price"]');
            totalElements.forEach(function(element) {
                total += parseFloat(element.innerText);
            });
            document.getElementById('subtotal').innerText = total.toFixed(2);
            document.getElementById('total').innerText = total.toFixed(2);
        }


    });

    function updateQuantity(cartId) {
        // Get the input field value
        var quantity = document.getElementById('quantity_' + cartId).value;

        // Make an AJAX request to update the quantity in the database
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_to_cart.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Quantity updated successfully
                console.log('Quantity updated successfully');
                // You can optionally reload the page after successful update
                window.location.reload();
            } else {
                // Error updating quantity
                console.error('Error updating quantity:', xhr.statusText);
            }
        };
        xhr.onerror = function() {
            // Handle network errors
            console.error('Request failed');
        };
        // Send the cart ID and new quantity to the server
        xhr.send('cart_id=' + cartId + '&quantity=' + quantity);
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Get all elements with the class "delete-link"
        var deleteLinks = document.querySelectorAll('.delete-link');

        // Loop through each delete link
        deleteLinks.forEach(function(link) {
            // Add click event listener to each delete link
            link.addEventListener('click', function(event) {
                // Prevent the default behavior (i.e., following the href)
                event.preventDefault();

                // Get the table name, primary key column name, and primary key value from the data attributes
                var tableName = link.getAttribute('data-table');
                var primaryKey = link.getAttribute('data-pk');
                var pkName = link.getAttribute('data-pk-name');

                // Perform AJAX request to the delete script
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '/Shared Components/delete.php?table=' + tableName +
                    '&pk=' +
                    primaryKey +
                    '&pk_name=' + pkName, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Handle successful deletion (if needed)
                        // For example, you can remove the deleted row from the DOM
                        link.parentElement.parentElement.remove();
                    } else {
                        // Handle error (if needed)
                        console.error('Error:', xhr.statusText);
                    }
                };
                xhr.onerror = function() {
                    // Handle network errors (if needed)
                    console.error('Request failed');
                };
                xhr.send();
            });
        });
    });

    document.getElementById("order-form").addEventListener('submit', function(e) {
        // Prevent the default form submission
        e.preventDefault();
        validateForm();
        console.log("Starting to submit the form");

    });
    var isValid = false;

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

    // Individual form functions
    function validateForm() {
        var isValid = true;
        // Validate fields for Individual form
        isValid = validateField('RecipientName', 'Recipient Name is required') && isValid;
        isValid = validatePhoneNumber('ContactNumber') && isValid; // Validate phone number field
        isValid = validateField('ContactNumber', 'Phone is required') && isValid;
        isValid = validateField('shipping_address', 'Address is required') && isValid;


        return isValid;
    }

    if (isValid) {
        document.getElementById("order-form").submit();
    }

    const prices = {
        'Mombasa (bus)': 900,
        'Mombasa (train economy)': 13500,
        'Kwale': 1065,
        'Kilifi': 1065,
        'Tana River': 1065,
        'Lamu': 1410,
        'Taita-Taveta': 705,
        'Garissa': 1065,
        'Wajir': 1410,
        'Mandera': 1770,
        'Marsabit': 1065,
        'Isiolo': 540,
        'Meru': 540,
        'Tharaka-Nithi': 540,
        'Embu': 225,
        'Kitui': 540,
        'Machakos': 105,
        'Makueni': 360,
        'Nyandarua': 270,
        'Nyeri': 300,
        'Kirinyaga': 180,
        'Murang\'a': 165,
        'Kiambu': 60,
        'Turkana': 1410,
        'West Pokot': 705,
        'Samburu': 705,
        'Trans-Nzoia': 705,
        'Uasin Gishu': 630,
        'Elgeyo-Marakwet': 630,
        'Nandi': 630,
        'Baringo': 450,
        'Laikipia': 360,
        'Nakuru': 285,
        'Narok': 450,
        'Kajiado': 180,
        'Kericho': 540,
        'Bomet': 540,
        'Kakamega': 705,
        'Vihiga': 705,
        'Bungoma': 705,
        'Busia': 705,
        'Siaya': 630,
        'Kisumu': 630,
        'Homa Bay': 705,
        'Migori': 900,
        'Kisii': 540,
        'Nyamira': 540,
        'Nairobi': 100
    };

    function showCountyDropdown() {
        const type = document.getElementById('type').value;
        if (type === 'company') {
            document.getElementById('companySection').style.display = 'block';
        } else {
            document.getElementById('companySection').style.display = 'none';
        }
    }

    function showPrice() {
        const county = document.getElementById('county').value;
        const price = prices[county] || 'N/A';
        document.getElementById('deliveryprice').value = price;
        document.getElementById('price').innerText = price + ' KSh';
        updateTotalPrice();

    }

    function backToProducts() {
        window.location.href = "/Home/products.php";
    }

    function updateTotalPrice() {
        const deliveryPrice = parseInt(document.getElementById('deliveryprice').value) || 0;
        const basePrice = <?php echo $total; ?>;
        const totalPrice = basePrice + deliveryPrice;
        document.getElementById('totalPrice').innerText = totalPrice;
    }
    updateTotalPrice();
    </script>


</body>

</html>