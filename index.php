<?php
include('header.php');
include('bdd/index.php');

$vinyls = getVinyls();
?>


<h1>Catalogue de Vinyles</h1>
<a href="addVinyl.php">Ajouter un vinyle</a>

<div class="catalogue">
    <?php foreach ($vinyls as $vinyl){ ?>
        <div class="vinyl">
            <img src="<?php echo htmlspecialchars($vinyl['imgCover']); ?>"
                alt="<?php echo htmlspecialchars($vinyl['nameVinyl']); ?>">
            <h2><?php echo htmlspecialchars($vinyl['nameVinyl']); ?></h2>
            <p><?php echo htmlspecialchars($vinyl['nameArtist']); ?></p>
        </div>
    <?php } ?>
</div>
</body>

</html>