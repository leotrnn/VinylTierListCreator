<?php
include("View/header.php");

// Récupérer l'ID de l'album depuis l'URL
$albumId = filter_input(INPUT_GET, 'albumId', FILTER_SANITIZE_NUMBER_INT);

function getAlbumDetails($albumId)
{
    // Appel à l'API Deezer pour récupérer les détails de l'album
    $url = "https://api.deezer.com/album/$albumId";
    $response = file_get_contents($url);

    // Vérifier si la requête a réussi
    if ($response === FALSE) {
        return false;  // Retourne false si l'API échoue
    }

    // Décoder les données JSON de la réponse
    $data = json_decode($response, true);

    // Vérifier si les données sont valides
    if (isset($data['id'])) {
        // Extraire les informations nécessaires
        $albumName = $data['title'];
        $artistName = $data['artist']['name'];
        $coverImage = $data['cover_xl'];  // Cover en grande taille

        // Retourner les informations sous forme de tableau associatif
        return [
            'albumName' => $albumName,
            'artistName' => $artistName,
            'coverImage' => $coverImage
        ];
    }

    return false;  // Retourne false si les informations ne sont pas disponibles
}



if ($albumId) {
    // Appel à l'API Deezer pour récupérer les morceaux de l'album
    $response = file_get_contents("https://api.deezer.com/album/$albumId/tracks");
    if ($response === FALSE) {
        die('Erreur lors de la récupération des morceaux de l\'album');
    }

    // Décoder les données JSON
    $data = json_decode($response, true);
    $songs = array_column($data['data'], 'title');
    $vinyl = [
        'nameVinyl' => $data['data'][0]['album']['title'],
        'nameArtist' => $data['data'][0]['album']['artist']['name'],
        'imgCover' => $data['data'][0]['album']['cover_xl']
    ];
} else {
    die('Album ID manquant');
}



?>

<style>
    .song {
        position: relative;
        text-align: center;
        font-size: 1.2rem;
        color: var(--backColor);
        display: flex;
        justify-content: center;
        align-items: center;
        white-space: wrap;
        /* Empêche le texte de se diviser sur plusieurs lignes */
        overflow: hidden;
        /* Cache le texte qui dépasse */
        text-overflow: ellipsis;
        /* Ajoute les points de suspension (...) */
    }

    .song::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('<?= getAlbumDetails($albumId)["coverImage"] ?>');
        background-size: cover;
        background-position: center;
        filter: brightness(50%);
        /* Applique le filtre seulement à l'image de fond */
        z-index: 1;
        /* Assure que l'image soit en dessous du texte */
    }

    .song-title {
        position: relative;
        z-index: 2;
        /* Le texte est au-dessus de l'image */
        color: var(--backColor);
        /* Utiliser une couleur claire pour contraster avec l'arrière-plan sombre */
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8);
        /* Ombre portée pour le texte */
    }
</style>
<a href="index.php" class="view">Cancel</a>
<h2 class="info"><?= htmlspecialchars(getAlbumDetails($albumId)["albumName"]) ?> - <?= htmlspecialchars(getAlbumDetails($albumId)["artistName"]) ?></h2>
<div class="tierlist">
    <div class="tier" data-tier="S">
        <div class="songs" id="tier-S" data-tier-name="S">
            <div class="tier-label s editable-label">S</div>
        </div>
    </div>
    <div class="tier" data-tier="A">
        <div class="songs" id="tier-A" data-tier-name="A">
            <div class="tier-label a editable-label">A</div>
        </div>
    </div>
    <div class="tier" data-tier="B">
        <div class="songs" id="tier-B" data-tier-name="B">
            <div class="tier-label b editable-label">B</div>
        </div>
    </div>
    <div class="tier" data-tier="C">
        <div class="songs" id="tier-C" data-tier-name="C">
            <div class="tier-label c editable-label">C</div>
        </div>
    </div>
    <div class="tier" data-tier="D">
        <div class="songs" id="tier-D" data-tier-name="D">
            <div class="tier-label d editable-label">D</div>
        </div>
    </div>

    <div class="start">
        <div class="songs">
            <?php foreach ($songs as $song): ?>
                <div class="song" data-song="<?= htmlspecialchars($song) ?>">
                    <span class="song-title"><?= htmlspecialchars($song) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>

