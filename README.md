edge-laser-simulator
====================

A NodeJS laser simulator for AlsaceDigitale/edgefest-hacking

##Remarques
Ceci est un serveur de test pour commencer à coder votre jeu pour l'"Edge Laser".
Ce repo contient le kit de développement nécessaire, à savoir :
* Serveur Node.js
* Visualisateur en websocket (via votre navigateur Web, Chromium/Chrome testé uniquement)
* Scripts d'exemples (_faites des pull requests, tous langages !_)

**Attention.** Le protocole est entièrement implémenté au niveau fonctionnel, et ceci dans la version du protocole indiquée dans index.html. Néanmoins, toutes les vérifications de format des requeêtes client ne sont pas encore effectuées. Une requête valide mais avec des arguments en trop fonctionnera, mais des dépassements de buffer auront lieu sur des requêtes mal formées et trop courtes.

##Installation
* Clonez ou téléchargez ce repo en local
* Installez Node.js dernière version
* (Optionnel) PHP-Cli pour tester les exemples en PHP et/ou faire un jeu en PHP : `apt-get install php5-cli` sous Debian-like.
* C'est bon !

##Run the sauce
* `node main.js` dans edge-laser-simulator/node
* Ouvrez index.html dans un navigateur (dans la pseudo-console doit-être affiché _Socket is ready_)
* (Exemple) `php shapes.php` dans edge-laser-simulator/samples/php

Dans votre navigateur, la liste des clients a du être mise à jour. Et par exemple, si vous lancez plusieurs fois le script PHP dans des consoles différentes, la liste contiendra plusieurs fois le même jeu. Vous êtes alors habilité à changer de jeu à la volonté.
Changer de jeu impliquera l'envoi de la commande STOP au jeu en cours et l'envoi de la commande GO au jeu visé.

##EdgeLaserPHP
EdgeLaserPHP est une petite librairie contenue dans le fichier edge-laser-simulator/samples/php/EdgeLaser.ns.php
Elle permet de se libérer de la couche réseau et du protocole lors du développement d'un jeu en PHP pour l'"Edge Laser".

####Include the sauce
```php
	include('EdgeLaser.ns.php');

	use EdgeLaser\LaserGame;
	use EdgeLaser\LaserColor;
```

####Créer un nouveau jeu
```php
	$game = new LaserGame('SuperTetris');
	$game->setResolution(500)->setDefaultColor(LaserColor::LIME);
```

* `setResolution` **est obligatoire** et va définir une résolution virtuelle (la résolution finale étant toujours de 65535*65535). Cela permet au développeur de ne pas travailler avec des valeurs inhabituelles deplusieurs dizaines de milliers de pixels. A l'écran, le rendu sera le même pour n'importe quelle résolution virtuelle.
* `setDefaultColor` **est facultatif** et permet d'appliquer une couleur de base aux objets ajoutés plus tard qui n'auraient pas de couleur renseignée.

####Ingame
Le code de base d'une code de jeu sous EdgeLaserPHP est la suivante :

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
#####LaserGame setResolution(int $resolutionXY)
Définit la résolution virtuelle pour cette instance de jeu

#####LaserGame setDefaultColor(LaserColor $color)
Définit la couleur par défaut des formes (cf. référence des couleurs)

#####bool isStopped()
Permet de savoir si l'instance de jeu a été stoppée par le serveur. Dans le cadre d'un pause(), cette valeur n'est PAS mise à true car pause() est une décision client et non serveur.

#####LaserGame receiveServerCommands()
Permet de mettre à jour les requêtes serveur (ACK, STOP, GO). **Obligatoire**.

#####LaserGame addLine(int $x1, int $y1, int $x2, int $y2 [, LaserColor $color])
Trace une ligne selon les arguments donnés.

#####LaserGame addCircle(int $x, int $y, int $diameter [, LaserColor $color])
Trace un cercle selon les arguments donnés.

#####LaserGame addRectangle(int $x1, int $y1, int $x2, int $y2 [, LaserColor $color])
Trace un rectangle selon les arguments donnés.

#####LaserGame refresh()
Envoie l'instruction REFRESH au serveur.

#####LaserGame pause()
Envoie l'instruction client STOP au serveur.

####Annexe des couleurs
Liste des couleurs disponibles :
* `LaserColor::RED`
* `LaserColor::LIME`
* `LaserColor::GREEN (alias LIME)`
* `LaserColor::YELLOW`
* `LaserColor::BLUE`
* `LaserColor::FUCHSIA`
* `LaserColor::CYAN`
* `LaserColor::WHITE`
