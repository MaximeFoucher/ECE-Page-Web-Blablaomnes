<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    if ($_SERVER["REQUEST_METHOD"] == "POST" &&  isset($_POST['marque_du_vehicule'], $_POST['modele_du_vehicule'], $_POST['immatriculation'], $_FILES['Permis_de_conduire']) && $_FILES['Permis_de_conduire']['error'] == 0) //ajouter permis
    
        // Testons, si le fichier est trop volumineux
        if ($_FILES['Permis_de_conduire']['size'] > 1000000) {
            echo "L'envoi n'a pas pu être effectué, erreur ou image trop volumineuse";
            return;
        }
    // Testons, si l'extension n'est pas autorisée
    $fileInfo = pathinfo($_FILES['Permis_de_conduire']['name']);
    $extension = $fileInfo['extension'];
    $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
    if (!in_array($extension, $allowedExtensions)) {
        echo "L'envoi n'a pas pu être effectué, l'extension {$extension} n'est pas autorisée";
        return;
    }    
    
    
    session_start();
    $marque_du_vehicule = htmlspecialchars($_POST['marque_du_vehicule']);
    $modele_du_vehicule = htmlspecialchars($_POST['modele_du_vehicule']);  
    $immatriculation = htmlspecialchars($_POST['immatriculation']);
    $email = $_SESSION['email'];
    $mdp = $_SESSION['mdp'];

    $id =$_SESSION['username'];

    if (preg_match('/[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/', $immatriculation)) { //verifie le format plaque immatriculation
        
        $stmt2 = $bdd->prepare("UPDATE utilisateur SET Vehicule= :marque_du_vehicule, Immatriculation= :immatriculation, Model= :modele_du_vehicule, Permis= :permis WHERE Username = :id");//màj de la table
        $stmt2->bindParam('marque_du_vehicule', $marque_du_vehicule);
        $stmt2->bindParam('immatriculation', $immatriculation);
        $stmt2->bindParam('modele_du_vehicule', $modele_du_vehicule);
        //$stmt2->bindParam('Etatconducteur', 1); il faut mettre l'etat actif du conducteur une fois que le permis a été validé 
        // Récupération des données de l'image
        $permis_photo = file_get_contents($_FILES['Permis_de_conduire']['tmp_name']);
        // lier des données de l'image
        $stmt2->bindParam(':permis',$permis_photo, PDO::PARAM_LOB);
        $stmt2->bindParam('id', $id);
        
        $stmt2->execute();

        header('Location: index.php');
    }else {
        header('Location: inscription3.php?erreur=5');
        exit();
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