function adjustFontSizeToFit(container) {
    const elements = document.querySelectorAll(container);
    elements.forEach(el => {
        let fontSize = 2; // Taille de police initiale en rem
        const minFontSize = 0.8; // Taille minimale de police en rem

        // Réinitialiser la taille de police avant de recalculer
        el.style.fontSize = `${fontSize}rem`;

        while ((el.scrollWidth > el.clientWidth || el.scrollHeight > el.clientHeight) && fontSize > minFontSize) {
            fontSize -= 0.1; // Réduire progressivement la taille
            el.style.fontSize = `${fontSize}rem`;
        }
    });
}

// Initialisation au chargement
window.addEventListener('load', () => {
    adjustFontSizeToFit('.tier-label, .song-title');
});

// Réagir aux changements de taille de fenêtre
window.addEventListener('resize', () => {
    adjustFontSizeToFit('.tier-label, .song-title');
});



document.querySelectorAll('.tier-label').forEach(label => {
    const adjustFontSize = () => {
        const span = label.querySelector('span');
        if (!span) return;

        // Réinitialise la taille pour mesurer correctement
        span.style.fontSize = '1.5rem';
        
        // Vérifie si le texte dépasse
        if (span.scrollWidth > label.clientWidth) {
            span.classList.add('resized'); // Applique la classe pour réduire
        } else {
            span.classList.remove('resized'); // Réinitialise si tout tient
        }
    };

    // Vérifie à chaque fois que le label est modifié
    label.addEventListener('input', adjustFontSize);

    // Initialisation
    adjustFontSize();
});


    document.querySelectorAll('.editable-label').forEach(label => {
        label.addEventListener('click', () => {
            // Rendre le label éditable
            label.setAttribute('contenteditable', 'true');
            label.focus();
        });

        label.addEventListener('blur', () => {
            // Enregistrer les changements et désactiver l'édition
            label.setAttribute('contenteditable', 'false');

            // Mettre à jour le nom du tier dans les attributs ou ailleurs si nécessaire
            const parentTier = label.closest('.tier');
            if (parentTier) {
                parentTier.setAttribute('data-tier-name', label.textContent.trim());
            }
        });

        label.addEventListener('keydown', (e) => {
            // Désactiver l'édition si l'utilisateur appuie sur "Entrée"
            if (e.key === 'Enter') {
                e.preventDefault(); // Empêcher le retour à la ligne
                label.blur(); // Déclenche l'événement "blur"
            }
        });
    });


    // Sélectionner tous les titres des chansons
    const songTitles = document.querySelectorAll('.song-title');

    songTitles.forEach(function(title) {
        // Récupérer le texte du titre
        let songText = title.innerText;

        // Supprimer tout ce qui vient après ( et le ( lui-même
        let cleanedText = songText.split('(')[0].trim(); // On prend la partie avant '('

        // Mettre à jour le titre avec le texte modifié
        title.innerText = cleanedText;
    });
</script>
<script>
    // Rendre les éléments de la tier list triables
    document.querySelectorAll('.songs').forEach(function(songsContainer) {
        new Sortable(songsContainer, {
            group: 'songs', // Assurez-vous que tous les conteneurs partagent le même groupe
            animation: 150,
            filter: '.tier-label', // Exclut les éléments avec cette classe
            onStart: function(evt) {
                evt.from.classList.add('dragover'); // Ajoute la classe de survol
            },
            onEnd: function(evt) {
                evt.from.classList.remove('dragover'); // Supprime la classe de survol
                console.log('Moved song: ' + evt.item.getAttribute('data-song') + ' to tier: ' + evt.to.parentElement.getAttribute('data-tier'));
            },
            // Ajoutez ces événements pour gérer le survol
            onAdd: function(evt) {
                console.log('Added song: ' + evt.item.getAttribute('data-song') + ' to tier: ' + evt.to.parentElement.getAttribute('data-tier'));
            }
        });
    });
</script>
</body>

</html>