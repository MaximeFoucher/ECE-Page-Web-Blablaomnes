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
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['depart'], $_POST['arrivee'], $_POST['datealler'], $_POST['dateretour'], 
    $_POST['heurealler'], $_POST['nb_passagers'], $_POST['commentaire'], $_POST['prix'],$_POST['heureretour'])) {
        $departaller = $_POST['depart'];
        $arriveealler = $_POST['arrivee'];
        $departretour = $_POST['arrivee'];
        $arriveeretour = $_POST['depart'];
        $datealler = $_POST['datealler'];
        $dateretour = $_POST['dateretour'];
        $nb_passagers =  $_POST['nb_passagers'];
        $heurealler = $_POST['heurealler'];
        $heureretour = $_POST['heureretour'];
        $commentaire = $_POST['commentaire'];
        $prix = $_POST['prix'];
        $datealler = new DateTime( $datealler . ' ' . $heurealler);
        $dateretour = new DateTime($dateretour . ' ' . $heureretour);
        $currentDate = new DateTime();
        if ($datealler <= $currentDate) {
            header('Location: postertrajetar.php?erreur=11');
            exit();
        }
        if ($dateretour <= $datealler) {
            header('Location: postertrajetar.php?erreur=12');
            exit();
        }
        $datealler = $datealler->format('Y-m-d H:i:s');
        $dateretour = $dateretour->format('Y-m-d H:i:s');
        // Insetion des donnees de l'aller
        $req = $bdd->prepare('INSERT INTO trajet(Conducteur, Depart, Arrivee, Date, Placesrestantes, Commentaire, prix) VALUES(:conducteur, :departaller, :arriveealler, :datealler, :nb_passagers, :commentaire, :prix)');
        $req->execute(array(
            'conducteur' => $conducteur,
            'departaller' => $departaller,
            'arriveealler' => $arriveealler,
            'datealler' => $datealler,
            'nb_passagers' => $nb_passagers,
            'commentaire' => $commentaire,
            'prix' => $prix
        ));

        // Insertion du id du trajet aller et du id du passager dans la table appartient
        $trajetIdAller = $bdd->lastInsertId();
        $req2 = $bdd->prepare("INSERT INTO appartient(Idtrajet, Username) VALUES(:trajetIdAller, :conducteur)");
        $req2->bindParam(':trajetIdAller', $trajetIdAller);
        $req2->bindParam(':conducteur', $conducteur);
        $req2->execute();

        // Insetion des donnees du retour
        $req3 = $bdd->prepare('INSERT INTO trajet(Conducteur, Depart, Arrivee, Date, Placesrestantes, Commentaire, prix) VALUES(:conducteur, :departretour, :arriveeretour, :dateretour, :nb_passagers, :commentaire, :prix)');
        $req3->execute(array(
            'conducteur' => $conducteur,
            'departretour' => $departretour,
            'arriveeretour' => $arriveeretour,
            'dateretour' => $dateretour,
            'nb_passagers' => $nb_passagers,
            'commentaire' => $commentaire,
            'prix' => $prix
        ));

        // Insertion du id du trajet retour et du id du passager dans la table appartient
        $trajetIdRetour = $bdd->lastInsertId();
        $req4 = $bdd->prepare("INSERT INTO appartient(Idtrajet, Username) VALUES(:trajetIdRetour, :conducteur)");
        $req4->bindParam(':trajetIdRetour', $trajetIdRetour);
        $req4->bindParam(':conducteur', $conducteur);
        $req4->execute();

        // Redirection vers la page mes trajets
        header('Location: mestrajets.php');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>