<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idCampus = $_POST['IdCampus'];
        $requete = $bdd->prepare('SELECT * FROM campus WHERE IdCampus = ?');
        $requete->execute([$idCampus]);
        $campus = $requete->fetch();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $idCampus = $_POST['IdCampus'];
        $adresse = $_POST['Adresse'];
        $nom = $_POST['Nom'];

        $update = $bdd->prepare('UPDATE campus SET Adresse = ?, Nom = ? WHERE IdCampus = ?');
        $update->execute([$adresse, $nom, $idCampus]);

        header('Location: pageadmincampus.php');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) { //il faut ajouter une verification ex: si supprime alors que trajet en cours 
        // ou si supprime que faire des trajets passés 
        $idCampus = $_POST['IdCampus'];
        $adresse = $_POST['Adresse'];
        $nom = $_POST['Nom'];

        $update = $bdd->prepare('DELETE FROM campus WHERE IdCampus = ?');
        $update->execute([$idCampus]);

        header('Location: pageadmincampus.php');
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
    <title>Modifier Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Modifier Campus</h1>
        <form action="pageadminmodifier_campus.php" method="POST">
            <input type="hidden" name="IdCampus" value="<?php echo $campus['IdCampus']; ?>">
            <div class="mb-4">
                <label for="Adresse" class="block text-sm font-medium text-gray-700">Adresse :</label>
                <input type="text" id="Adresse" name="Adresse" value="<?php echo $campus['Adresse']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Nom" class="block text-sm font-medium text-gray-700">Nom :</label>
                <input type="text" id="Nom" name="Nom" value="<?php echo $campus['Nom']; ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <button type="submit" name="update" class="px-4 py-2 bg-blue-500 text-white rounded">Enregistrer les modifications</button>
            <button type="submit" name="delete" class="px-4 py-2 bg-red-500 text-white rounded">Supprimer le campus</button>
        </form>
    </div>
</body>

</html>
