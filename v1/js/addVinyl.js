
let debounceTimer;

function debounce(func, delay) {
    return function (...args) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.apply(this, args), delay);
    };
}

async function searchMusic(query, type) {
    if (!query) return;

    document.getElementById('loading').style.display = 'block'; // Afficher le loader

    const response = await fetch(`https://musicbrainz.org/ws/2/${type}?query=${query}&fmt=json`, {
        headers: {
            'User-Agent': 'NomDeTonApp/1.0 ( Contact : email@example.com )'
        }
    });

    document.getElementById('loading').style.display = 'none'; // Masquer le loader

    const data = await response.json();
    const suggestions = data[type === 'artist' ? 'artists' : 'releases'];
    displaySuggestions(suggestions, type);
}

function displaySuggestions(suggestions, type) {
    const suggestionBox = document.getElementById(`${type}Suggestions`);
    suggestionBox.innerHTML = '';

    const uniqueSuggestions = new Set(); // Utiliser un Set pour éviter les doublons

    suggestions.forEach(async item => {
        const displayText = type === 'artist' ? item.name : `${item.title} - ${item['artist-credit'][0].name}`;

        // Vérifiez si l'élément est déjà dans le Set
        if (!uniqueSuggestions.has(displayText)) {
            uniqueSuggestions.add(displayText); // Ajoutez l'élément au Set

            const suggestionItem = document.createElement('div');
            suggestionItem.classList.add('suggestion-item'); // Classe pour styliser les éléments

            const coverArtUrl = await fetchCoverArt(item.id); // Obtenir l'image de couverture
            const coverImg = document.createElement('img');
            coverImg.src = coverArtUrl || 'placeholder.webp'; // Utiliser une image par défaut si pas d'image
            coverImg.alt = displayText;
            coverImg.style.width = '50px'; // Largeur de l'image
            coverImg.style.height = 'auto'; // Hauteur automatique pour maintenir le ratio
            coverImg.style.marginRight = '10px'; // Espacement à droite de l'image

            suggestionItem.appendChild(coverImg); // Ajouter l'image à l'élément de suggestion
            suggestionItem.appendChild(document.createTextNode(displayText)); // Ajouter le texte

            suggestionItem.onclick = () => selectSuggestion(item, type);
            suggestionBox.appendChild(suggestionItem);
        }
    });
}

async function selectSuggestion(item, type) {
    if (type === 'artist') {
        document.getElementById('nameArtist').value = item.name;
    } else {
        document.getElementById('nameVinyl').value = item.title;
        document.getElementById('nameArtist').value = item['artist-credit'][0].name;

        // Récupérer l'URL de l'image de couverture
        const coverArtUrl = await fetchCoverArt(item.id); // Obtenir l'image de couverture
        if (coverArtUrl) {
            const coverPreview = document.getElementById('coverPreview');
            coverPreview.src = coverArtUrl;
            coverPreview.style.display = 'block'; // Afficher la prévisualisation

            // Ajouter l'URL de l'image dans un champ caché
            const coverImageInput = document.createElement('input');
            coverImageInput.type = 'hidden';
            coverImageInput.name = 'coverImage';
            coverImageInput.value = coverArtUrl;
            document.forms[0].appendChild(coverImageInput); // Ajouter à la forme
        }

        // Récupérer la tracklist
        const tracklist = await fetchTracklist(item.id);
        displayTracklist(tracklist);
    }
    document.getElementById(`${type}Suggestions`).innerHTML = '';
}


async function fetchTracklist(releaseId) {
    const response = await fetch(`https://musicbrainz.org/ws/2/release/${releaseId}?inc=recordings&fmt=json`);
    if (response.ok) {
        const data = await response.json();
        return data.media.flatMap(media => media.tracks).map(track => track.title); // Extraire les titres des morceaux
    }
    return []; // Retourner un tableau vide en cas d'échec
}

function displayTracklist(tracklist) {
const tracklistContainer = document.getElementById('tracklistContainer');
tracklistContainer.innerHTML = ''; // Vider le conteneur avant d'afficher la nouvelle tracklist

if (tracklist.length === 0) {
tracklistContainer.innerHTML = '<p>Aucune tracklist trouvée.</p>';
return;
}

tracklist.forEach((track, index) => {
const input = document.createElement('input'); // Créer un champ de saisie pour chaque morceau
input.type = 'hidden';
input.name = 'tracklist[]'; // Nom du champ pour soumettre un tableau
input.value = track; // Définir la valeur du morceau

tracklistContainer.appendChild(input); // Ajouter à la forme

const listItem = document.createElement('div'); // Créer un élément de liste
listItem.textContent = track; // Ajouter le titre du morceau
tracklistContainer.appendChild(listItem); // Ajouter à l'affichage
});
}

async function fetchCoverArt(releaseId) {
    const response = await fetch(`https://coverartarchive.org/release/${releaseId}`);
    if (response.ok) {
        const data = await response.json();
        // Filtrer les images pour garder uniquement celles avec des commentaires pertinents
        const relevantImages = data.images.filter(image => image.comment && image.comment.includes('front'));

        if (relevantImages.length > 0) {
            // Si des images pertinentes sont trouvées, retourner la première
            return relevantImages[0].image;
        }

        // Si aucune image pertinente n'est trouvée, retourner la première image
        return data.images.length > 0 ? data.images[0].image : 'placeholder.webp'; // Image par défaut
    }
    return 'placeholder.webp'; // Retourner l'image par défaut si la requête échoue
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('nameVinyl').addEventListener('input', debounce(function () {
        searchMusic(this.value, 'release');
    }, 300));

    document.getElementById('nameArtist').addEventListener('input', debounce(function () {
        searchMusic(this.value, 'artist');
    }, 300));
});
