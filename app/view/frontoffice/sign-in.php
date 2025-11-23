<?php
require_once '../../google_config.php';
$client = getGoogleClient();
$login_url = $client->createAuthUrl();

$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'blocked':
            $error_message = 'Your account has been blocked. Please contact support.';
            break;
        case 'registration':
            $error_message = 'Failed to create account. Please try again.';
            break;
        case 'oauth':
            $error_message = 'Google authentication failed. Please try again.';
            break;
        case 'invalid':
            $error_message = 'Invalid email or password.';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="./assets/images/logo-16x16.png" />
    <title>SmartGarden - Social Network</title>

    <link href="https://fonts.googleapis.com/css?family=Major+Mono+Display" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>

    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
    <link href="./assets/css/components.css" rel="stylesheet">
    <link href="./assets/css/auth.css" rel="stylesheet">
    <link href="./assets/css/forms.css" rel="stylesheet">
    <link href="./assets/css/media.css" rel="stylesheet">
</head>

<body>
    <div class="row ht-100v flex-row-reverse no-gutters">
        <div class="col-md-6 d-flex justify-content-center align-items-center">
            <div class="signup-form">
                <div class="auth-logo text-center mb-5">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="./assets/images/logo-64x64.png" class="logo-img" alt="Logo">
                        </div>
                        <div class="col-md-10">
                            <p>Smart Garden</p>
                        </div>
                    </div>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="authentification.php" method="POST" novalidate>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" placeholder="Email Address">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="Password">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <a href="forgot_password.php">Forgot password?</a>
                        </div>
                        <div class="col-md-12 text-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary sign-up">Sign In</button>
                            </div>
                        </div>
                        
                        <div class="col-md-12 text-center mt-3">
                            <div class="text-muted mb-2">OR</div>
                            <a href="<?php echo htmlspecialchars($login_url); ?>" class="btn btn-outline-danger btn-block">
                                <i class='bx bxl-google'></i> Login with Google
                            </a>
                        </div>
                        
                        <div class="col-md-12 text-center mt-4">
                            <p class="text-muted">Start using your fingerprint</p>
                            <a href="#" class="btn btn-outline-primary btn-sm sign-up" data-toggle="modal" data-target="#fingerprintModal">Use Fingerprint</a>
                        </div>
                        
                        <div class="col-md-12 text-center mt-3">
                            <p class="text-muted">Or use facial recognition</p>
                            <a href="login_face.php" class="btn btn-outline-success btn-sm">
                                <i class='bx bx-face-mask'></i> Login with Face
                            </a>
                            <span class="mx-2">|</span>
                            <a href="register_face.php" class="btn btn-outline-info btn-sm">
                                <i class='bx bx-camera'></i> Register Face
                            </a>
                        </div>
                        
                        <div class="col-md-12 text-center mt-5">
                            <span class="go-login">Not yet a member? <a href="sign-up.php">Sign Up</a></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6 auth-bg-image d-flex justify-content-center align-items-center">
            <div class="auth-left-content mt-5 mb-5 text-center">
                <div class="weather-small text-white">
                    <p class="current-weather"><i class='bx bx-sun'></i> <span>14&deg;</span></p>
                    <p class="weather-city">Gyumri</p>
                </div>
                <div class="text-white mt-5 mb-5">
                    <h2 class="create-account mb-3">Welcome Back</h2>
                </div>
               
            </div>
        </div>
    </div>

    <div class="modal fade fingerprint-modal" id="fingerprintModal" tabindex="-1" role="dialog" aria-labelledby="fingerprintModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h3 class="text-muted display-5">Place your Finger on the Device Now</h3>
                    <img src="./assets/images/icons/auth-fingerprint.png" alt="Fingerprint">
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/sign-in-validation.js"></script>
</body>

</html>
