<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home-page-navBar.css">
    <link rel="stylesheet" href="css/page/contact-page.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <title>Contact Us</title>
</head>
<body>
    <div class="home-page2">
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
                            <a class="nav-link" href="about-us.php" tabindex="-1">ABOUT US</a>
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
                            <li><a class="dropdown-item" href="user-profile.php">PROFILE <span class="notif-profile"></span></a></li>
                            <li><a class="dropdown-item" href="cart-page.php">CART <span class="cart-count"></span></a></li>
                            <!-- <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="info-home-page">
            <div class="bcp-text">
                <div>
                    <h1 class="first-text">MAGANDANG BUHAY!</h1>
                    <p class="second-text">Let's Talk</p>
                </div>
            </div>
        </section>
    </div>
    
    <section  class="contact-details">
        <div class="box-address-details">
            <div class="box-address">
                <div class="div-address">
                    <div class="image-location">
                        <img class="location-img" src="image/IMG_20250109_141850.jpg" alt="">
                    </div>

                    <div class="div-text-address">
                        <div class="address-text">
                            <h4>Address</h4>
                            <p>#1071 Brgy. Kaligayahan, Quirino Highway Novaliches, Quezon City, Philippines</p>
                        </div>
        
                        <div class="call-text">
                            <h4>Call us</h4>
                            <div class="number-div">
                                <span>09123456789</span>
                                <br>
                                <span>09123456789</span>
                            </div>
                            
                        </div>
        
                        <div class="email-text">
                            <h4>Email</h4>
                            <p>example@gmail.com</p>
                        </div>
        
                        <div class="follow-us-text">
                            <h4>Follow us</h4>
                            <img src="image/icon/facebook.png" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-message">
                <div class="div-message">
                    <div class="send-message-text">
                        <h2>Send a message</h2>
                    </div>

                    <div class="inputs-box">
                        <div class="input-divs">
                            <div>
                                <label for="">Name</label>
                            </div>
                            <input type="text" class="name-input">
                        </div>
                        <div class="input-divs">
                            <div>
                                <label for="">Email</label>
                            </div>
                            <input type="text" class="email-input">
                        </div>
                        <div class="input-divs">
                            <div>
                                <label for="">Subject</label>
                            </div>
                            <input type="text" class="subject-input">
                        </div>
                        <div class="input-divs">
                            <div>
                                <label for="">Message</label>
                            </div>
                            <input type="text" class="message-input">
                        </div>
                    </div>

                    <div class="btn-send-div">
                        <button class="send-btn">SEND</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
    
    <script src="script/cart-counter.js" type="module"></script>
    <script src="script/contact-form.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>