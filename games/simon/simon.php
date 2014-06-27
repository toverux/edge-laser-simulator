<?php

/*
|
|| HANDYPRESS
|
| # Game dev for the EdgeFest Strasbourg
| @YannickArmspach
|
*/

include('../../samples/php/EdgeLaser.ns.php');

use EdgeLaser\LaserGame;
use EdgeLaser\LaserColor;
use EdgeLaser\XboxKey;

class HANDYGAME {

  public function __construct() {

		$this->game = new LaserGame('simon');
		$this->game->setResolution(500);
		$this->game->setDefaultColor(LaserColor::LIME);

		$this->init();

	}

	public function init() {


    //START SCREEN
    $this->timerColor_int = 30;
    $this->timerColor = $this->timerColor_int;

		$this->margin = 10;

		//LEVELS
		$this->levels = array(
			array("X","X","A","A"),
			array("X","B","B","B"),
			array("Y","B","Y","B"),
		);
		$this->levels_check = array(
			array(),
			array(),
			array(),
		);
		$this->levels_state = array(
			"uncomplete",
			"uncomplete",
			"uncomplete",
		);
		$this->screen = 'start';
    $this->levelChecking = true;
		$this->ListenKey = true;
		$this->levelCheck = 0;
		$this->currentLevel = 0;
		$this->levelStep = 0;
		$this->userTap = array();
		$this->LevelSucces = false;

		//TIMER
		$this->timerDelay = 10;
		$this->timer = $this->timerDelay;

		//PLAYER
		$this->player['name'] = "simon";
		$this->player['levels'][$this->currentLevel] = 'uncomplete';

    //GAME
		while ( true ) {

      $commands = $this->game->receiveServerCommands();

      if ( ! $this->game->isStopped() ) {

        $this->display($this->screen);

        $this->game->refresh();
        usleep(50000);

      }

    }


	}

  public function display($id){

    switch ($id) {

      case 'start':

        $this->menuControl();
        $this->startscreen();

      break;

      case 'level':

        //$this->menuControl();
        $this->levelscreen();

      break;

      case 'error':

        $this->errorControl();
        $this->errorcreen();

      break;

      case 'success':

        $this->successControl();
        $this->successcreen();

      break;

    }

  }

