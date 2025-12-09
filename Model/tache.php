<?php
class tache
{
    private $id_dosage;
    private $type_dosage;
    private $quantite;
    private $mode_dosage;
    private $date_dosage;
    private $derniereExecution;
    private $prochaineExecution;
    private $estComplete;
    private $priorite;
    private $id_plante;

    public function __construct(
        $id_dosage,
        $type_dosage,
        $quantite,
        $mode_dosage,
        $date_dosage,
        $derniereExecution,
        $prochaineExecution,
        $estComplete,
        $priorite,
        $id_plante
    ) {
        $this->id_dosage = $id_dosage;
        $this->type_dosage = $type_dosage;
        $this->quantite = $quantite;
        $this->mode_dosage = $mode_dosage;
        $this->date_dosage = $date_dosage;
        $this->derniereExecution = $derniereExecution;
        $this->prochaineExecution = $prochaineExecution;
        $this->estComplete = $estComplete;
        $this->priorite = $priorite;
        $this->id_plante = $id_plante;
    }

    public function getIdDosage() { return $this->id_dosage; }
    public function getTypeDosage() { return $this->type_dosage; }
    public function getQuantite() { return $this->quantite; }
    public function getModeDosage() { return $this->mode_dosage; }
    public function getDateDosage() { return $this->date_dosage; }
    public function getDerniereExecution() { return $this->derniereExecution; }
    public function getProchaineExecution() { return $this->prochaineExecution; }
    public function getEstComplete() { return $this->estComplete; }
    public function getPriorite() { return $this->priorite; }
    public function getIdPlante() { return $this->id_plante; }

    public function setIdDosage($id_dosage) { $this->id_dosage = $id_dosage; }
    public function setTypeDosage($type_dosage) { $this->type_dosage = $type_dosage; }
    public function setQuantite($quantite) { $this->quantite = $quantite; }
    public function setModeDosage($mode_dosage) { $this->mode_dosage = $mode_dosage; }
    public function setDateDosage($date_dosage) { $this->date_dosage = $date_dosage; }
    public function setDerniereExecution($derniereExecution) { $this->derniereExecution = $derniereExecution; }
    public function setProchaineExecution($prochaineExecution) { $this->prochaineExecution = $prochaineExecution; }
    public function setEstComplete($estComplete) { $this->estComplete = $estComplete; }
    public function setPriorite($priorite) { $this->priorite = $priorite; }
    public function setIdPlante($id_plante) { $this->id_plante = $id_plante; }
}
?>
