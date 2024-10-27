<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    session_start();
    $conducteur = $_SESSION['username'];
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['depart'], $_POST['arrivee'], $_POST['date'], $_POST['nb_passagers'], 
    $_POST['heure'], $_POST['commentaire'], $_POST['prix']) ) {
        $depart = $_POST['depart'];
        $arrivee = $_POST['arrivee'];
        $date = $_POST['date'];
        $nb_passagers =  $_POST['nb_passagers'];
        $heure = $_POST['heure'];
        $commentaire = $_POST['commentaire'];
        $prix = $_POST['prix'];
        $date = new DateTime($date . ' ' . $heure);
        $currentDate = new DateTime();
        if ($date <= $currentDate) {
            header('Location: postertrajet.php?erreur=10');
            exit();
        }
        $date = $date->format('Y-m-d H:i:s');
        // insertion des données du trajet
        $req = $bdd->prepare("INSERT INTO trajet(Depart, Arrivee, Date, Placesrestantes, Conducteur, Commentaire, prix) 
        VALUES(:depart, :arrivee, :date, :nb_passagers, :conducteur, :commentaire, :prix)");
        $req->bindParam(':depart', $depart);
        $req->bindParam(':arrivee', $arrivee);
        $req->bindParam(':date', $date);
        $req->bindParam(':nb_passagers', $nb_passagers);
        $req->bindParam(':conducteur', $conducteur);
        $req->bindParam(':commentaire', $commentaire);
        $req->bindParam(':prix', $prix);
        $req->execute();
        
        // insertion du id du trajet et du id du conducteur dans la table appartient
        $trajetId = $bdd->lastInsertId();
        $req2 = $bdd->prepare("INSERT INTO appartient(Idtrajet, Username) VALUES(:trajetId, :conducteur)");
        $req2->bindParam(':trajetId', $trajetId);
        $req2->bindParam(':conducteur', $conducteur);
        $req2->execute();
        header('Location: mestrajets.php');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

?>