  public function startscreen(){

    //$this->writeSentence('SIMON LAZER',100,200,LaserColor::YELLOW,8);
    //$this->writeSentence('PRESS X TO PLAY',100,250,LaserColor::RED,4);

    //$this->writeSentence('BY YANNICK ARMSPACH',20,460,LaserColor::LIME,3);

    if ( $this->timerColor == $this->timerColor_int ) $this->timerColor = 0;

    $title_simon_color1 = LaserColor::BLUE;
    $title_simon_color2 = LaserColor::BLUE;
    $title_simon_color3 = LaserColor::BLUE;
    $title_simon_color4 = LaserColor::BLUE;
    $title_simon_color5 = LaserColor::BLUE;

    if ( $this->timerColor == 1 ) $title_simon_color5 = LaserColor::CYAN;
    if ( $this->timerColor == 2 ) $title_simon_color4 = LaserColor::CYAN;
    if ( $this->timerColor == 3 ) $title_simon_color3 = LaserColor::CYAN;
    if ( $this->timerColor == 4 ) $title_simon_color2 = LaserColor::CYAN;
    if ( $this->timerColor == 5 ) $title_simon_color1 = LaserColor::CYAN;

    // S
    $this->game->addLine(54,244,54,224,$title_simon_color1);
    $this->game->addLine(54,224,120,224,$title_simon_color1);
    $this->game->addLine(120,224,120,220,$title_simon_color1);
    $this->game->addLine(120,220,54,220,$title_simon_color1);
    $this->game->addLine(54,220,54,177,$title_simon_color1);
    $this->game->addLine(54,177,139,177,$title_simon_color1);
    $this->game->addLine(139,177,139,197,$title_simon_color1);
    $this->game->addLine(139,197,73,197,$title_simon_color1);
    $this->game->addLine(73,197,73,201,$title_simon_color1);
    $this->game->addLine(73,201,139,201,$title_simon_color1);
    $this->game->addLine(139,201,139,244,$title_simon_color1);
    $this->game->addLine(139,244,54,244,$title_simon_color1);
    // I
    $this->game->addLine(148,244,148,177,$title_simon_color2);
    $this->game->addLine(148,177,167,177,$title_simon_color2);
    $this->game->addLine(167,177,167,244,$title_simon_color2);
    $this->game->addLine(167,244,148,244,$title_simon_color2);
    // M
    $this->game->addLine(176,244,176,174,$title_simon_color3);
    $this->game->addLine(176,174,219,216,$title_simon_color3);
    $this->game->addLine(219,216,262,174,$title_simon_color3);
    $this->game->addLine(262,174,262,244,$title_simon_color3);
    $this->game->addLine(262,244,242,244,$title_simon_color3);
    $this->game->addLine(242,244,242,220,$title_simon_color3);
    $this->game->addLine(242,220,219,244,$title_simon_color3);
    $this->game->addLine(219,244,196,220,$title_simon_color3);
    $this->game->addLine(196,220,196,244,$title_simon_color3);
    $this->game->addLine(196,244,176,244,$title_simon_color3);
    // O
    $this->game->addLine(271,244,271,177,$title_simon_color4);
    $this->game->addLine(271,177,356,177,$title_simon_color4);
    $this->game->addLine(356,177,356,244,$title_simon_color4);
    $this->game->addLine(356,244,271,244,$title_simon_color4);
    $this->game->addLine(337,224,337,197,$title_simon_color4);
    $this->game->addLine(337,197,290,197,$title_simon_color4);
    $this->game->addLine(290,197,290,224,$title_simon_color4);
    $this->game->addLine(290,224,337,224,$title_simon_color4);
    // N
    $this->game->addLine(384,201,384,244,$title_simon_color5);
    $this->game->addLine(384,244,365,244,$title_simon_color5);
    $this->game->addLine(365,244,365,154,$title_simon_color5);
    $this->game->addLine(365,154,431,220,$title_simon_color5);
    $this->game->addLine(431,220,431,177,$title_simon_color5);
    $this->game->addLine(431,177,450,177,$title_simon_color5);
    $this->game->addLine(450,177,450,267,$title_simon_color5);
    $this->game->addLine(450,267,384,201,$title_simon_color5);


    //line
    $this->game->addLine(236,274,54,274,LaserColor::LIME);

    // L
    $this->game->addLine(251,285,251,261,LaserColor::LIME);
    $this->game->addLine(251,261,257,261,LaserColor::LIME);
    $this->game->addLine(257,261,257,278,LaserColor::LIME);
    $this->game->addLine(257,278,281,278,LaserColor::LIME);
    $this->game->addLine(281,278,281,285,LaserColor::LIME);
    $this->game->addLine(281,285,251,285,LaserColor::LIME);
    // A
    $this->game->addLine(285,285,316,254,LaserColor::LIME);
    $this->game->addLine(316,254,316,285,LaserColor::LIME);
    $this->game->addLine(316,285,309,285,LaserColor::LIME);
    $this->game->addLine(309,285,309,271,LaserColor::LIME);
    $this->game->addLine(309,271,295,285,LaserColor::LIME);
    $this->game->addLine(295,285,285,285,LaserColor::LIME);
    // Z
    $this->game->addLine(319,285,335,268,LaserColor::LIME);
    $this->game->addLine(335,268,320,268,LaserColor::LIME);
    $this->game->addLine(320,268,320,261,LaserColor::LIME);
    $this->game->addLine(320,261,352,261,LaserColor::LIME);
    $this->game->addLine(352,261,335,278,LaserColor::LIME);
    $this->game->addLine(335,278,350,278,LaserColor::LIME);
    $this->game->addLine(350,278,350,285,LaserColor::LIME);
    $this->game->addLine(350,285,319,285,LaserColor::LIME);
    //E
    $this->game->addLine(355,285,355,261,LaserColor::LIME);
    $this->game->addLine(355,261,385,261,LaserColor::LIME);
    $this->game->addLine(385,261,385,268,LaserColor::LIME);
    $this->game->addLine(385,268,362,268,LaserColor::LIME);
    $this->game->addLine(362,268,362,269,LaserColor::LIME);
    $this->game->addLine(362,269,385,269,LaserColor::LIME);
    $this->game->addLine(385,269,378,276,LaserColor::LIME);
    $this->game->addLine(378,276,362,276,LaserColor::LIME);
    $this->game->addLine(362,276,362,278,LaserColor::LIME);
    $this->game->addLine(362,278,385,278,LaserColor::LIME);
    $this->game->addLine(385,278,385,285,LaserColor::LIME);
    $this->game->addLine(385,285,355,285,LaserColor::LIME);
    //R
    $this->game->addLine(398,269,413,269,LaserColor::LIME);
    $this->game->addLine(413,269,413,268,LaserColor::LIME);
    $this->game->addLine(413,268,397,268,LaserColor::LIME);
    $this->game->addLine(397,268,397,285,LaserColor::LIME);
    $this->game->addLine(397,285,390,285,LaserColor::LIME);
    $this->game->addLine(390,285,390,261,LaserColor::LIME);
    $this->game->addLine(390,261,420,261,LaserColor::LIME);
    $this->game->addLine(420,261,420,276,LaserColor::LIME);
    $this->game->addLine(420,276,415,276,LaserColor::LIME);
    $this->game->addLine(415,276,421,283,LaserColor::LIME);
    $this->game->addLine(421,283,421,293,LaserColor::LIME);
    $this->game->addLine(421,293,398,269,LaserColor::LIME);

    // X (START)
    if ( $this->timerColor < 20 ) $XbtSize = 5;
    if ( $this->timerColor == 20 ) $XbtSize = 4;
    if ( $this->timerColor == 25 ) $XbtSize = 3;
    if ( $this->timerColor == 30 ) $XbtSize = 2;

    $this->game->addLine(230-$XbtSize,390-$XbtSize,271+$XbtSize,431+$XbtSize,LaserColor::BLUE);
    $this->game->addLine(229-$XbtSize,430+$XbtSize,271+$XbtSize,390-$XbtSize,LaserColor::BLUE);
    $this->game->addCircle(250,410,88+$XbtSize+$XbtSize,LaserColor::BLUE);

    $this->timerColor++;

  }

