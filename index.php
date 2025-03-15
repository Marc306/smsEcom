<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="css/login-style.css"/>
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="forms">

            <div class="form login" id="signIn">
                <div class="image-logo-container">
                    <img class="image-icon" src="image/bcp-olp-logo-mini2 (1).png" alt="">
                </div>
                <div class="title-div">
                     <span class="title">SMS E-commerece Portal in Bestlink College of the PH.</span>
                </div>
               

                <form id="form-page">
                    <div class="input-field">
                        <input id="student_id" name="student_id" type="text" placeholder="Username" required>
                        <i class="uil uil-user icon"></i>
                    </div>
                    <div class="input-field">
                        <input id="password" name="password" type="password" class="password" placeholder="Password" required>
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>

                    <div class="error-div">
                        <!-- js Generated -->
                    </div>
                    

                    <div class="input-field button">
                        <button id="signInButton" type="submit">Login</button>
                    </div>

                    <div class="instraction alert info">
                        <p><strong>INSTRUCTIONS</strong></p><p></p><ol><li>BCP SMS, LMS and E-commerse portal are using same username and password.</li><li>Visit Ascendens Asia office at BCP MV Campus or Bulacan Campus if you are having hard times logging in to your account.</li></ol>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="data/loginFetch.js"></script>
    <script src="script/loginShowPass.js"></script>
</body>
</html>

