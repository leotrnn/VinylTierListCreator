<?php
// 1. Définir l'URL pour rechercher l'album
$albumQuery = filter_input(INPUT_GET, 'album', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Album à chercher

if ($albumQuery != "") {

    $deezerApiUrl = "https://api.deezer.com/search?q=" . urlencode($albumQuery); // Utiliser un limit plus élevé pour avoir plus de résultats

    // 2. Récupérer les données de l'API Deezer
    $response = file_get_contents($deezerApiUrl);
    if ($response === FALSE) {
        die('Erreur lors de la récupération des données de Deezer');
    }

    // 3. Décoder les données JSON
    $data = json_decode($response, true);

    // 4. Récupérer les albums uniques
    $albums = [];
    foreach ($data['data'] as $track) {
        // Structure de l'album avec les informations demandées
        $album = [
            "idAlbum" => $track["album"]["id"],
            "title" => $track["album"]["title"],
            "artist" => $track["artist"]["name"],
            "cover" => $track["album"]["cover_xl"]
        ];
        // Ajouter l'album dans le tableau si il n'est pas déjà présent
        $albumId = $album["idAlbum"];
        if (!isset($albums[$albumId])) {
            $albums[$albumId] = $album; // Ajouter l'album si non présent
        }
    }

    // Fonction pour trier les albums par exact match, similitude de titre puis popularité
    function sortAlbums($albums, $albumQuery)
    {
        $albumScores = [];

        foreach ($albums as $album) {
            // Récupérer les morceaux de l'album
            $tracklistResponse = file_get_contents("https://api.deezer.com/album/{$album['idAlbum']}/tracks");
            $tracklist = json_decode($tracklistResponse, true);

            $maxRank = 0; // Initialiser le rang maximum
            foreach ($tracklist['data'] as $track) {
                if (isset($track['rank']) && $track['rank'] > $maxRank) {
                    $maxRank = $track['rank']; // Trouver le morceau le plus populaire
                }
            }

            // Calcul des scores
            $exactMatch = (strcasecmp($album['title'], $albumQuery) === 0) ? 1 : 0; // Exact match
            $levenshteinDistance = levenshtein(strtolower($album['title']), strtolower($albumQuery)); // Similitude
            $levenshteinScore = (strlen($album['title']) - $levenshteinDistance) / strlen($album['title']); // Calculer un score de similitude
            $containsQuery = strpos(strtolower($album['title']), strtolower($albumQuery)) !== false ? 1 : 0; // Vérifie si le terme recherché est contenu dans le titre

            // Ajouter à un tableau avec le score basé sur la correspondance exacte, la similitude et la popularité
            $albumScores[] = [
                'album' => $album,
                'exactMatch' => $exactMatch,
                'levenshteinScore' => $levenshteinScore,
                'containsQuery' => $containsQuery, // Si l'album contient le terme recherché
                'maxRank' => $maxRank
            ];
        }

        // Trier les albums par : 
        // 1. Exact match
        // 2. Similitude du titre (Levenshtein)
        // 3. Contient le terme recherché
        // 4. Popularité (rank)
        usort($albumScores, function ($a, $b) {
            // Priorité aux albums ayant une correspondance exacte
            if ($b['exactMatch'] !== $a['exactMatch']) {
                return $b['exactMatch'] - $a['exactMatch'];
            }

            // Priorité aux albums ayant une similitude de titre plus élevée
            if ($b['levenshteinScore'] !== $a['levenshteinScore']) {
                return $b['levenshteinScore'] - $a['levenshteinScore'];
            }

            // Priorité aux albums qui contiennent le terme recherché
            if ($b['containsQuery'] !== $a['containsQuery']) {
                return $b['containsQuery'] - $a['containsQuery'];
            }

            // Si tout est égal, trier par popularité (rank)
            return $b['maxRank'] - $a['maxRank'];
        });

        // Vérifier si on a moins de 3 albums avec un taux de similitude > 80%
        $filteredAlbums = array_filter($albumScores, function ($albumScore) {
            return $albumScore['levenshteinScore'] >= 0.8; // Similitude de 80% ou plus
        });

        // Si moins de 3 albums ont un score >= 80%, rechercher dans le nom de l'artiste
        if (count($filteredAlbums) < 3) {
            // Ajouter les albums correspondants à l'artiste en filtrant pour ne pas inclure les albums complètement différents
            $albumScores = array_merge($albumScores, searchByArtist($albumQuery));
        }

        // Limiter à 3 albums au maximum
        return array_slice($albumScores, 0, 9); 
    }

    // Fonction pour rechercher dans le nom de l'artiste
    function searchByArtist($albumQuery)
    {
        $deezerApiUrl = "https://api.deezer.com/search?q=" . urlencode($albumQuery) . "&order=artist"; // Recherche par artiste
        $response = file_get_contents($deezerApiUrl);
        $data = json_decode($response, true);
        
        $artistAlbums = [];
        foreach ($data['data'] as $track) {
            // Filtrer les albums avec des titres similaires au terme recherché
            if (strpos(strtolower($track["album"]["title"]), strtolower($albumQuery)) !== false) {
                $album = [
                    "idAlbum" => $track["album"]["id"],
                    "title" => $track["album"]["title"],
                    "artist" => $track["artist"]["name"],
                    "cover" => $track["album"]["cover_xl"]
                ];
                $artistAlbums[] = [
                    'album' => $album,
                    'levenshteinScore' => 0, // Score par défaut pour la recherche par artiste
                    'exactMatch' => 0,
                    'containsQuery' => 1,
                    'maxRank' => 0
                ];
            }
        }
        return $artistAlbums;
    }

    // 5. Trier les albums en fonction des critères : exact, similitude, popularité
    $sortedAlbums = sortAlbums($albums, $albumQuery);
}
?>