  public function successcreen(){

    if ( count($this->levels) == $this->currentLevel+1 ) {

      $this->writeSentence('BRAVO',100,200,LaserColor::GREEN,8);
      $this->writeSentence('YOU BEAT SIMON',100,250,LaserColor::GREEN,4);

    } else {

      $this->writeSentence('SUCCESS',100,200,LaserColor::GREEN,8);
      $this->writeSentence('PRESS X TO PLAY NEXT LEVEL',100,250,LaserColor::RED,4);

    }

  }

  public function errorcreen(){

    $this->writeSentence('ERROR',100,200,LaserColor::RED,8);

  }



  public function levelscreen(){

    if ( count($this->levels[$this->currentLevel]) == count($this->levels_check[$this->currentLevel]) ) {

      $this->screen = 'success';

    }

    if ( $this->levels_state[$this->currentLevel] == 'uncomplete' ){

      $this->playLevel();

    } else {

      $this->recordPress();

    }

    /*
    $this->game->addRectangle(1, 1, 249, 249, LaserColor::YELLOW);
    $this->game->addRectangle(251, 1, 499, 249, LaserColor::RED);
    $this->game->addRectangle(1, 251, 249, 499, LaserColor::BLUE);
    $this->game->addRectangle(251, 251, 499, 499, LaserColor::GREEN);
    */

    $this->game->addLine(250,9,364,125,LaserColor::YELLOW);
    $this->game->addLine(364,125,287,202,LaserColor::YELLOW);
    $this->game->addLine(287,202,250,165,LaserColor::YELLOW);
    $this->game->addLine(250,165,212,202,LaserColor::YELLOW);
    $this->game->addLine(212,202,135,124,LaserColor::YELLOW);
    $this->game->addLine(135,124,250,9,LaserColor::YELLOW);

    $this->game->addLine(490,250,374,364,LaserColor::RED);
    $this->game->addLine(374,364,297,287,LaserColor::RED);
    $this->game->addLine(297,287,335,250,LaserColor::RED);
    $this->game->addLine(335,250,297,212,LaserColor::RED);
    $this->game->addLine(297,212,375,134,LaserColor::RED);
    $this->game->addLine(375,134,490,250,LaserColor::RED);

    $this->game->addLine(250,489,135,374,LaserColor::GREEN);
    $this->game->addLine(135,374,212,297,LaserColor::GREEN);
    $this->game->addLine(212,297,250,335,LaserColor::GREEN);
    $this->game->addLine(250,335,287,297,LaserColor::GREEN);
    $this->game->addLine(287,297,365,375,LaserColor::GREEN);
    $this->game->addLine(365,375,250,489,LaserColor::GREEN);

    $this->game->addLine(10,250,125,135,LaserColor::BLUE);
    $this->game->addLine(125,135,202,212,LaserColor::BLUE);
    $this->game->addLine(202,212,164,250,LaserColor::BLUE);
    $this->game->addLine(164,250,202,287,LaserColor::BLUE);
    $this->game->addLine(202,287,124,365,LaserColor::BLUE);
    $this->game->addLine(124,365,10,250,LaserColor::BLUE);

  }



