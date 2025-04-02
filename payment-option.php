<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home-page-navBar.css">
    <link rel="stylesheet" href="css/page/payment-op.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand color-dark" href="#">BCP E-COMMERCE</a>
            <button class="navbar-toggler border-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <div class="notif-burger"></div>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="home-page.php">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product-page.php">PRODUCTS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about-us.php" tabindex="-1">ABOUT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php" tabindex="-1">CONTACTS</a>
                    </li>
                    <li class="nav-item dropdown">
                        <div class="notif"></div>
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        MY ACCOUNT
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="user-profile.php">Profile <span class="notif-profile"></span></a></li>
                        <li><a class="dropdown-item" href="cart-page.php">Cart <span class="cart-count"></span></a></li>
                        <!-- <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- <div class="payment-container">
        <div class="div-container">
            
        </div>
    </div>  -->
    <div class="container payment-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="payment-options">
                    <!-- <div class="payment-option mb-3">
                        <input type="radio" id="kasunduan" name="payment_method" value="Kasunduan">
                        <label for="kasunduan">
                            <h2><img class="icon-sign" src="image/icon/sign.png" alt=""> Agreement</h2>
                            <p>Use <a href="#">Agreement</a> if you want to buy the product you've chosen because it's necessary but don't currently have a budget. You can pay in the cashier by the given deadline.</p>
                        </label>
                    </div> -->

                    <div class="payment-option mb-3">
                        <input type="radio" id="walkin" name="payment_method" value="Walk-In Payment">
                        <label for="walkin">
                            <h2><img class="icon-sign" src="image/icon/distance.png" alt=""> Cashier Payment</h2>
                            <p>Use <a href="#">Cashier Payment</a> if you're unsure about the item you chose, as you can still exchange it if your order is incorrect.</p>
                        </label>
                    </div>

                    <div class="payment-option mb-3">
                        <input type="radio" id="gcash" name="payment_method" value="Gcash Payment">
                        <label for="gcash">
                            <h2><img class="gcash-logo" src="image/icon/gcash-logo.png" alt=""> GCash</h2>
                            <p>Use <a href="#">GCash</a> to make an online payment if the item you select is certain to be the one you purchase.</p>
                        </label>
                    </div>
                </div>

                <div class="payment-summary text-center mt-4">
                    <h2>Payment Method</h2>
                    <p>Choose your payment method before checking out for your purchase.</p>
                    <button id="confirmCheckout" class="btn btn-primary">Checkout Now</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-footer">
        <footer class="the-footer">
            <div class="left-section">
                <div class="left-div">
                    <div class="first-add">
                        <h6>BCP Main Campus</h6>
                        <p>#1071 Brgy. Kaligayahan, Quirino Highway Novaliches, Quezon City, Philippines</p>
                    </div>
    
                    <div class="second-add">
                        <h6>BCP Bulacan Campus</h6>
                        <p>Quirino Highway, San Jose del Monte Bulacan, Philippines</p>
                    </div>
                </div>
            </div>
            <div class="middle-section">
                <div class="first-links">
                    <h4>Links</h4>
                    <p>Home</p>
                    <p>Products</p>
                    <p>Contucts</p>
                    <p>Cart</p>
                    <p>MyAccount</p>
                </div>

                <div class="second-hepls">
                    <h4>Help</h4>
                    <p>Privacy Policy</p>
                    <p>Terms & Condition</p>
                </div>
            </div>
            <div class="right-section">
                <div class="newsletter">
                    <h4>NewsLetter</h4>
                    <div class="input-box">
                        <input class="input" type="text" placeholder="Enter your Email Address">
                        <button class="button-subscribe">Subscribe</button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <script src="script/payment.js" type="module"></script>
    <script src="script/cart-counter.js" type="module"></script>
    <!-- <script src="script/selected-payment.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>




 