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

    // Vérifier si le formulaire a été soumis et si toutes les données requises sont présentes
    if (
        $_SERVER["REQUEST_METHOD"] == "POST" &&
        isset($_POST['depart'], $_POST['arrivee'], $_POST['datealler'], $_POST['passagers'])
    ) {


        // Récupérer les données du formulaire
        $depart = $_POST['depart'];
        $arrivee = $_POST['arrivee'];
        $passagers = $_POST['passagers'];
        $datealler = $_POST['datealler'];

        $datealler1 = htmlspecialchars($_POST['datealler']);
        $datealler1 = new DateTime($datealler);
        $today = new DateTime(); // Obtenir la date du jour
        if ($datealler1 >= $today ) { //la date superieur à la date l'actuelle

            // Définir la durée de vie des cookies (20 minutes)
            $cookie_duration = time() + (1200 * 1); // 1200 secondes = 20 mins

            // Stocker les données dans des cookies
            setcookie('depart', $depart, $cookie_duration, "/");
            setcookie('arrivee', $arrivee, $cookie_duration, "/");
            setcookie('datealler', $datealler, $cookie_duration, "/");
            setcookie('passagers', $passagers, $cookie_duration, "/");
            if (isset($_SESSION['username'])) {
                // Si l'utilisateur est connecté, redirige vers le resultat
                header("Location: index.php?recherche=1");
                exit();
            } else {
                // sinon, redirige vers la connexion
                header("Location: connexion.php?resultat=1");
                exit();
            }
        } else {
            header("Location: index.php?erreur=7");
            //renvoie sur la page avec l'erreur de mauvaise date
            exit();
        }
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
