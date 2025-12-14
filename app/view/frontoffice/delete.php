<?php
include '../../controller/publicationC.php';

if (isset($_GET["id"])) {
    $publicationC = new PublicationC();
    $result = $publicationC->deletePublication($_GET["id"]);
    
    if ($result) {
        header('Location: liste.php?success=2');
    } else {
        header('Location: liste.php?error=0');
    }
} else {
    header('Location: liste.php?error=1');
}
exit;
?>