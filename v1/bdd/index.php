<?php


require_once 'bdd.php'; 
function getVinyls()
{
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT * FROM Vinyls"); // Préparer la requête pour obtenir tous les vinyles
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats sous forme de tableau associatif
}