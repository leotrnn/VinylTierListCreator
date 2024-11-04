<?php
session_start();

require_once 'bdd.php';

function addVinyl($nameVinyl, $nameArtist, $coverImage) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("INSERT INTO Vinyls (nameVinyl, nameArtist, imgCover) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$nameVinyl, $nameArtist, $coverImage])) {
        return $pdo->lastInsertId(); // Récupérer l'ID du vinyle
    } else {
        return false; // Échec
    }
}

function addSongs($vinylId, $songs) {
    $pdo = getDatabaseConnection(); // Établir une nouvelle connexion
    $stmt = $pdo->prepare("INSERT INTO Songs (idVinyl, nameSong) VALUES (?, ?)");
    
    foreach ($songs as $song) {
        if (!empty($song)) {
            // Debug : Afficher chaque chanson avant insertion
            echo "Inserting song: $song<br>";
            $stmt->execute([$vinylId, $song]); // Insérer chaque chanson
        }
    }
}

// Vérifiez si le formulaire a été soumis
if (isset($_POST['nameVinyl'], $_POST['nameArtist'], $_POST['coverImage'], $_POST['tracklist'])) {
    // Récupérer les valeurs du formulaire
    $nameVinyl = $_POST['nameVinyl'];
    $nameArtist = $_POST['nameArtist'];
    $coverImage = $_POST['coverImage'];
    $tracklist = $_POST['tracklist']; // Récupérer la tracklist

    // Insérer le vinyle dans la base de données
    $vinylId = addVinyl($nameVinyl, $nameArtist, $coverImage);

    if ($vinylId) {
        // Insérer chaque morceau dans la base de données
        addSongs($vinylId, $tracklist);

        // Stocker le message de succès dans une variable de session
        $_SESSION['success_message'] = "Vinyle ajouté avec succès.";
        
        // Rediriger vers la même page
        header("Location: addVinyl.php");
        exit; // S'assurer que le script s'arrête ici
    } else {
        echo "Erreur lors de l'ajout du vinyle.";
    }
}

// Afficher le message de succès si disponible
if (isset($_SESSION['success_message'])) {
    echo "<p style='color: green;'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']); // Effacer le message après l'affichage
}
?>
