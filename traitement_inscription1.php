<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
if ($_SERVER["REQUEST_METHOD"] == "POST" &&  isset($_POST['email'], $_POST['password'], $_POST['confirm-password'])) {

    $email = $_POST['email'];

    $mdp = htmlspecialchars($_POST['password']);

    $mdp2 = htmlspecialchars($_POST['confirm-password']);

    // --> faire passer les nom et prenmo dans la page 2 pour verification

    if (substr(strstr($email, '@'), 1) == 'edu.ece.fr' || substr(strstr($email, '@'), 1) == 'ece.fr' || substr(strstr($email, '@'), 1) == 'omnesintervenant.com') { //verifie que la partie après le @ corresponde

        $stmt = $bdd->prepare("SELECT Username FROM utilisateur WHERE Email = :email"); //creer ligne dans bdd
        $stmt->bindParam('email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            header('Location: inscription1.php?erreur=20');
            exit();
        }

        if ($mdp == $mdp2) { //verifie la correspondance des deux mdp

            list($prenomverif, $nomverif) = explode('.', strtok($email, '@')); // recupere la partie prenom.nom sans le . et les assigne à prenom et nom

            session_start();

            $_SESSION['email'] = $email;
            $_SESSION['mdp'] = $mdp;

            header('Location: inscription2.php'); //renvoie à la page suivante
        } else {
            header("Location: inscription1.php?erreur=1"); //designe probleme avec les deux mdp et recommance le formulaire
        }
    } else {
        header("Location: inscription1.php?erreur=2"); //designe probleme avec email
    }
}
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}