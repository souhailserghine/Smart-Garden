<?php
session_start();
require_once '../../mailer.php';
require_once '../../password_helpers.php';
require_once '../../controller/utilisateurController.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    $controller = new UtilisateurC();
    $user = $controller->trouverParEmail($email);
    
    if ($user) {
        $lastRequest = $user['reset_requested_at'];
        if ($lastRequest && (time() - strtotime($lastRequest)) < 60) {
            $message = "Si un compte existe avec cet email, vous recevrez un lien de réinitialisation.";
            $messageType = 'info';
        } else {
            $token = genererToken(32);
            $tokenHash = hasherToken($token);
            
            if ($controller->sauvegarderTokenReset($email, $tokenHash, 3600)) {
                $resetUrl = 'http://localhost/website/app/view/frontoffice/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email);
                
                $sujet = 'Réinitialisation de votre mot de passe';
                $html = "
                    <p>Bonjour " . htmlspecialchars($user['nom']) . ",</p>
                    <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
                    <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe. Ce lien expirera dans 1 heure.</p>
                    <p><a href=\"{$resetUrl}\" style=\"background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;\">Réinitialiser mon mot de passe</a></p>
                    <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.</p>
                    <p>Cordialement,<br>L'équipe SMARTGARDEN</p>
                ";
                
                $envoi = envoyerEmail($email, $user['nom'], $sujet, $html);
                
                if (!$envoi['succes']) {
                    error_log("Échec d'envoi de l'email de réinitialisation pour {$email}: " . ($envoi['erreur'] ?? 'inconnu'));
                }
                
                $message = "Si un compte existe avec cet email, vous recevrez un lien de réinitialisation.";
                $messageType = 'success';
            }
        }
    } else {
        $message = "Si un compte existe avec cet email, vous recevrez un lien de réinitialisation.";
        $messageType = 'info';
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
    <title>Forgot Password - SmartGarden</title>

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

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : ($messageType === 'danger' ? 'danger' : 'info'); ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="forgotPasswordForm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" placeholder="Email Address">
                            </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary sign-up">Send Reset Link</button>
                            </div>
                        </div>
                        <div class="col-md-12 text-center mt-5">
                            <span class="go-login"><a href="sign-in.php"><i class='bx bx-arrow-back'></i> Back to Sign In</a></span>
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
                    <h2 class="create-account mb-3">Forgot Password?</h2>
                    <p>Enter your email to reset your password</p>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/popper/popper.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="./assets/js/forgot-password-validation.js"></script>
</body>

</html>
