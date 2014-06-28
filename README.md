edge-laser-simulator
====================

A NodeJS laser simulator for AlsaceDigitale/edgefest-hacking

![alt text](http://i.imgur.com/2CVHDOs.png "Demo")

##Sommaire
* [Installation du simulateur](#remarques)
* [Manette Xbox : mapping des touches](#gestion-des-touches)
* [EdgeLaserPHP (implé. PHP par morgan-linux)](#edgelaserphp)
* [EdgeLaserPython (implé. Python par Yannick Jost)](#edgelaserpython)
* [Format de fontes](#elf-fonts)

##Remarques
Ceci est un serveur de test pour commencer à coder votre jeu pour l'"Edge Laser".
Ce repo contient le kit de développement nécessaire, à savoir :
* Serveur Node.js
* Visualisateur en websocket (via votre navigateur Web, Chromium/Chrome testé uniquement)
* Scripts d'exemples (_faites des pull requests, tous langages !_)

**Attention.** Le protocole est entièrement implémenté au niveau fonctionnel, et ceci dans la version du protocole indiquée dans index.html. Néanmoins, toutes les vérifications de format des requêtes client ne sont pas encore effectuées. Une requête valide mais avec des arguments en trop fonctionnera, mais des dépassements de buffer auront lieu sur des requêtes mal formées et trop courtes.

##Installation
* Clonez ou téléchargez ce repo en local
* Installez Node.js dernière version
* (Optionnel) PHP-CLI pour tester les exemples en PHP et/ou faire un jeu en PHP : `apt-get install php5-cli` sous Debian-like. **Attention a bien installer la dernière version de PHP (5.5 minimum).**
* C'est bon !

###Installation sous Mac
Certaines personnes ont rencontré des problèmes lors de l'exécution du script d'exemple sous Mac et Windows. Voilà comment corriger le problème.
####Solution
* Installer PHP depuis ce site : http://php-osx.liip.ch/
* ...et en version 5.5 minimum !

####Solution dernier recours ?
* Installer MAMP (http://www.mamp.info/en/downloads/)
* Ajouter un alias `phpmamp='/Applications/MAMP/bin/php/php5.5.10/bin/php'` à la fin du fichier .bash_profile pour exécuter le fichier php via l'environement MAMP.
* Pour exécuter samples.php via le terminal, utiliser l'alias phpmamp au lieu de php, comme suit : `phpmamp shapes.php`

##Run the sauce
* `node main.js` dans edge-laser-simulator/node
* Ouvrez index.html dans un navigateur (dans la pseudo-console doit-être affiché _Socket is ready_)
* (Exemple) `php shapes.php` dans edge-laser-simulator/samples/php (Exemple Windows : `C:\PHP\php.exe -f "shapes.php"`)

Dans votre navigateur, la liste des clients a du être mise à jour. Et par exemple, si vous lancez plusieurs fois le script PHP dans des consoles différentes, la liste contiendra plusieurs fois le même jeu. Vous êtes alors habilité à changer de jeu à la volonté.
Changer de jeu impliquera l'envoi de la commande STOP au jeu en cours et l'envoi de la commande GO au jeu visé.

##Gestion des touches
La gestion des touches est multi-touches et les boutons des deux manettes XBOX sont mappés sur le clavier selon le schéma suivant.
![xbox-mapped-keyboard](http://alembic-dev.com/dl/edgefest/kbd.png)

Pour que les touches soient capturées, c'est le visualisateur JavaScript qui doit avoir le focus (la fenêtre de visualisation ouverte dans votre navigateur).

##EdgeLaserPHP
EdgeLaserPHP est une petite librairie contenue dans le fichier edge-laser-simulator/samples/php/EdgeLaser.ns.php
Elle permet de se libérer de la couche réseau et du protocole lors du développement d'un jeu en PHP pour l'"Edge Laser".

####Include the sauce
```php
include('EdgeLaser.ns.php');

use EdgeLaser\LaserGame;
use EdgeLaser\LaserColor;
use EdgeLaser\XboxKey; 
```

####Créer un nouveau jeu
```php
$game = new LaserGame('SuperTetris');
$game->setResolution(500)->setDefaultColor(LaserColor::LIME);
```

* `setResolution` **est obligatoire** et va définir une résolution virtuelle (la résolution finale étant toujours de 65535*65535). Cela permet au développeur de ne pas travailler avec des valeurs inhabituelles de plusieurs dizaines de milliers de pixels. A l'écran, le rendu sera le même pour n'importe quelle résolution virtuelle.
* `setDefaultColor` **est facultatif** et permet d'appliquer une couleur de base aux objets ajoutés plus tard qui n'auraient pas de couleur renseignée.

####Ingame
Le code de base d'une boucle de jeu sous EdgeLaserPHP est le suivant :

```php
while(true)
{
	$game->receiveServerCommands();

	if(!$game->isStopped())
	{
		//Doing some stuff
		$game->refresh();
	}
}
```

####Liste des méthodes
#####LaserGame LaserGame::setResolution(int $resolutionXY)
Définit la résolution virtuelle pour cette instance de jeu

#####LaserGame LaserGame::setDefaultColor(int LaserColor::$color)
Définit la couleur par défaut des formes (cf. référence des couleurs)

#####LaserGame LaserGame::setFramerate(int $fps)
Définit le nombre de FPS du jeu. Le nombre de FPS sera fixe, à condition que la boucle de jeu soit assez rapide pour tenir le rythme que vous choisissez.

#####void LaserGame::newFrame()
Si vous souhaitez laisser EdgeLaserPHP gérer vos FPS et que vous avez appelé précedemment setFramerate(), cette fonction doit être appelée en **début** de votre boucle de jeu, avant la première instruction.

#####void LaserGame::endFrame()
Si vous souhaitez laisser EdgeLaserPHP gérer vos FPS et que vous avez appelé précedemment setFramerate() et newFrame() en début de boucle de jeu, cette fonction doit être appelée en **fin** de votre boucle de jeu, elle doit donc être la toute dernière instruction. Cette fonction imposera la pause nécessaire au jeu pour atteindre le nombre de FPS demandés.

#####bool LaserGame::isStopped()
Permet de savoir si l'instance de jeu a été stoppée par le serveur. Dans le cadre d'un pause(), cette valeur n'est PAS mise à true car pause() est une décision client et non serveur.

#####LaserGame LaserGame::receiveServerCommands()
Permet de mettre à jour les requêtes serveur (ACK, STOP, GO). **Obligatoire**.

#####LaserGame LaserGame::addLine(int $x1, int $y1, int $x2, int $y2 [, int LaserColor::$color])
Trace une ligne selon les arguments donnés.

#####LaserGame LaserGame::addCircle(int $x, int $y, int $diameter [, int LaserColor::$color])
Trace un cercle selon les arguments donnés.

#####LaserGame LaserGame::addRectangle(int $x1, int $y1, int $x2, int $y2 [, int LaserColor::$color])
Trace un rectangle selon les arguments donnés.

#####LaserGame LaserGame::refresh()
Envoie l'instruction REFRESH au serveur.

#####LaserGame LaserGame::pause()
Envoie l'instruction client STOP au serveur.

#####LaserFont LaserFont::__construct(string $filename)#####
Charge la police ELFC au chemin d'accès donné et renvoie un pointeur vers cette police ce qui permettra de faire un render().

#####void LaserFont::render(LaserGame $ctx, string $text, int $x, int $y, int int LaserColor::$color, int $coeff)#####
Dessine le texte $text.
* $ctx : référence vers le jeu de type LaserGame
* $text : le texte à dessiner
* $x, $y : coordonnées où écrire le texte
* $color : une couleur de type LaserColor
* $coeff : taille du texte (coefficient multiplicateur)

#####array XboxKey::getKeys()
Renvoie un array de int contenant les touches actuellement pressées.
Les valeurs int de cet array peuvent être comparées aux constantes de la classe abstraite XboxKey, Cf. annexe des touches plus bas.

**Exemple :**
```php
foreach(XboxKey::getKeys() as $key)
{
	switch($key)
	{
		case XboxKey::P1_ARROW_UP : $p1posy -= 5; break;
		case XboxKey::P1_ARROW_LEFT : $p1posx -= 5; break;
		case XboxKey::P1_ARROW_DOWN : $p1posy += 5; break;
		case XboxKey::P1_ARROW_RIGHT : $p1posx += 5; break;
	}
}
```


####Annexe des couleurs
Liste des couleurs disponibles :
* `LaserColor::RED`
* `LaserColor::LIME`
* `LaserColor::GREEN` (alias LIME)
* `LaserColor::YELLOW`
* `LaserColor::BLUE`
* `LaserColor::FUCHSIA`
* `LaserColor::CYAN`
* `LaserColor::WHITE`

####Annexe des touches
Liste des couleurs disponibles :
**Note :** ici les 8 touches du joueur 1 sont représentées. Pour les touches du joueur 2, remplacer P1 par P2.

*Touches directionnelles*
* `XboxKey::P1_ARROW_UP`
* `XboxKey::P1_ARROW_LEFT`
* `XboxKey::P1_ARROW_DOWN`
* `XboxKey::P1_ARROW_RIGHT`

*Touches d'action*
* `XboxKey::P1_X`
* `XboxKey::P1_Y`
* `XboxKey::P1_A`
* `XboxKey::P1_B`

##EdgeLaserPython
EdgeLaserPython a été écrite par Yanncik Jost, organisateur du projet.
Cette librairie Python est basée sur la librairie PHP, vous y trouverez donc quasiment les mêmes fonctions, avec les mêmes arguments.

La librairie et le script d'exemple shapes.py sont inclus dans [samples](samples) mais vous pouvez peut-être trouver une version plus à jour sur le github de Y. Jost : [yanjost/edge-laser-python](https://github.com/yanjost/edge-laser-python).

##ELF fonts
Le format de fontes ELF (voir dans [samples/php/fonts](samples/php/fonts)) est un format conçu pour l'affichage de texte dans les jeux. Pour le moment, seules les librairies EdgeLaserPHP et EdgeLaserPython gèrent ce format.

###Format de base ELF
Les fontes sont d'abord écrites à la main dans un fichier .elf, chaque fonte ayant son fichier.

###Format compilé ELFC
Les fontes sont ensuite compilées via l'utilitaire Makefont : `./makefont.php fontname.elf`.
Les données y sont ensuite compressées en GZIP.

**Description du format :**
* La première ligne contient 1 octet représentant un nombre, qui est l'espacement entre caractères.
* Les lignes suivantes représentent un caractère.
* Chaque ligne de caractère commence par un octet, qui est le caractère ASCII que l'on veut décrire, n'importe lequel, sans ordre imposé dans les lignes.
* Après ce caractère ASCII commence la description des lignes de ce caractère.
* Chaque ligne est représentée par quatre octets : x1, y1, x2, y2.
* Chaque ligne est collée à l'autre.
* Par exemple, codons le caractère ASCII 7 : 0x37-0x1-0x1-0x6-0x1-0x6-0x1-0x5-0xA : nous avons l'octet avec le code ASCII du caractère 7, suivi de 8 octets, donc le caractère 7 est composé ici de deux lignes.
* Chaque ligne est séparée par un octet 0x0.
