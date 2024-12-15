<h1 align="center">Kurei's Vinyl tierlist creator</h1>

<p align="justify">Imaginez : vous écoutez un superbe vinyle, vous voulez à tout prix rank les sons de cet album avec vos amis, mais en vous rendant sur tierlistmaker, personne n'a créé de tierlist pour cet album. Pas cool hein ? Mais pas de panique, <em>Vinyl tierlist creator</em> est un créateur dynamique de tierlist pour albums, pour que vous n'ayez plus jamais à devoir créer de tierlist vous même !</p>

<h2 align="center">Table des matières</h2>

1. [Fonctionnement](#fonctionnement)
4. [Technologies utilisées](#technologies-utilisées)
5. [Installation et utilisation](#installation-et-utilisation)
6. [Potentiels bugs](#potentiels-bugs)

<h2 align="center">Fonctionnement</h2>
<h3>1. cherchez l'album que vous souhaitez rank</h3>
![image](https://github.com/user-attachments/assets/33b53f0e-081d-4214-ac8a-62ad5fa55522)


<h2 align="center">Installation et utilisation</h2>

<h3>1. Démarrez vos services apache2 et mysql</h3>

```bash
sudo service apache2 start
sudo service mysql start
```

<h3>3. Rendez-vous dans le répertoire des projets web locaux</h3>

```bash
# Si le sous-système utilisé est WSL :
cd /var/www/html
```

<h3>2. Clonez le dépôt</h3>
      
```bash
git clone https://github.com/leotrnn/swissexplorers.git
```

<h3>7. Ouvrez votre navigateur web et allez sur localhost</h3>

<h2 align="center">Potentiels bugs</h2>
<h3>Impossible de cloner le projet car le dossier /var/www est protégé</h3>
      
```bash
# Donner l'accès d'écriture au dossier www
sudo chown -R www-data:www-data /var/www
sudo chmod -R g+rwX /var/www
sudo chmod 0777 /var/www
sudo chown -R [VOTRE USER] var/www
```

