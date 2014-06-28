<?php

include('../../samples/php/EdgeLaser.ns.php');

use EdgeLaser\LaserGame;
use EdgeLaser\LaserColor;
use EdgeLaser\XboxKey;

/*
*
* ---------
* S I M O N  =>  EdgeFest 2014
* ----lazer
*
* @desc : Lazer game for the EdgeFest @Strasbourg
* @author : Yannick Armspach (yannick.armspach@gmail.com)
* @version : 0.1
*
*/
class SIMONLAZER {


  /*
  *
  * ---------
  * CONSTRUCT
  * ---------
  *
  */
  public function __construct() {

    //set lazer
		$this->game = new LaserGame('simon');
		$this->game->setResolution(500);
		$this->game->setDefaultColor(LaserColor::LIME);

    //set graphic
    $this->setGraphics();

    //set first screen
    $this->screen = 'start';

    //init start screen
    $this->startInit();

    //run game
		$this->run();

	}


  /*
  *
  * ----
  * RUN => init game var
  * ----
  *
  */
	public function run() {

  		/*
      | Level screen
      */
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
      $this->levelInit();

      /*
      | Use
      */
      $this->margin = 10;


    /*
    |
    | Init Game
    |
    */
		while ( true ) {

      $commands = $this->game->receiveServerCommands();

      if ( ! $this->game->isStopped() ) {

        $this->display();

        $this->game->refresh();
        usleep(50000);

      }

    }


	}


  /*
  *
  * -------
  * DISPLAY => init and run screen
  * -------
  *
  * $id : string : start,level,error,success
  *
  */
  public function display(){

    /*
    | Execute current screen
    */
    switch ($this->screen) {

      case 'start':

        //$this->levelInit();

        $this->menuControl();
        $this->startScreen();

      break;

      case 'level':

        $this->startInit();

        //$this->menuControl();
        $this->levelScreen();

      break;

      case 'error':

        $this->errorControl();
        $this->errorrScreen();

      break;

      case 'success':

        $this->successControl();
        $this->succesScreen();

      break;

    }


  }

  /*
  *
  * ------------
  * START INIT  => init screen
  * ------------
  *
  */
  public function startInit(){

    $this->startScreen_int_loop = 35;
    $this->startScreen_anim_init = true;
    $this->timerColor = 0;

  }


  /*
  *
  * ------------
  * START SCREEN  => screen loop
  * ------------
  *
  */
  public function startScreen(){

    if ( $this->timerColor == $this->startScreen_int_loop ) $this->timerColor = 0;

    //LOGO
    //reflect fx
    $this->graphic_LOGO[4][color] = LaserColor::BLUE;
    $this->graphic_LOGO[3][color] = LaserColor::BLUE;
    $this->graphic_LOGO[2][color] = LaserColor::BLUE;
    $this->graphic_LOGO[1][color] = LaserColor::BLUE;
    $this->graphic_LOGO[0][color] = LaserColor::BLUE;
    if ( $this->timerColor == 13 ) $this->graphic_LOGO[4][color] = LaserColor::CYAN;
    if ( $this->timerColor == 14 ) $this->graphic_LOGO[3][color] = LaserColor::CYAN;
    if ( $this->timerColor == 15 ) $this->graphic_LOGO[2][color] = LaserColor::CYAN;
    if ( $this->timerColor == 16 ) $this->graphic_LOGO[1][color] = LaserColor::CYAN;
    if ( $this->timerColor == 17 ) $this->graphic_LOGO[0][color] = LaserColor::CYAN;

    //slide In
    if ( $this->startScreen_anim_init == true ){
      if ( $this->timerColor == 1 ) $this->drawShape(50,0,1,$this->graphic_LOGO);
      if ( $this->timerColor == 2 ) $this->drawShape(50,20,1,$this->graphic_LOGO);
      if ( $this->timerColor == 3 ) $this->drawShape(50,40,1,$this->graphic_LOGO);
      if ( $this->timerColor == 4 ) $this->drawShape(50,80,1,$this->graphic_LOGO);
      if ( $this->timerColor == 5 ) $this->drawShape(50,100,1,$this->graphic_LOGO);
      if ( $this->timerColor == 5 ) $this->startScreen_anim_init = false;
    } else {
      $this->drawShape(50,100,1,$this->graphic_LOGO);
    }

    //BT X
    if ( $this->startScreen_anim_init == true ){
      if ( $this->timerColor == 1 ) $this->drawShape(250,500,1.2,$this->graphic_BT_X);
      if ( $this->timerColor == 2 ) $this->drawShape(250,410,1.2,$this->graphic_BT_X);
      if ( $this->timerColor == 3 ) $this->drawShape(250,390,1.2,$this->graphic_BT_X);
      if ( $this->timerColor == 4 ) $this->drawShape(250,370,1.2,$this->graphic_BT_X);
      if ( $this->timerColor == 5 ) $this->drawShape(250,350,1.2,$this->graphic_BT_X);
      if ( $this->timerColor == 5 ) $this->startScreen_anim_init = false;
    } else {
      if ( $this->timerColor <= 5 ) $this->drawShape(250,350,1.2,$this->graphic_BT_X);
      if ( $this->timerColor == 6 ) $this->drawShape(250,350,1.1,$this->graphic_BT_X);
      if ( $this->timerColor == 7 ) $this->drawShape(250,350,1,$this->graphic_BT_X);
      if ( $this->timerColor == 8 ) $this->drawShape(250,350,1.1,$this->graphic_BT_X);
      if ( $this->timerColor == 9 ) $this->drawShape(250,350,1.2,$this->graphic_BT_X);
      if ( $this->timerColor == 10 ) $this->drawShape(250,350,1.1,$this->graphic_BT_X);
      if ( $this->timerColor == 11 ) $this->drawShape(250,350,1,$this->graphic_BT_X);
      if ( $this->timerColor == 12 ) $this->drawShape(250,350,1.1,$this->graphic_BT_X);
      if ( $this->timerColor >= 13 ) $this->drawShape(250,350,1.2,$this->graphic_BT_X);
    }

    $this->timerColor++;

  }


