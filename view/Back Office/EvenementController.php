<?php
require_once __DIR__ . '/Evenement.php';

class EvenementController {

    private function connectDB() {
        try {
            $db = new PDO('mysql:host=localhost;dbname=project;charset=utf8', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (Exception $e) {
            // Log detail server-side, but return a generic message to the client
            error_log('EvenementController::connectDB error: ' . $e->getMessage());
            echo json_encode(["status"=>"error","message"=>"Server error"]);
            exit;
        }
    }

    public function add() {
        $input = json_decode(file_get_contents("php://input"), true);
        if(!$input){ echo json_encode(["status"=>"error","message"=>"No input data"]); return; }

        $type = $input['type_event'] ?? '';
        $date = $input['date_event'] ?? '';
        $etat = strtolower($input['etat'] ?? '');

        if(!$type || !$date || !in_array($etat,['active','inactive'])){
            echo json_encode(["status"=>"error","message"=>"Données invalides"]); return;
        }

        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("INSERT INTO evenement (type_event, date_event, etat) VALUES (:type, :date, :etat)");
            $stmt->execute(['type'=>$type,'date'=>$date,'etat'=>$etat]);
            echo json_encode(["status"=>"success","message"=>"Événement ajouté"]);
        } catch(Exception $e){
            error_log('EvenementController::add error: ' . $e->getMessage());
            echo json_encode(["status"=>"error","message"=>"Server error"]);
        }
    }

    public function getAll() {
        try {
            $db = $this->connectDB();
            $stmt = $db->query("SELECT * FROM evenement ORDER BY date_event ASC");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status"=>"success","data"=>$events]);
        } catch(Exception $e){
            error_log('EvenementController::getAll error: ' . $e->getMessage());
            echo json_encode(["status"=>"error","message"=>"Server error"]);
        }
    }

    // Debug info: retourne la BDD utilisée et l'heure du fichier controller
    public function info() {
        try {
            $db = $this->connectDB();
            // retourne uniquement le nom de la base via une requête simple
            $stmt = $db->query("SELECT DATABASE() as dbname");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbname = $row['dbname'] ?? null;
        } catch (Exception $e) {
            $dbname = null;
        }
        $mtime = filemtime(__FILE__);
        echo json_encode(["status"=>"success","dbname"=>$dbname,"controller_mtime"=>date('c',$mtime)]);
    }

    public function getOne($id){
        if(!is_numeric($id)){ echo json_encode(["status"=>"error","message"=>"ID invalide"]); return; }
        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("SELECT * FROM evenement WHERE id_event=:id");
            $stmt->execute(['id'=>$id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            if($event) echo json_encode(["status"=>"success","data"=>$event]);
            else echo json_encode(["status"=>"error","message"=>"Événement non trouvé"]);
        } catch(Exception $e){
            error_log('EvenementController::getOne error: ' . $e->getMessage());
            echo json_encode(["status"=>"error","message"=>"Server error"]);
        }
    }

    public function update($id){
        if(!is_numeric($id)){ echo json_encode(["status"=>"error","message"=>"ID invalide"]); return; }
        $input = json_decode(file_get_contents("php://input"), true);
        if(!$input){ echo json_encode(["status"=>"error","message"=>"No input data"]); return; }

        $type = $input['type_event'] ?? '';
        $date = $input['date_event'] ?? '';
        $etat = strtolower($input['etat'] ?? '');

        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("UPDATE evenement SET type_event=:type, date_event=:date, etat=:etat WHERE id_event=:id");
            $stmt->execute(['type'=>$type,'date'=>$date,'etat'=>$etat,'id'=>$id]);
            echo json_encode(["status"=>"success","message"=>"Événement modifié"]);
        } catch(Exception $e){
            error_log('EvenementController::update error: ' . $e->getMessage());
            echo json_encode(["status"=>"error","message"=>"Server error"]);
        }
    }

    public function delete($id){
        if(!is_numeric($id)){ echo json_encode(["status"=>"error","message"=>"ID invalide"]); return; }
        try {
            $db = $this->connectDB();
            $stmt = $db->prepare("DELETE FROM evenement WHERE id_event=:id");
            $stmt->execute(['id'=>$id]);
            echo json_encode(["status"=>"success","message"=>"Événement supprimé"]);
        } catch(Exception $e){
            error_log('EvenementController::delete error: ' . $e->getMessage());
            echo json_encode(["status"=>"error","message"=>"Server error"]);
        }
    }
}
?>
