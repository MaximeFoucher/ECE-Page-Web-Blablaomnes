<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    if ($_SERVER["REQUEST_METHOD"] == "POST" &&  isset($_POST['email'], $_POST['mot_de_passe'])) {
        //verifie si un utilisateur est associé à email et pwd
        $email = htmlspecialchars($_POST['email']);
        $mdp = htmlspecialchars($_POST['mot_de_passe']);
        $stmt = $bdd->prepare("SELECT Username FROM utilisateur WHERE email = :email AND pwd = :mdp");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->execute();
        if (isset($_GET['resultat'])) {
            $resultat=$_GET['resultat'];
        }



        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $row['Username'];
            session_start();
            $_SESSION['username'] = $username;

            //verifie si l'utilisateur est un admin
            $stmt2 = $bdd->prepare("SELECT Admin FROM utilisateur WHERE Username= :username");
            $stmt2->bindParam(':username', $username);
            $stmt2->execute();
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            $admin = $row['Admin'];

            //connexion etables
            if ($admin == 1) { 
                //utilisateur est un admin
                header('Location: pageadminutilisateur.php');
                exit();
            } else {
                if ($resultat==1) {
                    //signifie qu'une recherche etait faite avant donc rediriger vers la page de resultat avec les cookie
                    header('Location: resultatrecherche.php?id=1');
                    exit();
                } else {
                    //si pas de recherche precedente alors renvoie dans l'accueil par defaut
                    header('Location: index.php');
                    exit();
                }
            }
        } else { 
            //si la connexion n'est pas bien faite
            if ($resultat == 1) {
                //renvoie page connexion avec erreur et le fait qu'une recherche à été demandée
                header('Location: connexion.php?erreur=6&resultat=1'); 
                exit();
            }else {
                //renvoie page connexion avec erreur
                header('Location: connexion.php?erreur=6'); 
                exit();
            }
        }
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