  /*
  *
  * --------------
  * SUCCESS SCREEN  => screen loop
  * --------------
  *
  */
  public function succesScreen(){

    if ( count($this->levels) == $this->currentLevel+1 ) {

      $this->drawText('BOOOOM|you|beat|simon',100,100,LaserColor::BLUE,1);

    } else {

      $this->drawText('GREAT|you|beat|simon',50,50,LaserColor::BLUE,1);

      $this->drawShape(400,400,1.2,$this->graphic_BT_X);

    }

  }


  /*
  *
  * ------------
  * ERROR SCREEN  => screen loop
  * ------------
  *
  */
  public function errorrScreen(){

    //$this->drawText('ERROR',100,200,LaserColor::RED,8);

  }


  /*
  *
  * ------------
  * LEVEL INIT  => init screen
  * ------------
  *
  */
  public function levelInit(){



  }

  /*
  *
  * ------------
  * LEVEL SCREEN  => screen loop
  * ------------
  *
  */
  public function levelScreen(){

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


  /*
  *
  * ----------
  * LEVEL PLAY  => next level if complete
  * ----------
  *
  */
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


  /*
  *
  * ------------
  * CONTROL MENU  => ...
  * ------------
  *
  */
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


  /*
  *
  * ---------------
  * CONTROL SUCCESS  => ...
  * ---------------
  *
  */
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


  /*
  *
  * ---------------
  * CONTROL XXXXXXX  => ...
  * ---------------
  *
  */
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


  /*
  *
  * ---------------
  * XXXXXXX  => ...
  * ---------------
  *
  */
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


  /*
  *
  * ---------------
  * XXXXXXX  => ...
  * ---------------
  *
  */
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


  /*
  *
  * ---------------
  * XXXXXXX  => ...
  * ---------------
  *
  */
	public function PRESS($key) {

		switch ( $key ) {

			case 'X':

        $this->drawShape(125,250,1,$this->graphic_SIMON_PRESS);

			break;

			case 'B':

			  $this->drawShape(375,250,1,$this->graphic_SIMON_PRESS);

      break;

			case 'Y':

        $this->drawShape(250,175,1,$this->graphic_SIMON_PRESS);

			break;

			case 'A':

        $this->drawShape(250,375,1,$this->graphic_SIMON_PRESS);

			break;

		}



	}

  /*
  *
  * ----------
  * DRAW SHAPE
  * ----------
  *
  */
  public function drawShape($x,$y,$zoom,$items){

    foreach ( $items as $item ) {

      $color = $item['color'];
      $type = $item['type'];
      $origin = $item['origin'];

      $bigX = 0;
      $bigY = 0;
      foreach ( $item['coord'] as $key => $coords ) {

          if ( $bigX < $coords[0] ) $bigX = $coords[0]*$zoom;
          if ( $bigY1 < $coords[1] ) $bigY = $coords[1]*$zoom;
          if ( $bigY < $coords[2] ) $bigX = $coords[2]*$zoom;
          if ( $bigY < $coords[3] ) $bigY = $coords[3]*$zoom;

      }

      foreach ( $item['coord'] as $key => $coords  ) {

        $x1 = $coords[0]*$zoom;
        $y1 = $coords[1]*$zoom;
        $x2 = $coords[2]*$zoom;
        $y2 = $coords[3]*$zoom;

        $x1 += $x;
        $y1 += $y;
        $x2 += $x;
        $y2 += $y;

        switch ( $origin ) {

          case 'left':
            if ( $type == 'circle' ) {
              $x1 = $x1+($bigX/2);
              $y1 = $y1+($bigY/2);
              $x2 = $x2+($bigX/2);
              $y2 = $y2+($bigY/2);
            }
          break;

          case 'center':
            if ( $type != 'circle' ) {
              $x1 = $x1-($bigX/2);
              $y1 = $y1-($bigY/2);
              $x2 = $x2-($bigX/2);
              $y2 = $y2-($bigY/2);
            }
          break;

          case 'right':
            $x1 = $x1-$bigX;
            $y1 = $y1-$bigY;
            $x2 = $x2-$bigX;
            $y2 = $y2-$bigY;
          break;

        }

        switch ($type) {

          case 'line':
            $this->game->addLine(floor($x1),floor($y1),floor($x2),floor($y2),$color);
          break;

          case 'rectangle':
            $this->game->addRectangle(floor($x1),floor($y1),floor($x2),floor($y2),$color);
          break;

          case 'circle':
            $dim = $coords[2]*$zoom;
            if ( !$dim ) $dim = 20;
            $this->game->addCircle(floor($x1),floor($y1),floor($dim),$color);
          break;

        }

      }

    }

  }


  /*
  *
  * ---------
  * DRAW TEXT
  * ---------
  *
  */
  public function drawText($str,$str_x,$str_y,$color,$size) {

    $str = strtoupper( $str );

    $lines = explode('|',$str);

    foreach ( $lines as $key_line => $line) {

      $chars = str_split($line);

      $span = 0;
      foreach ($chars as $key_char => $char) {

        if ( $key_char != 0 && $chars[$key_char-1] == 'I' ) $span += -(30*$size);
        if ( $key_char != 0 && $chars[$key_char-1] == 'W' ) $span += 20*$size;

        $x = $str_x+(60*$size*$key_char)+$span;
        $y = $str_y+(90*$size*$key_line);

        $this->drawChar($char,floor($x) ,floor($y),$color,$size);

      }

    }

  }
  public function drawText2($str,$str_x,$str_y,$color,$size) {

    $size = 1;

    $lines = explode('|',$str);

    foreach ( $lines as $key_line => $line) {

      $chars = str_split($line);

      $span = 0;
      foreach ($chars as $key_char => $char) {

        //if ( $key_char != 0 && $chars[$key_char-1] == 'I' ) $span += -(20*$size);
        //if ( $key_char != 0 && $chars[$key_char-1] == 'W' ) $span += 10*$size;

        $x = ((60*$size)*$key_char)+$span;
        $y = (90*$size)*$key_line;

        $x += $str_x;
        $y += $str_y;

        $this->drawChar($char,floor($x) ,floor($y),$color,$size);

      }

    }

  }


  /*
  *
  * --------------
  * DRAW CHARACTER
  * --------------
  *
  */
  public function drawChar($char,$posx,$posy,$color,$size) {

    switch ($char) {

      case 'A':

        $coord = array(
          array(35,40,13,40),
          array(13,40,13,69),
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,69),
          array(48,69,35,69),
          array(35,69,35,40),
          array(13,11,13,29),
          array(13,29,35,29),
          array(35,29,35,11),
          array(35,11,13,11),
        );

      break;

      case 'B':

        $coord = array(
          array(48,0,48,26),
          array(48,26,42,35),
          array(42,35,48,43),
          array(48,43,48,69),
          array(48,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(13,11,13,29),
          array(13,29,35,29),
          array(35,29,35,11),
          array(35,11,13,11),
          array(13,40,13,57),
          array(13,57,35,57),
          array(35,57,35,40),
          array(35,40,13,40),
        );

      break;

      case 'C':

        $coord = array(
          array(13,57,48,57),
          array(48,57,48,69),
          array(48,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,11),
          array(48,11,13,11),
          array(13,11,13,57),
        );

      break;

      case 'D':

        $coord = array(
          array(0,69,0,0),
          array(0,0,42,0),
          array(42,0,48,6),
          array(48,6,48,63),
          array(48,63,43,69),
          array(43,69,0,69),
          array(35,11,13,11),
          array(13,11,13,57),
          array(13,57,35,57),
          array(35,57,35,11),
        );

      break;

      case 'E':

        $coord = array(
          array(13,11,13,29),
          array(13,29,48,29),
          array(48,29,48,40),
          array(48,40,13,40),
          array(13,40,13,57),
          array(13,57,48,57),
          array(48,57,48,69),
          array(48,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,11),
          array(48,11,13,11),
        );

      break;

      case 'F':

        $coord = array(
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,11),
          array(48,11,13,11),
          array(13,11,13,29),
          array(13,29,48,29),
          array(48,29,48,40),
          array(48,40,13,40),
          array(13,40,13,69),
        );

      break;

      case 'G':

        $coord = array(
          array(21,29,48,29),
          array(48,29,48,69),
          array(48,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,11),
          array(48,11,13,11),
          array(13,11,13,57),
          array(13,57,35,57),
          array(35,57,35,40),
          array(35,40,21,40),
          array(21,40,21,29),
        );

      break;

      case 'H':

        $coord = array(
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,13,0),
          array(13,0,13,29),
          array(13,29,35,29),
          array(35,29,35,0),
          array(35,0,48,0),
          array(48,0,48,69),
          array(48,69,35,69),
          array(35,69,35,40),
          array(35,40,13,40),
          array(13,40,13,69),
        );

      break;

      case 'I':

        $coord = array(
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,13,0),
          array(13,0,13,69),
        );

