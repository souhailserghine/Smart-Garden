<?php

require_once __DIR__ . '/../model/Reservation.php';

class ReservationController
{
    private $reservationModel;

    public function __construct()
    {
        $this->reservationModel = new Reservation();
    }

    public function getAllReservations()
    {
        header('Content-Type: application/json');
        try {
            error_log("ReservationController: getAllReservations called");
            $reservations = $this->reservationModel->getAllWithDetails();
            error_log("ReservationController: Found " . count($reservations) . " reservations");
            echo json_encode(['status' => 'success', 'data' => $reservations]);
        } catch (Exception $e) {
            error_log("ReservationController Error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    public function getReservation($id)
    {
        header('Content-Type: application/json');
        try {
            $reservation = $this->reservationModel->getById($id);
            if ($reservation) {
                echo json_encode(['status' => 'success', 'data' => $reservation]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Reservation not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createReservation()
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id_event']) || empty($data['date_reservation'])) {
                echo json_encode(['status' => 'error', 'message' => 'Event and date are required']);
                return;
            }

            $result = $this->reservationModel->create($data);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Reservation created successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create reservation']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateReservation($id)
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->reservationModel->update($id, $data);
            
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Reservation updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update reservation']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteReservation($id)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->reservationModel->delete($id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Reservation deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete reservation']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getByEvent($eventId)
    {
        header('Content-Type: application/json');
        try {
            $reservations = $this->reservationModel->getByEvent($eventId);
            echo json_encode(['status' => 'success', 'data' => $reservations]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getByUser($userId)
    {
        header('Content-Type: application/json');
        try {
            $reservations = $this->reservationModel->getByUser($userId);
            echo json_encode(['status' => 'success', 'data' => $reservations]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
