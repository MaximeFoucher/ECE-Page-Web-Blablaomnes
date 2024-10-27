<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    // Inserer le message envoyé par l'utilisateur

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
        session_start();
        $envoyeur = $_SESSION['username'];
        $receveur = $_COOKIE['chatusername'];
        $date = new DateTime();
        $message = ($_POST['message']);
        $lu = 0;

        // trouver le id du chat entre les deux utilisateurs
        $req = $bdd->prepare('SELECT Idchat FROM chat WHERE (Username1 = :envoyeur AND Username2 = :receveur) 
        OR (Username1 = :receveur AND Username2 = :envoyeur)');
        $req->execute(array('envoyeur' => $envoyeur, 'receveur' => $receveur));
        $resultat = $req->fetch();
        $idchat = $resultat['Idchat'];

        // inserer le message dans la table message 
        $req = $bdd->prepare('INSERT INTO message (Idchat, Envoyeur, Message, Date, Lu) VALUES (:idchat, :envoyeur, :message, :date, :lu)');
        $req->execute(array('idchat' => $idchat, 'envoyeur' => $envoyeur, 'message' => $message, 'date' => $date->format('Y-m-d H:i:s'), 'lu' => $lu));
        header('Location: messagechat.php');

        // Rediriger vers la page messagechat.php
        header('Location: messagechat.php');
    }
    $envoyeur = $_SESSION['username'];
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
