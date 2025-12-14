<?php

require_once __DIR__ . '/../model/Event.php';
require_once __DIR__ . '/../model/Category.php';
require_once __DIR__ . '/../model/Reservation.php';

class EventController
{
    private $eventModel;

    public function __construct()
    {
        $this->eventModel = new Event();
    }

    public function listEvents()
    {
        header('Content-Type: application/json');
        try {
            $events = $this->eventModel->getAllWithCategory();
            echo json_encode(['status' => 'success', 'data' => $events]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getEvent($id)
    {
        header('Content-Type: application/json');
        try {
            $event = $this->eventModel->getByIdWithCategory($id);
            if ($event) {
                echo json_encode(['status' => 'success', 'data' => $event]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createEvent()
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['type_event']) || empty($data['date_event'])) {
                echo json_encode(['status' => 'error', 'message' => 'Type and date are required']);
                return;
            }

            $result = $this->eventModel->create($data);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Event created successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create event']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateEvent($id)
    {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->eventModel->update($id, $data);
            
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Event updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update event']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteEvent($id)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->eventModel->delete($id);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete event']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getStats()
    {
        header('Content-Type: application/json');
        try {
            $stats = $this->eventModel->getStats();
            echo json_encode(['status' => 'success', 'data' => $stats]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function searchEvents()
    {
        header('Content-Type: application/json');
        try {
            $filters = json_decode(file_get_contents('php://input'), true);
            $events = $this->eventModel->search($filters);
            echo json_encode(['status' => 'success', 'data' => $events]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