      break;

      case 'J':

        $coord = array(
          array(13,39,13,57),
          array(13,57,35,57),
          array(35,57,35,0),
          array(35,0,48,0),
          array(48,0,48,69),
          array(48,69,0,69),
          array(0,69,0,39),
          array(0,39,13,39),
        );

      break;

      case 'K':

        $coord = array(
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,13,0),
          array(13,0,13,29),
          array(13,29,35,0),
          array(35,0,48,0),
          array(48,0,24,34),
          array(24,34,48,69),
          array(48,69,35,69),
          array(35,69,13,40),
          array(13,40,13,69),
        );

      break;

      case 'L':

        $coord = array(
          array(13,57,48,57),
          array(48,57,48,69),
          array(48,69,0,69),
          array(0,69,0,0),
          array(0,0,13,0),
          array(13,0,13,57),
        );

      break;

      case 'M':

        $coord = array(
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,13,0),
          array(13,0,27,38),
          array(27,38,41,0),
          array(41,0,53,0),
          array(53,0,53,69),
          array(53,69,41,69),
          array(41,69,41,40),
          array(41,40,31,69),
          array(31,69,23,69),
          array(23,69,13,40),
          array(13,40,13,69),
        );

      break;

      case 'N':

        $coord = array(
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,13,0),
          array(13,0,35,39),
          array(35,39,35,0),
          array(35,0,48,0),
          array(48,0,48,69),
          array(48,69,35,69),
          array(35,69,13,29),
          array(13,29,13,69),
        );

      break;

      case 'O':

        $coord = array(
          array(48,0,48,69),
          array(48,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(35,11,13,11),
          array(13,11,13,57),
          array(13,57,35,57),
          array(35,57,35,11),
        );

      break;

      case 'P':

        $coord = array(
          array(13,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,40),
          array(48,40,13,40),
          array(13,40,13,69),
          array(13,11,13,29),
          array(13,29,35,29),
          array(35,29,35,11),
          array(35,11,13,11),
        );

      break;

      case 'Q':

        $coord = array(
          array(30,69,0,69),
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,69),
          array(48,69,41,69),
          array(41,69,47,81),
          array(47,81,36,81),
          array(36,81,30,69),
          array(35,57,35,11),
          array(35,11,13,11),
          array(13,11,13,57),
          array(13,57,24,57),
          array(24,57,19,49),
          array(19,49,31,49),
          array(31,49,35,57),
        );

      break;

      case 'R':

        $coord = array(
          array(0,69,0,0),
          array(0,0,48,0),
          array(48,0,48,40),
          array(48,40,31,40),
          array(31,40,48,69),
          array(48,69,35,69),
          array(35,69,18,40),
          array(18,40,13,40),
          array(13,40,13,69),
          array(13,69,0,69),
          array(13,11,13,29),
          array(13,29,35,29),
          array(35,29,35,11),
          array(35,11,13,11),
        );

      break;

      case 'S':

        $coord = array(
          array(0,40,0,0),
          array(0,0,48,0),
          array(48,0,48,11),
          array(48,11,13,11),
          array(13,11,13,29),
          array(13,29,48,29),
          array(48,29,48,69),
          array(48,69,0,69),
          array(0,69,0,57),
          array(0,57,35,57),
          array(35,57,35,40),
          array(35,40,0,40),
        );

      break;

      case 'T':

        $coord = array(
          array(18,11,0,11),
          array(0,11,0,0),
          array(0,0,48,0),
          array(48,0,48,11),
          array(48,11,31,11),
          array(31,11,31,69),
          array(31,69,18,69),
          array(18,69,18,11),
        );

      break;

      case 'U':

        $coord = array(
          array(35,0,48,0),
          array(48,0,48,69),
          array(48,69,0,69),
          array(0,69,0,0),
          array(0,0,13,0),
          array(13,0,13,57),
          array(13,57,35,57),
          array(35,57,35,0),
        );

      break;

      case 'V':

        $coord = array(
          array(35,0,48,0),
          array(48,0,32,69),
          array(32,69,16,69),
          array(16,69,0,0),
          array(0,0,13,0),
          array(13,0,23,53),
          array(23,53,35,0),
        );

      break;

      case 'W':

        $coord = array(
          array(34,37,28,69),
          array(28,69,12,69),
          array(12,69,0,0),
          array(0,0,13,0),
          array(13,0,20,46),
          array(20,46,27,0),
          array(27,0,40,0),
          array(40,0,47,46),
          array(47,46,54,0),
          array(54,0,68,0),
          array(68,0,55,69),
          array(55,69,39,69),
          array(39,69,34,37),
        );

      break;

      case 'X':

        $coord = array(
          array(17,34,17,34),
          array(17,34,0,0),
          array(0,0,13,0),
          array(13,0,24,22),
          array(24,22,35,0),
          array(35,0,48,0),
          array(48,0,30,34),
          array(30,34,31,34),
          array(31,34,48,69),
          array(48,69,34,69),
          array(34,69,23,47),
          array(23,47,12,69),
          array(12,69,0,69),
          array(0,69,17,34),
        );

      break;

      case 'Y':

        $coord = array(
          array(30,34,30,69),
          array(30,69,16,69),
          array(16,69,16,34),
          array(16,34,0,0),
          array(0,0,13,0),
          array(13,0,24,22),
          array(24,22,35,0),
          array(35,0,48,0),
          array(48,0,30,34),
        );

      break;

      case 'Z':

        $coord = array(
          array(31,11,0,11),
          array(0,11,0,0),
          array(0,0,48,0),
          array(48,0,48,11),
          array(48,11,16,57),
          array(16,57,48,57),
          array(48,57,48,69),
          array(48,69,0,69),
          array(0,69,0,57),
          array(0,57,31,11),
        );

      break;

    }

    if ( $coord ) {

      $this->drawShape($posx,$posy,$size,array(

        array(
          "id" => "chart",
          "type" => "line",
          "origin" => "left",
          "color" => $color,
          "coord" => $coord,
        )

      ));

    }

  }

  public function setGraphics(){

    /*
    | LOGO
    */
    $this->graphic_LOGO = array(

      array(
        "id" => "S",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(0,89,0,69),
          array(0,69,66,69),
          array(66,69,66,66),
          array(66,66,0,66),
          array(0,66,0,23),
          array(0,23,85,23),
          array(85,23,85,42),
          array(85,42,19,42),
          array(19,42,19,46),
          array(19,46,85,46),
          array(85,46,85,89),
          array(85,89,0,89),
        )
      ),

      array(
        "id" => "I",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(94,89,94,23),
          array(94,23,113,23),
          array(113,23,113,89),
          array(113,89,94,89),
        )
      ),

      array(
        "id" => "M",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(122,89,122,19),
          array(122,19,165,62),
          array(165,62,208,19),
          array(208,19,208,89),
          array(208,89,188,89),
          array(188,89,188,66),
          array(188,66,165,89),
          array(165,89,142,66),
          array(142,66,142,89),
          array(142,89,122,89),
        )
      ),

      array(
        "id" => "0",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(217,89,217,23),
          array(217,23,302,23),
          array(302,23,302,89),
          array(302,89,217,89),
          array(283,69,283,42),
          array(283,42,236,42),
          array(236,42,236,69),
          array(236,69,283,69),
        )
      ),

      array(
        "id" => "N",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(330,46,330,89),
          array(330,89,311,89),
          array(311,89,311,0),
          array(311,0,377,66),
          array(377,66,377,23),
          array(377,23,396,23),
          array(396,23,396,112),
          array(396,112,330,46),
        )
      ),

      array(
        "id" => "decoration",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::LIME,
        "coord" => array(
          array(182,117,0,117),
        )
      ),

      array(
        "id" => "L",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::LIME,
        "coord" => array(
          array(197,128,197,105),
          array(197,105,203,105),
          array(203,105,203,121),
          array(203,121,227,121),
          array(227,121,227,128),
          array(227,128,197,128),
        )
      ),

      array(
        "id" => "A",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::LIME,
        "coord" => array(
          array(231,128,262,98),
          array(262,98,262,128),
          array(262,128,255,128),
          array(255,128,255,114),
          array(255,114,241,128),
          array(241,128,231,128),
        )
      ),

      array(
        "id" => "Z",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::LIME,
        "coord" => array(
          array(265,128,281,112),
          array(281,112,266,112),
          array(266,112,266,105),
          array(266,105,298,105),
          array(298,105,281,121),
          array(281,121,296,121),
          array(296,121,296,128),
          array(296,128,265,128),
        )
      ),

      array(
        "id" => "E",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::LIME,
        "coord" => array(
          array(301,128,301,105),
          array(301,105,331,105),
          array(331,105,331,112),
          array(331,112,308,112),
          array(308,112,308,113),
          array(308,113,331,113),
          array(331,113,324,120),
          array(324,120,308,120),
          array(308,120,308,121),
          array(308,121,331,121),
          array(331,121,331,128),
          array(331,128,301,128),
        )
      ),

      array(
        "id" => "R",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::LIME,
        "coord" => array(
          array(344,113,359,113),
          array(359,113,359,112),
          array(359,112,343,112),
          array(343,112,343,128),
          array(343,128,336,128),
          array(336,128,336,105),
          array(336,105,366,105),
          array(366,105,366,120),
          array(366,120,361,120),
          array(361,120,367,127),
          array(367,127,367,137),
          array(367,137,344,113),
        )
      )

    );

    /*
    | BUTTON
    */
    $this->graphic_BT_X = array(

      array(
        "id" => "btcircle",
        "type" => "circle",
        "origin" => "center",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(0,0,80,80),
        )
      ),
      array(
        "id" => "X",
        "type" => "line",
        "origin" => "center",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(0,0,30,30),
          array(0,30,30,0),
        )
      ),

    );

    /*
    | PRESS SIMON
    */
    $this->graphic_SIMON_PRESS = array(

      array(
        "id" => "",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(232,116,116,232),
          array(116,232,0,116),
          array(0,116,116,0),
          array(116,0,232,116),
        )
      ),
      array(
        "id" => "",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(214,116,116,214),
          array(116,214,17,116),
          array(17,116,116,17),
          array(116,17,214,116),
        )
      ),
      array(
        "id" => "",
        "type" => "line",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(195,116,116,195),
          array(116,195,36,116),
          array(36,116,116,36),
          array(116,36,195,116),
        )
      ),


    );








  }

}

$SIMONLAZER = new SIMONLAZER();

?>
