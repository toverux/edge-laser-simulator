<?php

	include('EdgeLaser.ns.php');

	use EdgeLaser\LaserGame;
	use EdgeLaser\LaserColor;

	$game = new LaserGame('SuperTetris');

	$game->setResolution(500)->setDefaultColor(LaserColor::LIME);

	$coeff = 0;

	$posx=450;
	$posy=450;

	while($game->isStopped())
	{
		$game->receiveServerCommands();
	}

	while(!$game->isStopped())
	{
		$commands = $game->receiveServerCommands();

		foreach($commands as $cmd)
		{
			switch($cmd->key)
			{
				case '1' : $posy-=5; break;
				case '2' : $posx-=5; break;
				case '3' : $posy+=5; break;
				case '4' : $posx+=5; break;
			}
		}

		$game->addRectangle($posx, $posy, $posx+10, $posy+10, LaserColor::RED);


		$coeff = $coeff > 499 ? 0 : $coeff+4;

		$game
			->addLine(250, 0, $coeff, 250, LaserColor::CYAN)    //'Color' argument is
			->addLine(250, 500, $coeff, 250, LaserColor::CYAN)  //facultative for ALL objects
			->addCircle(250, 250, $coeff, LaserColor::FUCHSIA)  //(default LIME)
			->addRectangle(10, 10, $coeff, $coeff)
			->refresh();

			usleep(50000); //Some calculations

	}

?>
