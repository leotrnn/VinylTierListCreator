<?php

include('View/header.php');

include('Model/searchResult.php');

?>

<form class="searchField" action="index.php" autocomplete="off">
    <h1>Vinyl Tierlist Creator™</h1>
    <input type="text" name="album" id="" placeholder="🔎 Search for an album..." value="<?= $albumQuery ?>">
</form>

<?php

if ($albumQuery != "") {
    if (!empty($sortedAlbums)) { ?>
        <div class="card-container">
            <?php
            foreach ($sortedAlbums as $albumScore) {
                $album = $albumScore['album'];
                $maxRank = $albumScore['maxRank'];
            ?>
                <div class="card">
                    <img src="<?= $album['cover'] ?>" alt="Album Cover">
                    <div class="card-info">
                        <h3><?= htmlspecialchars($album['title']) ?> - <?= htmlspecialchars($album['artist']) ?></h3>
                        <div class="actions">
                            <a href="tierlist.php" class="rank">Rank this album</a>
                            <a href="https://www.deezer.com/album/<?= $album['idAlbum'] ?>" class="view" target="_blank">View on Deezer</a>
                        </div>
                    </div>
                </div>
            <?php         } ?>
        </div>

    <?php    } else { ?>
        <div class="noResultField">
            <div class="noResult">
                <img src="View/img/noResult.png" alt="no result">
                <h1>We're sorry, no result matches your search</h1>
            </div>
        </div>

<?php   }
}

?>
</body>

</html>