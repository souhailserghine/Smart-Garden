<?php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function getMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sou.sergh@gmail.com';
    $mail->Password = 'eeqi efff zfka ozsc';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('sou.sergh@gmail.com', 'SMARTGARDEN');
    return $mail;
}

function envoyerEmail($destinataire, $nom, $sujet, $contenuHtml, $contenuTexte = '') {
    try {
        $mail = getMailer();
        $mail->addAddress($destinataire, $nom);
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $contenuHtml;
        $mail->AltBody = $contenuTexte ?: strip_tags($contenuHtml);
        $mail->send();
        return ['succes' => true];
    } catch (Exception $e) {
        return ['succes' => false, 'erreur' => $mail->ErrorInfo];
    }
}
?>