	public function playLevel(){

		if ( $this->levelStep < count( $this->levels[$this->currentLevel] ) ) {

			if ( $this->timer && $this->levels[$this->currentLevel][$this->levelStep] )  {

				$pressed_keys = explode(',',$this->levels[$this->currentLevel][$this->levelStep]);

				foreach ($pressed_keys as $key => $pressKey) {
					$this->PRESS( $pressKey );
				}

				$this->timer--;

			} else {

				$this->timer = $this->timerDelay;
				$this->levelStep++;

			}

		} else {

			$this->levelStep = 0;
			$this->levels_state[$this->currentLevel] = 'complete';

		}

	}

	public function menuControl(){

   $keyListener = XboxKey::getKeys();

   switch( $keyListener[0] ) {

     case XboxKey::P1_X :

      $this->screen = 'level';

     break;

     case XboxKey::P1_ARROW_RIGHT :

     break;

     case XboxKey::P1_ARROW_LEFT :

       $this->screen = 'start';

     break;

   }

  }

  public function successControl(){

   $keyListener = XboxKey::getKeys();

   switch( $keyListener[0] ) {

     case XboxKey::P1_X :

      $this->screen = 'level';
      $this->currentLevel++;

     break;

     case XboxKey::P1_ARROW_LEFT :

       $this->screen = 'start';

     break;

   }

  }

  public function errorControl(){

   $keyListener = XboxKey::getKeys();

   switch( $keyListener[0] ) {

     case XboxKey::P1_X :

      $this->screen = 'level';

     break;

     case XboxKey::P1_ARROW_LEFT :

       $this->screen = 'start';

     break;

   }

  }

  public function recordPress(){

		$keyListener = XboxKey::getKeys();

		switch( $keyListener[0] ) {

			case XboxKey::P1_Y :

				$this->PRESS('Y');
				$this->checkPRESS('Y');

			break;

			case XboxKey::P1_B :

				$this->PRESS('B');
				$this->checkPRESS('B');

			break;

			case XboxKey::P1_X :

				$this->PRESS('X');
				$this->checkPRESS('X');

			break;

			case XboxKey::P1_A :

				$this->PRESS('A');
				$this->checkPRESS('A');

			break;

			case XboxKey::P1_ARROW_RIGHT :

				//$this->currentLevel++;

			break;

      case XboxKey::P1_ARROW_LEFT :

        $this->screen = 'start';

      break;

		}

	}

