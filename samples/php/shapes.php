<?php

	include('EdgeLaser.ns.php');

	use EdgeLaser\LaserGame;
	use EdgeLaser\LaserColor;

	$game = new LaserGame('SuperTetris');

	$game->setResolution(500)->setDefaultColor(LaserColor::LIME);

	$coeff = 0;

	while(true)
	{
		$game->receiveServerCommands();

		if(!$game->isStopped())
		{
			$coeff = $coeff > 499 ? 0 : $coeff+4;
			usleep(50000); //Some calculations

			$game
				->addLine(250, 0, $coeff, 250, LaserColor::CYAN)    //'Color' argument is
				->addLine(250, 500, $coeff, 250, LaserColor::CYAN)  //facultative for ALL objects
				->addCircle(250, 250, $coeff, LaserColor::FUCHSIA)  //(default LIME)
				->addRectangle(10, 10, $coeff, $coeff)
				->refresh();
		}
	}

?>