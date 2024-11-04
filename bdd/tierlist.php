<?php
require_once 'bdd/bdd.php';

// Récupérer l'ID du vinyle depuis l'URL
$vinylId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = getDatabaseConnection();

// Rechercher les informations sur le vinyle
$stmt = $pdo->prepare("SELECT nameVinyl, nameArtist, imgCover FROM Vinyls WHERE idVinyl = ?");
$stmt->execute([$vinylId]);
$vinyl = $stmt->fetch();

if (!$vinyl) {
    die("Vinyle non trouvé.");
}

// Rechercher les morceaux associés au vinyle
$songsStmt = $pdo->prepare("SELECT nameSong FROM Songs WHERE idVinyl = ?");
$songsStmt->execute([$vinylId]);
$songs = $songsStmt->fetchAll(PDO::FETCH_COLUMN);

?>