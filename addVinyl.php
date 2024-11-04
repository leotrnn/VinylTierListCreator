<?php

include('header.php');

include('bdd/addVinyl.php');

echo (isset($_SESSION["success_message"])) ? $_SESSION["success_message"] : "";

?>


<body>
    <h2>Ajouter un Vinyle</h2>
    <form action="addVinyl.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="nameVinyl">Nom du Vinyle :</label>
            <input type="text" id="nameVinyl" name="nameVinyl" autocomplete="off">
            <div id="releaseSuggestions" class="suggestions"></div>
        </div>
        <div>
            <label for="nameArtist">Nom de l'Artiste :</label>
            <input type="text" id="nameArtist" name="nameArtist" autocomplete="off">
            <div id="artistSuggestions" class="suggestions"></div>
        </div>
        <div>
            <img id="coverPreview" alt="Prévisualisation de la couverture" style="display:none;">
        </div>
        <div id="loading" style="display:none;">Chargement...</div> <!-- Indicateur de chargement -->
        <div id="tracklistContainer" class="tracklist-container"></div>
        <!-- Conteneur pour la tracklist -->
        <input type="submit" value="Ajouter le Vinyle">
    </form>
</body>

</html>