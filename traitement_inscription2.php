<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    if ($_SERVER["REQUEST_METHOD"] == "POST" &&  isset($_POST['firstname'], $_POST['lastname'], $_POST['tel'], $_POST['birthdate'], $_FILES['profile-photo']) && $_FILES['profile-photo']['error'] == 0)

        // Testons, si le fichier est trop volumineux
        if ($_FILES['profile-photo']['size'] > 1000000) {
            echo "L'envoi n'a pas pu être effectué, erreur ou image trop volumineuse";
            return;
        }
    // Testons, si l'extension n'est pas autorisée
    $fileInfo = pathinfo($_FILES['profile-photo']['name']);
    $extension = $fileInfo['extension'];
    $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
    if (!in_array($extension, $allowedExtensions)) {
        echo "L'envoi n'a pas pu être effectué, l'extension {$extension} n'est pas autorisée";
        return;
    }
    session_start();
    $prenom = ucfirst(htmlspecialchars($_POST['firstname'])); //met la premiere lettre en maj
    $nom = strtoupper(htmlspecialchars($_POST['lastname'])); //met tout en maj
    $tel = htmlspecialchars($_POST['tel']);
    $date_naissance = htmlspecialchars($_POST['birthdate']);
    $age = (new DateTime())->diff(new DateTime($date_naissance))->y; //fait la différence entre la date actuelle et la date de naissance renseignée
    $email = $_SESSION['email'];
    $mdp = $_SESSION['mdp'];


    if (preg_match("/^(?:\+33|0)[1-9](?:[0-9]{2}){4}$/", $tel)) { //verifie num tel
        if ($age >= 16 && $age < 90) { // verifie l'age

            $stmt = $bdd->prepare("INSERT INTO utilisateur(Nom, Prenom, email, pwd, Tel, Date, Photo) VALUES(:nom, :prenom, :email, :mdp, :tel, :age, :photo)"); //creer ligne dans bdd
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mdp', $mdp);
            $stmt->bindParam(':tel', $tel);
            $stmt->bindParam(':age', $date_naissance);
            // Récupération des données de l'image
            $profile_photo = file_get_contents($_FILES['profile-photo']['tmp_name']);
            // lier des données de l'image
            $stmt->bindParam(':photo', $profile_photo, PDO::PARAM_LOB);
            $stmt->execute();
            $stmt = $bdd->prepare("SELECT Username FROM utilisateur WHERE email= :email AND pwd= :mdp"); //pour recuperer l'id de la personne pour mettre à jour sa table
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mdp', $mdp);
            $stmt->execute();
            // Récupération du résultat
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            // attribut l'id
            $id = $resultat['Username'];
            $_SESSION['username'] = $id;

            $idbot = 2;

            // Ajout du chat avec le bot
            $ajoutbot = $bdd->prepare('INSERT INTO chat (Username1, Username2) VALUES (?, ?)');
            $ajoutbot->execute([$_SESSION['username'], $idbot]);

            // Récupération de l'id du chat qui vient d'être créé
            $idchat = $bdd->prepare('SELECT Idchat FROM chat WHERE Username1 = ? AND Username2 = ?');
            $idchat->execute([$_SESSION['username'], $idbot]);
            $result = $idchat->fetch(PDO::FETCH_ASSOC);

            $messagebot = 'Bienvenue';
            $time = new DateTime();

            // Envoi du premier message
            $messagebienvue = $bdd->prepare('INSERT INTO message (Idchat, Message, Date, Envoyeur) VALUES (?, ?, ?, ?)');
            $messagebienvue->execute([$result['Idchat'], $messagebot, $time->format('Y-m-d H:i:s'), $idbot]);


            if (isset($_POST['conducteur'])) {
                header('Location: inscription3.php');
            } else {
                header('Location: index.php');
            }
        } else {
            header('Location: inscription2.php?erreur=4'); //pour montrer que l'age n'est pas correct
            exit();
        }
    } else {
        header('Location: inscription2.php?erreur=3'); //pour montrer que le tel est faux
        exit();
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

/*<?php
// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=votre_base_de_donnees', 'votre_utilisateur', 'votre_mot_de_passe');

// Récupération des données binaires de la photo depuis la base de données
$stmt = $bdd->query("SELECT Photo FROM utilisateur WHERE id = 6"); // Remplacez 1 par l'ID de l'utilisateur concerné
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$photoData = $row['Photo'];

// Enregistrement des données binaires dans un fichier
file_put_contents('photo.jpg', $photoData); // Le fichier sera enregistré sous le nom photo.jpg

echo "Photo enregistrée avec succès.";
?>*/ 

//mettre cela pour recuperer la photo et la sauvegarder un temps sous le nom 'photo.jpg'
