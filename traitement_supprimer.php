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
    $username = $_SESSION['username'];
    // supression de la ligne ou l'utilisateur apparait dans la table appartient
    $stmt = $bdd->prepare("DELETE FROM appartient WHERE Username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // supression de la ligne ou l'utilisateur apparait dans la table chat
    $stmt = $bdd->prepare("DELETE FROM chat WHERE Username1 = :username OR Username2 = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    // supression de la ligne ou l'utilisateur apparait dans la table message
    $stmt = $bdd->prepare("DELETE FROM message WHERE Envoyeur = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // supression de la ligne ou l'utilisateur apparait dans la table trajet
    $stmt = $bdd->prepare("DELETE FROM trajet WHERE Conducteur = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // supression de la ligne ou l'utilisateur apparait dans la table utilisateur
    $stmt = $bdd->prepare("DELETE FROM utilisateur WHERE Username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    // Libérer toutes les variables de session
    session_unset();
    // Détruire la session
    session_destroy();
    header('Location: index.php');
    exit();
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
