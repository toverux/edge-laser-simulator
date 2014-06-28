<?php

	include('EdgeLaser.ns.php');

	use EdgeLaser\LaserGame;
	use EdgeLaser\LaserColor;
	use EdgeLaser\LaserFont;
	use EdgeLaser\XboxKey;

	$game = new LaserGame('FontSample');

	$game->setResolution(500)->setDefaultColor(LaserColor::LIME)->setFramerate(20);

	$font_lcd = new LaserFont('fonts/lcd.elfc');

	$x = 10;
	$y = 10;

	while(true)
	{
		$commands = $game->receiveServerCommands();

		if(!$game->isStopped())
		{
			$game->newFrame();

			$y = ($y < 450) ? $y += 4 : $y = 10;

			$font_lcd->render($game, 'EDGEFEST 2014', $x, $y, rand(1, 7), 3);

			$game->refresh();

			$game->endFrame();
		}
	}

?>
