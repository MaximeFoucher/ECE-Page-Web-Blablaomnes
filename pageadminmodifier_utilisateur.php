<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Username = $_POST['Username'];
        $requete = $bdd->prepare('SELECT * FROM utilisateur WHERE Username = ?');
        $requete->execute([$Username]);
        $user = $requete->fetch();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $nom = $_POST['firstname'];
        $prenom = $_POST['lastname'];
        $email = $_POST['Email'];
        $date = $_POST['birthdate'];
        $tel = $_POST['phone'];
        $conducteur = $_POST['Etat_conducteur'];
        $argent = $_POST['Argent'];
        $immatriculation = $_POST['Immatriculation'];
        $vehicule = $_POST['Vehicule'];
        $model = $_POST['Model'];

        $update = $bdd->prepare('UPDATE utilisateur SET Nom = ?, Prenom = ?, Email = ?, Date = ?, Tel = ?, Etat_conducteur = ?, Argent = ?, Immatriculation = ?, Vehicule = ?, Model = ? WHERE Username = ?');
        $update->execute([$nom, $prenom, $email, $date, $tel, $conducteur, $argent, $immatriculation, $vehicule, $model, $Username]);

        header('Location: pageadminutilisateur.php');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) { //il faut ajouter une verification ex: si supprime alors que trajet en cours 
        // ou si supprime que faire des trajets passés 


        $update = $bdd->prepare('DELETE FROM utilisateur WHERE Username = ?');
        $update->execute([$Username]);

        header('Location: pageadminutilisateur.php');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['back'])) {

        header('Location: pageadminutilisateur.php');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accepted'])) {

        $update = $bdd->prepare('UPDATE utilisateur SET Etat_conducteur = ? WHERE Username = ?');
        $update->execute([1, $Username]);
        header('Location: pageadminutilisateur.php');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refused'])) {

        $update = $bdd->prepare('UPDATE utilisateur SET Immatriculation = NULL, Permis = NULL, Model = NULL, Vehicule = NULL WHERE Username = ?');
        $update->execute([$Username]);
        header('Location: pageadminutilisateur.php');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Modifier Utilisateur</h1>
        <form action="pageadminmodifier_utilisateur.php" method="POST">
            <input type="hidden" name="Username" value="<?php echo $user['Username']; ?>">
            <!--afficher la photo-->
            <div class="mb-4">
                <label for="firstname" class="block text-sm font-medium text-gray-700">Nom :</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo $user['Nom']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="lastname" class="block text-sm font-medium text-gray-700">Prenom :</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo $user['Prenom']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Email" class="block text-sm font-medium text-gray-700">Email :</label>
                <input type="text" id="Email" name="Email" value="<?php echo $user['Email']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="birthdate" class="block text-sm font-medium text-gray-700">Date de naissance :</label>
                <input type="text" id="birthdate" name="birthdate" value="<?php echo $user['Date']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Tel :</label>
                <input type="text" id="phone" name="phone" value="<?php echo $user['Tel']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <?php
            if ($user['Admin'] == 0) { //car l'admin n'a pas d'etat de conducteur
            ?>
                <div class="mb-4">
                    <label for="Etat_conducteur" class="block text-sm font-medium text-gray-700">Conducteur :</label>
                    <input type="text" id="Etat_conducteur" name="Etat_conducteur" value="<?php echo $user['Etat_conducteur']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="Argent" class="block text-sm font-medium text-gray-700">Argent :</label>
                    <input type="text" id="Argent" name="Argent" value="<?php echo $user['Argent']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <?php
                if ($user['Etat_conducteur'] == 1) { //si la personne est conducteur on affiche les infos 
                ?>
                    <!--afficher le permis-->
                    <div class="mb-4">
                        <label for="Immatriculation" class="block text-sm font-medium text-gray-700">Immatriculation :</label>
                        <input type="text" id="Immatriculation" name="Immatriculation" value="<?php echo $user['Immatriculation']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label for="Vehicule" class="block text-sm font-medium text-gray-700">Vehicule :</label>
                        <input type="text" id="Vehicule" name="Vehicule" value="<?php echo $user['Vehicule']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label for="Model" class="block text-sm font-medium text-gray-700">Model :</label>
                        <input type="text" id="Model" name="Model" value="<?php echo $user['Model']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                <?php
                } elseif ($user['Etat_conducteur'] == 0 && isset($user['Permis'])) {
                    $stmt = $bdd->prepare('SELECT Permis FROM utilisateur WHERE Username = :username'); // Remplacez 1 par l'ID de l'utilisateur concerné
                    $stmt->bindParam(':username', $user['Username']);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $photoData = $row['Permis'];
                    // Générer un nom de fichier unique basé sur le nom d'utilisateur
                    $photoFileName = 'photo_permis_' . $user['Username'] . '.jpg';
                    // Enregistrement des données binaires dans un fichier avec un nom unique
                    file_put_contents($photoFileName, $photoData);
                ?>
                    <div class="inline-block">
                        <img src="<?php echo $photoFileName; ?>" alt="photo" class="float-left w-20 h-auto">
                    </div>
                    <br>
                    <div class="clear-both mt-10">
                        <button type="submit" name="accepted" class="px-4 py-2 bg-green-500 text-white rounded">Accepter la demande de permis</button>
                        <!--ne pas pouvoir modifier si l'utilisateur est un admin-->
                        <button type="submit" name="refused" class="px-4 py-2 bg-red-500 text-white rounded">Refuser</button>
                    <?php
                }
                    ?>

                <?php
            } ?>
                <div class="clear-both mt-10">
                    <button type="submit" name="update" class="px-4 py-2 bg-green-500 text-white rounded">Enregistrer les modifications</button>
                    <!--ne pas pouvoir modifier si l'utilisateur est un admin-->
                    <button type="submit" name="back" class="px-4 py-2 bg-blue-500 text-white rounded">Retour</button>
                    <button type="submit" name="delete" class="px-4 py-2 bg-red-500 text-white rounded">Supprimer l'utilisateur</button>
                </div>
        </form>
    </div>
</body>

</html>