 <?php
session_start();
require_once '../../google_config.php';
require_once '../../controller/utilisateurController.php';
require_once '../../controller/historiqueController.php';

$client = getGoogleClient();

if (!isset($_GET['code'])) {
    header('Location: sign-in.php');
    exit();
}

try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (isset($token['error'])) {
        throw new Exception('Error fetching access token: ' . $token['error']);
    }
    
    $client->setAccessToken($token['access_token']);
    
    $google_oauth = new Google\Service\Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    
    $google_id = $google_account_info->id;
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $picture = $google_account_info->picture;
    
    $controller = new UtilisateurC();
    $hcontroller = new HistoriqueC();
    $user = $controller->trouverParEmail($email);
    
    if ($user) {
        $dateConnexion = date('Y-m-d H:i:s');
        $log = $hcontroller->ajouterHistorique($dateConnexion, '0000-00-00 00:00:00', 'login', 0, $user['idUtilisateur']);
        
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['idUtilisateur'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_localisation'] = $user['localisation'];
        $_SESSION['user_role'] = $user['role'];
        
        if ($user['statut'] == 'bloque') {
            session_destroy();
            header('Location: sign-in.php?error=blocked');
            exit();
        }
        
        if ($user['role'] == 'admin') {
            header('Location: ../backoffice/index.php');
        } else {
            header('Location: index.php');
        }
    } else {
        $randomPassword = bin2hex(random_bytes(16));
        $result = $controller->ajouterUtilisateur($name, $email, $randomPassword, '', 'actif');
        
        if ($result) {
            $newUser = $controller->trouverParEmail($email);
            
            $dateConnexion = date('Y-m-d H:i:s');
            $log = $hcontroller->ajouterHistorique($dateConnexion, '0000-00-00 00:00:00', 'login', 0, $newUser['idUtilisateur']);
            
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $newUser['idUtilisateur'];
            $_SESSION['user_name'] = $newUser['nom'];
            $_SESSION['user_email'] = $newUser['email'];
            $_SESSION['user_localisation'] = $newUser['localisation'];
            $_SESSION['user_role'] = $newUser['role'];
            
            header('Location: index.php');
        } else {
            header('Location: sign-in.php?error=registration');
        }
    }
    
} catch (Exception $e) {
    error_log('Google OAuth Error: ' . $e->getMessage());
    header('Location: sign-in.php?error=oauth');
    exit();
}
?>
