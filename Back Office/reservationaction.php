<?php
include __DIR__ . '/../../Backend/app/controllers/reservationC.php';
include __DIR__ . '/../../Backend/app/models/reservation.php';

$resC = new reservationC();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'ajouter') {
        if (!empty($_POST['date_reservation'])) {
            $reservation = new Reservation();
            $reservation->setDateReservation($_POST['date_reservation']);
            $resC->ajouterReservation($reservation);

            header('Location: reservations.php?toast=' . urlencode('Réservation ajoutée avec succès !') . '&ok=1');
            exit;
        } else {
            header('Location: reservations.php?toast=' . urlencode('Veuillez sélectionner une date.') . '&ok=0');
            exit;
        }
    }

}
?>
