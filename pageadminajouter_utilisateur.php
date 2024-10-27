<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
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

        if ($conducteur == 1) {
   
            $add = $bdd->prepare('INSERT INTO utilisateur (Nom, Prenom, Email, Date, Tel, Etat_conducteur, Argent, Immatriculation, Vehicule, Model) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $add->execute([$nom, $prenom, $email, $date, $tel, $conducteur, $argent, $immatriculation, $vehicule, $model]);
            
            header('Location: pageadminutilisateur.php');
            exit();
        }else {
            $add = $bdd->prepare('INSERT INTO utilisateur (Nom, Prenom, Email, Date, Tel, Etat_conducteur, Argent) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $add->execute([$nom, $prenom, $email, $date, $tel, $conducteur, $argent]);
            
            header('Location: pageadminutilisateur.php');
            exit();
        }
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['back'])) {

        header('Location: pageadminutilisateur.php');
        exit();
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
    <title>Ajouter Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Ajouter Utilisateur</h1>
        <form action="pageadminajouter_utilisateur.php" method="POST">
            <div class="mb-4">
                <label for="firstname" class="block text-sm font-medium text-gray-700">Nom :</label>
                <input type="text" id="firstname" name="firstname" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="lastname" class="block text-sm font-medium text-gray-700">Prenom :</label>
                <input type="text" id="lastname" name="lastname" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Email" class="block text-sm font-medium text-gray-700">Email :</label>
                <input type="text" id="Email" name="Email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="birthdate" class="block text-sm font-medium text-gray-700">Date de naissance :</label>
                <input type="text" id="birthdate" name="birthdate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Tel :</label>
                <input type="text" id="phone" name="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Argent" class="block text-sm font-medium text-gray-700">Argent :</label>
                <input type="text" id="Argent" name="Argent" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Etat_conducteur" class="block text-sm font-medium text-gray-700">Conducteur :</label>
                <input type="text" id="Etat_conducteur" name="Etat_conducteur" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Immatriculation" class="block text-sm font-medium text-gray-700">Immatriculation :</label>
                <input type="text" id="Immatriculation" name="Immatriculation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Vehicule" class="block text-sm font-medium text-gray-700">Vehicule :</label>
                <input type="text" id="Vehicule" name="Vehicule" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Model" class="block text-sm font-medium text-gray-700">Model :</label>
                <input type="text" id="Model" name="Model" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <!--ajouter le champ des photos et la verification-->


            <button type="submit" name="add" class="px-4 py-2 bg-green-500 text-white rounded">Ajouter</button>
            <button type="submit" name="back" class="px-4 py-2 bg-blue-500 text-white rounded">Retour</button>

        </form>
    </div>
</body>

</html>