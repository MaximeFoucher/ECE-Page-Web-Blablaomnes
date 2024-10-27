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
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tel'])) {
        $tel = htmlspecialchars($_POST['tel']);
        $stmt = $bdd->prepare("UPDATE utilisateur SET Tel = :tel WHERE Username = :username");
        $stmt->bindParam(':tel', $tel);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        header('Location: profil.php');
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
        $email = htmlspecialchars($_POST['email']);
        $stmt = $bdd->prepare("UPDATE utilisateur SET Email = :email WHERE Username = :username");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        header('Location: profil.php');
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dateNaissance'])) {
        $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
        $stmt = $bdd->prepare("UPDATE utilisateur SET Date = :dateNaissance WHERE Username = :username");
        $stmt->bindParam(':dateNaissance', $dateNaissance);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        header('Location: profil.php');
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pwd'])) {
        $motDePasse = htmlspecialchars($_POST['pwd']);
        $stmt = $bdd->prepare("UPDATE utilisateur SET Pwd = :motDePasse WHERE Username = :username");
        $stmt->bindParam(':motDePasse', $motDePasse);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        header('Location: profil.php');
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vehicule'])) {
        // verifier que l'utilisateur a un etat conducteur = 1
        $req = $bdd->prepare("SELECT Etat_conducteur FROM utilisateur WHERE Username = :username");
        $req->bindParam(':username', $username);
        $req->execute();
        $row = $req->fetch(PDO::FETCH_ASSOC);
        $etat = $row['Etat_conducteur'];
        if ($etat == 1) {
            $vehicule = htmlspecialchars($_POST['vehicule']);
            $stmt = $bdd->prepare("UPDATE utilisateur SET Vehicule = :vehicule WHERE Username = :username");
            $stmt->bindParam(':vehicule', $vehicule);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
        }

        header('Location: profil.php');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
