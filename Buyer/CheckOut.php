<?php
require_once '../Shared Components/dbconnection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
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
    $discount = 0;
    $total = 0;


    // Calculate subtotal
    foreach ($cartItems as $item) {
        // Calculate total price for each item
        $totalPrice = $item['quantity'] * $item['price'];
        // Add total price to subtotal
        $subtotal += $totalPrice;
        $discount = $item['discount'];
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
                                    <?php echo $item['product_type']; ?>
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
                                        data-pk="<?php echo $item['cart_id']; ?>" data-pk-name="cart_id">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>


                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class=" cart-total">
                    <div class="return-container">
                        <button type="button" id="return-button" class="return-button"><i
                                class="fa-solid fa-angle-left"></i>Continue
                            Shopping</button>
                    </div>
                    <div class="totals">
                        <p>SubTotal:
                            <?php echo $subtotal; ?>
                        </p>
                        <p> Discount:
                            <?php echo $discount; ?>
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
                            <label for="mpesaNumber">Mpesa Number</label>
                            <input type="text" class="inputfield" id="mpesaNumber" name="mpesaNumber">
                            <div class="error"></div>
                        </div>

                        <div class="inputcontrol">
                            <label for="mpesaName">Mpesa Name</label>
                            <input type="text" class="inputfield" id="mpesaName" name="mpesaName">
                            <div class="error"></div>
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
                            <label for="airtelNumber">Airtel Money Number</label>
                            <input type="text" class="inputfield" id="airtelNumber" name="airtelNumber">
                            <div class="error"></div>
                        </div>

                        <div class="inputcontrol">
                            <label for="airtelName">Airtel Money Name</label>
                            <input type="text" class="inputfield" id="airtelName" name="airtelName">
                            <div class="error"></div>
                        </div>

                        <!-- <div class="inputcontrol">
                        <label for="tillNumberAirtel">Till Number</label>
                        <input type="text" class="inputfield" id="tillNumberAirtel" name="tillNumberAirtel">
                        <div class="error"></div>
                    </div> -->
                    </div>

                    <div id="cardFields" class="input-box" style="display:none;">
                        <div class="inputcontrol">
                            <label for="cardName">Name on Card</label>
                            <input type="text" class="inputfield" id="cardName" name="cardName">
                            <div class="error"></div>
                        </div>

                        <div class="inputcontrol">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" class="inputfield" id="cardNumber" name="cardNumber">
                            <div class="error"></div>
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
                            <label for="Instructions" class="no-asterisk">Delivery Instructions</label>
                            <textarea class="inputfield" style="height: 70px;" class="inputfield"
                                name="Instructions"></textarea>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="return-container">
                        <button id="order-button" class="order-button">Place Order</button>
                    </div>



                </div>


            </div>
        </div>
    </form>

    <script>
        // Define a function to submit the form
        function submitOrderForm() {
            console.log("Starting to submit the form");
            document.getElementById('order-form').submit();
        }

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

        document.addEventListener("DOMContentLoaded", function () {
            fetch('header.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('header-container').innerHTML = data;
                });



            // Attach event listener to submit button
            var submitButton = document.getElementById('order-button');
            submitButton.addEventListener('click', submitOrderForm);

            function updateSubtotalAndTotal() {
                var total = 0;
                var totalElements = document.querySelectorAll('.reg-cell[data-type="total-price"]');
                totalElements.forEach(function (element) {
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
            xhr.onload = function () {
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
            xhr.onerror = function () {
                // Handle network errors
                console.error('Request failed');
            };
            // Send the cart ID and new quantity to the server
            xhr.send('cart_id=' + cartId + '&quantity=' + quantity);
        }


        document.addEventListener("DOMContentLoaded", function () {
            // Get all elements with the class "delete-link"
            var deleteLinks = document.querySelectorAll('.delete-link');

            // Loop through each delete link
            deleteLinks.forEach(function (link) {
                // Add click event listener to each delete link
                link.addEventListener('click', function (event) {
                    // Prevent the default behavior (i.e., following the href)
                    event.preventDefault();

                    // Get the table name, primary key column name, and primary key value from the data attributes
                    var tableName = link.getAttribute('data-table');
                    var primaryKey = link.getAttribute('data-pk');
                    var pkName = link.getAttribute('data-pk-name');

                    // Perform AJAX request to the delete script
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', '/Shared Components/delete.php?table=' + tableName + '&pk=' +
                        primaryKey +
                        '&pk_name=' + pkName, true);
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            // Handle successful deletion (if needed)
                            // For example, you can remove the deleted row from the DOM
                            link.parentElement.parentElement.remove();
                        } else {
                            // Handle error (if needed)
                            console.error('Error:', xhr.statusText);
                        }
                    };
                    xhr.onerror = function () {
                        // Handle network errors (if needed)
                        console.error('Request failed');
                    };
                    xhr.send();
                });
            });
        });
    </script>


</body>

</html>