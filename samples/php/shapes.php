<?php

	include('EdgeLaser.ns.php');

	use EdgeLaser\LaserGame;
	use EdgeLaser\LaserColor;
	use EdgeLaser\XboxKey;

	$game = new LaserGame('SuperTetris');

	$game->setResolution(500)->setDefaultColor(LaserColor::LIME);

	//Testing variables
	$coeff = 0;
	$p1posx = 450;
	$p1posy = 450;
	$p2posx = 480;
	$p2posy = 450;

	while($game->isStopped())
	{
		$game->receiveServerCommands();
	}

	while(true)
	{
		$commands = $game->receiveServerCommands();

		if(!$game->isStopped())
		{
			foreach(XboxKey::getKeys() as $key)
			{
				switch($key)
				{
					case XboxKey::P1_ARROW_UP : $p1posy -= 5; break;
					case XboxKey::P1_ARROW_LEFT : $p1posx -= 5; break;
					case XboxKey::P1_ARROW_DOWN : $p1posy += 5; break;
					case XboxKey::P1_ARROW_RIGHT : $p1posx += 5; break;

					case XboxKey::P2_ARROW_UP : $p2posy -= 5; break;
					case XboxKey::P2_ARROW_LEFT : $p2posx -= 5; break;
					case XboxKey::P2_ARROW_DOWN : $p2posy += 5; break;
					case XboxKey::P2_ARROW_RIGHT : $p2posx += 5; break;
				}
			}

			$game
				->addRectangle($p1posx, $p1posy, $p1posx+10, $p1posy+10, LaserColor::RED)     //Player 1
				->addRectangle($p2posx, $p2posy, $p2posx+10, $p2posy+10, LaserColor::YELLOW); //Player 2


			$coeff = $coeff > 499 ? 0 : $coeff+4;

			$game
				->addLine(250, 0, $coeff, 250, LaserColor::CYAN)    //'Color' argument is
				->addLine(250, 500, $coeff, 250, LaserColor::CYAN)  //facultative for ALL objects
				->addCircle(250, 250, $coeff, LaserColor::FUCHSIA)  //(default LIME)
				->addRectangle(10, 10, $coeff, $coeff)
				->refresh();

				usleep(50000); //Some calculations
		}
	}

?>
