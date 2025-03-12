<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home-page-navBar.css">
    <link rel="stylesheet" href="css/profile-sideBar.css">
    <link rel="stylesheet" href="css/user-profile.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <title>My Profile</title>
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
                            <a class="nav-link" href="about-us.php" tabindex="-">ABOUT US</a>
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
                    <p class="second-text">#Profile Account</p>
                </div>
            </div>
        </section>
    </div>
      



    <div class="body-page">
        <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
            <svg class="hamburger-icon" viewBox="0 0 24 24" width="24" height="24">
                <path d="M3 12h18M3 6h18M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        <!-- Overlay -->
        <div class="sidebar-overlay"></div>

        <!-- Enhanced Sidebar -->
        <nav class="side-bar2" id="sidebar">
            <!-- Close button -->
            <button class="sidebar-close" id="sidebarClose" aria-label="Close Sidebar"></button>

            <div class="image-side-bar">
                <div class="user-profile-div">
                    <img id="preview2" class="side-bar-profile" alt="">
                    <div class="name-id">
                        <span class="first-span"></span>
                        <span class="second-span"></span>
                    </div>
                </div>         
            </div>
    
            <div class="sidebar-links">
                <a class="sidebar-link" href="user-profile.php">
                    <img class="icon-sideBar" src="image/profile-image.png" alt="">
                    <span class="a-tag">My Account</span>
                </a>
                
                <a class="sidebar-link" href="user-purchase.php">
                    <img class="icon-sideBar" src="image/items.png" alt="">
                    <span class="a-tag">My Purchase</span>
                    <span class="purchase-notif"></span>
                </a>
                
                <a class="sidebar-link" href="notifications.php">
                    <img class="icon-sideBar" src="image/icon/notification.png" alt=""> 
                    <span class="a-tag">Notification</span>
                    <span class="notif-number">1</span>
                </a>
                
                <a class="sidebar-link" href="php/logout.php?logout=true">
                    <img class="icon-sideBar" src="image/icon/logout.png" alt=""> 
                    <span class="a-tag">Logout</span>
                </a>
            </div>
        </nav>

        <!-- Profile Content -->
        <div class="profile-content">
            <main class="main-div">
                <div class="profile-account-container">
                    <div class="profile-text">My Account</div>
                    <div class="profile-section">
                        <div class="image-container">
                            <div class="file-div">
                                <img id="preview1" class="user-picture" alt="Profile Picture">
                                <label class="name-label">
                                    Name: 
                                    <span class="user-names"></span>
                                </label>
                                
                                <form id="uploadForm" action="uploadIMG.php" method="POST" enctype="multipart/form-data">
                                    <label class="custom-file-upload">
                                        Change Profile
                                        <input type="file" id="file-input" class="file-image-input" name="profile_image" accept=".jpg,.jpeg,.png" onchange="uploadImage()">
                                    </label>
                                </form>
                            </div>
                        </div>
                        <div class="form-container">
                            <div class="first-info-container">
                                <div class="profile-info">Personal Information</div>
                                <div>
                                    <div class="infos">
                                        <div>
                                            <label for="">First Name</label> 
                                            <p class="inputs"><img src="image/icon/user.png" alt=""><span class="first-name-info"></span></p>
                                        </div>
                                        <div>
                                            <label for="">Last Name</label> 
                                            <p class="inputs"><img src="image/icon/user.png" alt=""><span class="last-name-info"></span></p>
                                        </div>
                                    </div>
                                    <div class="infos">
                                        <div>
                                            <label for="">Contact Number</label> 
                                            <p class="inputs"><img src="image/telephone.png" alt=""><span class="number"></span></p>
                                        </div>
                                        <div> 
                                            <label for="">Birthday</label> 
                                            <p class="inputs"><img src="image/calendar.png" alt=""><span class="birth"></span></p>
                                        </div>   
                                    </div>
                                    <div class="infos">
                                        <div>
                                            <label for="">Email</label> 
                                            <p class="inputs"><img src="image/icon/email.png" alt=""><span class="email"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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

    <script>
        function uploadImage() {
            document.getElementById('uploadForm').submit();
        }
    </script>
   
    <script src="script/sideBar-info.js" type="module"></script>
    <script src="script/student-personal-info.js" type="module"></script>
    <script src="script/cart-counter.js" type="module"></script>
    <script src="script/sidebarActive2.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="script/purchase-notification.js"></script>
    <!-- <script src="script/sidebar.js"></script> -->
</body>
</html>