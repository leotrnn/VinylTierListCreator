<?php
require_once 'header.php';
require_once 'bdd/tierlist.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tier List de <?= htmlspecialchars($vinyl['nameVinyl']) ?></title>
    <link rel="stylesheet" href="css/tierList.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        .songs {
            min-height: 50px;
            /* Ajoutez une hauteur minimale pour que les zones soient visibles */
            border: 1px solid #ddd;
            /* Bordure pour la visibilité */
            padding: 5px;
            /* Ajout d'un espacement interne */
        }

        .song {
            margin: 5px 0;
            /* Espacement entre les morceaux */
            padding: 5px;
            /* Ajout d'un espacement interne aux morceaux */
            background-color: #f0f0f0;
            /* Fond pour les morceaux */
            cursor: move;
            /* Curseur pour indiquer que l'élément est déplaçable */
        }

        .songs.dragover {
            border: 2px dashed #F54640;
            /* Bordure pour indiquer où l'élément peut être déposé */
        }
    </style>
</head>

<body>
    <h2><?= htmlspecialchars($vinyl['nameVinyl']) ?> - <?= htmlspecialchars($vinyl['nameArtist']) ?></h2>

    <div class="tier" data-tier="S">
        <div class="songs" id="tier-S" data-tier-name="S">
            <div class="tier-label">S</div> <!-- Label pour le tier -->
            <!-- Les chansons seront ajoutées ici dynamiquement -->
        </div>
    </div>
    <div class="tier" data-tier="A">
        <div class="songs" id="tier-A" data-tier-name="A">
            <div class="tier-label">A</div> <!-- Label pour le tier -->
        </div>
    </div>
    <div class="tier" data-tier="B">
        <div class="songs" id="tier-B" data-tier-name="B">
            <div class="tier-label">B</div> <!-- Label pour le tier -->
        </div>
    </div>
    <div class="tier" data-tier="C">
        <div class="songs" id="tier-C" data-tier-name="C">
            <div class="tier-label">C</div> <!-- Label pour le tier -->
        </div>
    </div>
    <div class="tier" data-tier="D">
        <div class="songs" id="tier-D" data-tier-name="D">
            <div class="tier-label">D</div> <!-- Label pour le tier -->
        </div>
    </div>

    <div class="start">
        <div class="songs">
            <?php foreach ($songs as $song): ?>
                <div class="song" data-song="<?= htmlspecialchars($song) ?>"
                    style='background-image: url(<?= $vinyl["imgCover"] ?>);'>
                    <span class="song-title"><?= htmlspecialchars($song) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
    <script>
        // Rendre les éléments de la tier list triables
        document.querySelectorAll('.songs').forEach(function (songsContainer) {
            new Sortable(songsContainer, {
                group: 'songs', // Assurez-vous que tous les conteneurs partagent le même groupe
                animation: 150,
                filter: '.tier-label', // Exclut les éléments avec cette classe
                onStart: function (evt) {
                    evt.from.classList.add('dragover'); // Ajoute la classe de survol
                },
                onEnd: function (evt) {
                    evt.from.classList.remove('dragover'); // Supprime la classe de survol
                    console.log('Moved song: ' + evt.item.getAttribute('data-song') + ' to tier: ' + evt.to.parentElement.getAttribute('data-tier'));
                },
                // Ajoutez ces événements pour gérer le survol
                onAdd: function (evt) {
                    console.log('Added song: ' + evt.item.getAttribute('data-song') + ' to tier: ' + evt.to.parentElement.getAttribute('data-tier'));
                }
            });
        });



    </script>
</body>

</html>