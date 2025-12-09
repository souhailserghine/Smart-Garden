<?php
class Reservation
{
    private $id_reservation;
    private $date_reservation;

    public function __construct($id_reservation, $date_reservation)
    {
        $this->id_reservation = $id_reservation;
        $this->date_reservation = $date_reservation;
    }

    public function getIdReservation()
    {
        return $this->id_reservation;
    }

    public function getDateReservation()
    {
        return $this->date_reservation;
    }

    public function setIdReservation($id_reservation)
    {
        $this->id_reservation = $id_reservation;
    }

    public function setDateReservation($date_reservation)
    {
        $this->date_reservation = $date_reservation;
    }
}
?>