	public function checkPRESS($key){

		if ( $this->levels[$this->currentLevel][$this->levelCheck] == $key ) {

			$this->levels_check[$this->currentLevel][$this->levelCheck] = $key;

			$this->levelCheck++;

		} else{

			$this->levelCheck = 0;
			$this->levels_check[$this->currentLevel] = array();
			$this->levels_state[$this->currentLevel] = 'uncomplete';

		}

	}

	public function PRESS($key) {

		switch ( $key ) {

			case 'Y':
				$x1 = 0;
				$y1 = 0;
				$x2 = 250;
				$y2 = 0;
				$color = LaserColor::YELLOW;
			break;

			case 'B':
				$x1 = 250;
				$y1 = 0;
				$x2 = 500;
				$y2 = 0;
				$color = LaserColor::RED;
			break;

			case 'X':
				$x1 = 0;
				$y1 = 250;
				$x2 = 250;
				$y2 = 250;
				$color = LaserColor::BLUE;
			break;

			case 'A':
				$x1 = 250;
				$y1 = 250;
				$x2 = 500;
				$y2 = 250;
				$color = LaserColor::GREEN;
			break;

		}

		$this->game
		->addLine($x1+$this->margin, $y1+10, $x2-$this->margin, $y2+10, $color)
		->addLine($x1+$this->margin, $y1+20, $x2-$this->margin, $y2+20, $color)
		->addLine($x1+$this->margin, $y1+30, $x2-$this->margin, $y2+30, $color)
		->addLine($x1+$this->margin, $y1+40, $x2-$this->margin, $y2+40, $color)
		->addLine($x1+$this->margin, $y1+50, $x2-$this->margin, $y2+50, $color)
		->addLine($x1+$this->margin, $y1+60, $x2-$this->margin, $y2+60, $color)
		->addLine($x1+$this->margin, $y1+70, $x2-$this->margin, $y2+70, $color)
		->addLine($x1+$this->margin, $y1+80, $x2-$this->margin, $y2+80, $color)
		->addLine($x1+$this->margin, $y1+90, $x2-$this->margin, $y2+90, $color)
		->addLine($x1+$this->margin, $y1+100, $x2-$this->margin, $y2+100, $color)
		->addLine($x1+$this->margin, $y1+110, $x2-$this->margin, $y2+110, $color)
		->addLine($x1+$this->margin, $y1+120, $x2-$this->margin, $y2+120, $color)
		->addLine($x1+$this->margin, $y1+130, $x2-$this->margin, $y2+130, $color)
		->addLine($x1+$this->margin, $y1+140, $x2-$this->margin, $y2+140, $color)
		->addLine($x1+$this->margin, $y1+150, $x2-$this->margin, $y2+150, $color)
		->addLine($x1+$this->margin, $y1+160, $x2-$this->margin, $y2+160, $color)
		->addLine($x1+$this->margin, $y1+170, $x2-$this->margin, $y2+170, $color)
		->addLine($x1+$this->margin, $y1+180, $x2-$this->margin, $y2+180, $color)
		->addLine($x1+$this->margin, $y1+190, $x2-$this->margin, $y2+190, $color)
		->addLine($x1+$this->margin, $y1+200, $x2-$this->margin, $y2+200, $color)
		->addLine($x1+$this->margin, $y1+210, $x2-$this->margin, $y2+210, $color)
		->addLine($x1+$this->margin, $y1+220, $x2-$this->margin, $y2+220, $color)
		->addLine($x1+$this->margin, $y1+230, $x2-$this->margin, $y2+230, $color)
		->addLine($x1+$this->margin, $y1+240, $x2-$this->margin, $y2+240, $color);

	}

  public function writeSentence($str,$str_x,$str_y,$color,$size) {

    $char_arr = str_split($str);

    foreach ($char_arr as $key => $char) {

      switch ($char) {

        case 'X':

        break;

      }

      $this->writeChar($char,$str_x+((4*$size)*$key),$str_y,$color,$size);

    }

  }

