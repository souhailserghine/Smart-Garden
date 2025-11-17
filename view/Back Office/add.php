<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter une Nouvelle Plante</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
h1 { color: #28a745; }
form { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
.form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
label { font-weight: bold; margin-bottom: 5px; }
input[type="text"], input[type="number"], input[type="date"], select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; }
input[type="submit"] { padding: 12px 25px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
input[type="submit"]:hover { background-color: #1e7e34; }
span.error { color: red; font-size: 14px; margin-top: 2px; }
</style>
</head>
<body>

<h1>Ajouter une Nouvelle Plante 🪴</h1>

<form id="planteForm" action="ajout.php" method="POST" onsubmit="return validateForm()">

    <div class="form-group">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom">
        <span id="err-nom" class="error"></span>
    </div>
    
    <div class="form-group">
        <label for="date">Date d'Ajout :</label>
        <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
        <span id="err-date" class="error"></span>
    </div>
    
    <div class="form-group">
        <label for="niveau_humidite">Humidité (%) :</label>
        <input type="number" id="niveau_humidite" name="niveau_humidite">
        <span id="err-humidite" class="error"></span>
    </div>
    
    <div class="form-group">
        <label for="besoin_eau">Besoin Eau (ml) :</label>
        <input type="number" id="besoin_eau" name="besoin_eau">
        <span id="err-eau" class="error"></span>
    </div>
    
    <div class="form-group">
        <label for="etat_sante">État Santé :</label>
        <select id="etat_sante" name="etat_sante">
            <option value="0">--- Choisir ---</option>
            <option value="Bon état">Bon état</option>
            <option value="Moyen">Moyen</option>
            <option value="Mauvais état">Mauvais état</option>
        </select>
        <span id="err-etat" class="error"></span>
    </div>
    
    <div class="form-group">
        <label for="idUtilisateur">ID Utilisateur :</label>
        <input type="number" id="idUtilisateur" name="idUtilisateur" value="1">
        <span id="err-user" class="error"></span>
    </div>

    <input type="submit" value="Ajouter la Plante">
</form>

<script>
function validateForm() {
    let valid = true;

    // récupération des valeurs
    const nom = document.getElementById('nom').value.trim();
    const date = document.getElementById('date').value;
    const humidite = document.getElementById('niveau_humidite').value.trim();
    const besoin = document.getElementById('besoin_eau').value.trim();
    const etat = document.getElementById('etat_sante').value;
    const user = document.getElementById('idUtilisateur').value.trim();

    // vider tous les messages d'erreur
    document.getElementById('err-nom').textContent = "";
    document.getElementById('err-date').textContent = "";
    document.getElementById('err-humidite').textContent = "";
    document.getElementById('err-eau').textContent = "";
    document.getElementById('err-etat').textContent = "";
    document.getElementById('err-user').textContent = "";

    // vérifications simples
    if(nom === "") { document.getElementById('err-nom').textContent = "Nom obligatoire"; valid=false; }
    if(date === "") { document.getElementById('err-date').textContent = "Date obligatoire"; valid=false; }
    if(humidite === "" || humidite < 0 || humidite > 100) { document.getElementById('err-humidite').textContent = "Humidité 0-100"; valid=false; }
    if(besoin === "" || besoin < 0) { document.getElementById('err-eau').textContent = "Besoin d'eau >0"; valid=false; }
    if(etat === "0") { document.getElementById('err-etat').textContent = "Choisir un état"; valid=false; }
    if(user === "" || user <= 0) { document.getElementById('err-user').textContent = "ID Utilisateur valide"; valid=false; }

    return valid;
}
</script>

</body>
</html>
