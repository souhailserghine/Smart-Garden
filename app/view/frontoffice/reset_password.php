<?php
session_start();
require_once '../../password_helpers.php';
require_once '../../controller/utilisateurController.php';

$controller = new UtilisateurC();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';
    $token = $_GET['token'] ?? '';

    if (!$email || !$token) {
        $error = 'Lien invalide';
    } else {
        $tokenHash = hasherToken($token);
        $user = $controller->verifierTokenReset($email, $tokenHash);
        
        if (!$user) {
            $error = 'Lien invalide ou expiré';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!$email || !$token) {
        $error = 'Requête invalide';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Les mots de passe ne correspondent pas';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères';
    } else {
        $tokenHash = hasherToken($token);
        $user = $controller->verifierTokenReset($email, $tokenHash);
        
        if (!$user) {
            $error = 'Lien invalide ou expiré';
        } else {
            if ($controller->mettreAJourMotDePasse($user['idUtilisateur'], $password)) {
                $success = 'Votre mot de passe a été réinitialisé avec succès.';
            } else {
                $error = 'Une erreur est survenue. Veuillez réessayer.';
            }
        }
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
    <title>Reset Password - SmartGarden</title>

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

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="sign-in.php" class="btn btn-primary sign-up">Go to Sign In</a>
                    </div>
                <?php elseif (!$error): ?>
                    <form method="POST" action="" id="resetPasswordForm">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="New Password (min 8 characters)">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password_confirm" placeholder="Confirm New Password">
                                </div>
                            </div>
                            <div class="col-md-12 text-right">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary sign-up">Reset Password</button>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-5">
                                <span class="go-login"><a href="sign-in.php"><i class='bx bx-arrow-back'></i> Back to Sign In</a></span>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center mt-3">
                        <a href="forgot_password.php" class="btn btn-outline-primary sign-up">Request New Link</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 auth-bg-image d-flex justify-content-center align-items-center">
            <div class="auth-left-content mt-5 mb-5 text-center">
                <div class="weather-small text-white">
                    <p class="current-weather"><i class='bx bx-sun'></i> <span>14&deg;</span></p>
                    <p class="weather-city">Gyumri</p>
                </div>
                <div class="text-white mt-5 mb-5">
                    <h2 class="create-account mb-3">Reset Password</h2>
                    <p>Enter your new password</p>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/reset-password-validation.js"></script>
</body>

</html>