  public function writeChar($char,$posx,$posy,$color,$size) {

    switch ($char) {

      case 'A':

        $this->game
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(4*$size), $posy+(4*$size), $color)
        ->addLine($posx+(1*$size), $posy+(2*$size), $posx+(3*$size), $posy+(2*$size), $color);

      break;

      case 'B':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(2*$size), $posy+(2*$size), $color)
        ->addLine($posx+(0*$size), $posy+(2*$size), $posx+(3*$size), $posy+(2*$size), $color)
        ->addLine($posx+(3*$size), $posy+(2*$size), $posx+(3*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(3*$size), $posy+(4*$size), $color);

      break;

      case 'C':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(2*$size), $posy+(4*$size), $color);

      break;

      case 'D':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(3*$size), $posy+(1*$size), $color)
        ->addLine($posx+(3*$size), $posy+(1*$size), $posx+(3*$size), $posy+(3*$size), $color)
        ->addLine($posx+(3*$size), $posy+(3*$size), $posx+(2*$size), $posy+(4*$size), $color)
        ->addLine($posx+(2*$size), $posy+(4*$size), $posx+(0*$size), $posy+(4*$size), $color);

      break;

      case 'E':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(3*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(3*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(2*$size), $posx+(2*$size), $posy+(2*$size), $color);

      break;

      case 'F':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(3*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(2*$size), $posx+(2*$size), $posy+(2*$size), $color);

      break;

      case 'G':

        $this->game
        ->addLine($posx+(3*$size), $posy+(0*$size), $posx+(0*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(2*$size), $posy+(4*$size), $color)
        ->addLine($posx+(2*$size), $posy+(4*$size), $posx+(3*$size), $posy+(3*$size), $color)
        ->addLine($posx+(3*$size), $posy+(3*$size), $posx+(3*$size), $posy+(2*$size), $color)
        ->addLine($posx+(3*$size), $posy+(2*$size), $posx+(2*$size), $posy+(2*$size), $color);

      break;

      case 'H':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(2*$size), $posx+(3*$size), $posy+(2*$size), $color)
        ->addLine($posx+(3*$size), $posy+(0*$size), $posx+(3*$size), $posy+(4*$size), $color);

      break;

      case 'I':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(1*$size), $posy+(0*$size), $posx+(1*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(2*$size), $posy+(4*$size), $color);

      break;

      case 'J':

        $this->game
        ->addLine($posx+(1*$size), $posy+(0*$size), $posx+(3*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(2*$size), $posy+(4*$size), $color)
        ->addLine($posx+(2*$size), $posy+(4*$size), $posx+(0*$size), $posy+(4*$size), $color);

      break;

      case 'K':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(2*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(2*$size), $posx+(2*$size), $posy+(4*$size), $color);

      break;

      case 'L':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(2*$size), $posy+(4*$size), $color);

      break;

      case 'M':

        $this->game
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(0*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(2*$size), $color)
        ->addLine($posx+(2*$size), $posy+(2*$size), $posx+(4*$size), $posy+(0*$size), $color)
        ->addLine($posx+(4*$size), $posy+(0*$size), $posx+(4*$size), $posy+(4*$size), $color);

      break;

      case 'N':

        $this->game
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(0*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(4*$size), $color)
        ->addLine($posx+(2*$size), $posy+(4*$size), $posx+(2*$size), $posy+(0*$size), $color);

      break;

      case 'O':

        $this->game
        ->addLine($posx+(1*$size), $posy+(0*$size), $posx+(0*$size), $posy+(1*$size), $color)
        ->addLine($posx+(0*$size), $posy+(1*$size), $posx+(0*$size), $posy+(3*$size), $color)
        ->addLine($posx+(0*$size), $posy+(3*$size), $posx+(1*$size), $posy+(4*$size), $color)
        ->addLine($posx+(1*$size), $posy+(4*$size), $posx+(2*$size), $posy+(4*$size), $color)
        ->addLine($posx+(2*$size), $posy+(4*$size), $posx+(3*$size), $posy+(3*$size), $color)
        ->addLine($posx+(3*$size), $posy+(3*$size), $posx+(3*$size), $posy+(1*$size), $color)
        ->addLine($posx+(3*$size), $posy+(1*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(1*$size), $posy+(0*$size), $color);

      break;

      case 'P':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(2*$size), $posy+(2*$size), $color)
        ->addLine($posx+(2*$size), $posy+(2*$size), $posx+(0*$size), $posy+(2*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color);

      break;

      case 'Q':

        $this->game
        ->addLine($posx+(1*$size), $posy+(0*$size), $posx+(0*$size), $posy+(1*$size), $color)
        ->addLine($posx+(0*$size), $posy+(1*$size), $posx+(0*$size), $posy+(3*$size), $color)
        ->addLine($posx+(0*$size), $posy+(3*$size), $posx+(1*$size), $posy+(4*$size), $color)
        ->addLine($posx+(1*$size), $posy+(4*$size), $posx+(2*$size), $posy+(4*$size), $color)
        ->addLine($posx+(2*$size), $posy+(4*$size), $posx+(3*$size), $posy+(3*$size), $color)
        ->addLine($posx+(3*$size), $posy+(3*$size), $posx+(3*$size), $posy+(1*$size), $color)
        ->addLine($posx+(3*$size), $posy+(1*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(1*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(3*$size), $posx+(3*$size), $posy+(4*$size), $color);

      break;

      case 'R':

        $this->game
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(0*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(2*$size), $posy+(2*$size), $color)
        ->addLine($posx+(2*$size), $posy+(2*$size), $posx+(0*$size), $posy+(2*$size), $color)
        ->addLine($posx+(1*$size), $posy+(2*$size), $posx+(2*$size), $posy+(4*$size), $color);

      break;

      case 'S':

        $this->game
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(0*$size), $posy+(0*$size), $color)
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(2*$size), $color)
        ->addLine($posx+(0*$size), $posy+(2*$size), $posx+(2*$size), $posy+(2*$size), $color)
        ->addLine($posx+(2*$size), $posy+(2*$size), $posx+(2*$size), $posy+(4*$size), $color)
        ->addLine($posx+(2*$size), $posy+(4*$size), $posx+(0*$size), $posy+(4*$size), $color);

      break;

      case 'T':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(1*$size), $posy+(0*$size), $posx+(1*$size), $posy+(4*$size), $color);

      break;

      case 'U':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(3*$size), $posy+(4*$size), $color)
        ->addLine($posx+(3*$size), $posy+(4*$size), $posx+(3*$size), $posy+(0*$size), $color);

      break;

      case 'V':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(1*$size), $posy+(4*$size), $color)
        ->addLine($posx+(1*$size), $posy+(4*$size), $posx+(2*$size), $posy+(0*$size), $color);

      break;

      case 'W':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(1*$size), $posy+(4*$size), $color)
        ->addLine($posx+(1*$size), $posy+(4*$size), $posx+(2*$size), $posy+(1*$size), $color)
        ->addLine($posx+(2*$size), $posy+(1*$size), $posx+(3*$size), $posy+(4*$size), $color)
        ->addLine($posx+(3*$size), $posy+(4*$size), $posx+(4*$size), $posy+(0*$size), $color);

      break;

      case 'X':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(4*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(4*$size), $posy+(0*$size), $color);

      break;

      case 'Y':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(1*$size), $posy+(2*$size), $color)
        ->addLine($posx+(1*$size), $posy+(2*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(1*$size), $posy+(2*$size), $posx+(1*$size), $posy+(4*$size), $color);

      break;

      case 'Z':

        $this->game
        ->addLine($posx+(0*$size), $posy+(0*$size), $posx+(2*$size), $posy+(0*$size), $color)
        ->addLine($posx+(2*$size), $posy+(0*$size), $posx+(0*$size), $posy+(4*$size), $color)
        ->addLine($posx+(0*$size), $posy+(4*$size), $posx+(2*$size), $posy+(4*$size), $color);

      break;

    }

  }



}

$HANDYGAME = new HANDYGAME();

?>
