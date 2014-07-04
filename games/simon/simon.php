<?php

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

include('../../samples/php/EdgeLaser.ns.php');

use EdgeLaser\LaserGame;
use EdgeLaser\LaserColor;
use EdgeLaser\XboxKey;

class SIMONLAZER {

  /*** #construct
  *
  * ---------
  * CONSTRUCT
  * ---------
  *
  ***/
  public function __construct() {

    echo 'SIMON: __construct' . PHP_EOL;

    //set lazer
		$this->game = new LaserGame('simon');
		$this->game->setResolution(500);
		$this->game->setDefaultColor(LaserColor::LIME);
    $this->game->setFramerate(25);

    //set graphic
    $this->setGraphics();

    $this->defaultScreen = 'start';

    //init screen
    $this->startInit();
    $this->levelInit();

    //init game
		$this->init();

	}


  /*** #init
  *
  * --------
  * RUN GAME
  * --------
  *
  ***/
	public function init() {

    echo 'SIMON: init' . PHP_EOL;

  	/*
     | Generate level
    */
    $this->levelArray = $this->levelGenerate( 20 );
    //$this->levelArray = array('X','Y','B','A');
    echo implode(',', $this->levelArray) . PHP_EOL;

    /*
    |
    | Init Game
    |
    */
		while ( true ) {

      $commands = $this->game->receiveServerCommands();

      if ( ! $this->game->isStopped() ) {

        $this->game->newFrame();

        switch ($this->screen) {

          case 'start':

            $this->levelInit();
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

        $this->game->endFrame();

        $this->game->refresh();

        usleep(50000);

      } else {

        $this->screen = $this->defaultScreen;

      }

    }


	}


  /*** #start
  *
  * ------------
  * START INIT  => init screen
  * ------------
  *
  ***/
  public function startInit(){

    $this->startScreen_timer_global = 0;

    $this->startScreen_timer_logoFX_loop = 30;
    $this->startScreen_timer_logoFX = 0;

    $this->startScreen_timer_btX_loop = 15;
    $this->startScreen_timer_btX = 0;

  }


  /*** #start
  *
  * ------------
  * START SCREEN  => screen loop
  * ------------
  *
  ***/
  public function startScreen(){

    /*
     | anim Logo effect
    */
    if ( $this->startScreen_timer_logoFX == $this->startScreen_timer_logoFX_loop ) $this->startScreen_timer_logoFX = 0;

    $this->graphic_LOGO[4][color] = LaserColor::BLUE;
    $this->graphic_LOGO[3][color] = LaserColor::BLUE;
    $this->graphic_LOGO[2][color] = LaserColor::BLUE;
    $this->graphic_LOGO[1][color] = LaserColor::BLUE;
    $this->graphic_LOGO[0][color] = LaserColor::BLUE;
    if ( $this->startScreen_timer_logoFX == 13 ) $this->graphic_LOGO[4][color] = LaserColor::CYAN;
    if ( $this->startScreen_timer_logoFX == 14 ) $this->graphic_LOGO[3][color] = LaserColor::CYAN;
    if ( $this->startScreen_timer_logoFX == 15 ) $this->graphic_LOGO[2][color] = LaserColor::CYAN;
    if ( $this->startScreen_timer_logoFX == 16 ) $this->graphic_LOGO[1][color] = LaserColor::CYAN;
    if ( $this->startScreen_timer_logoFX == 17 ) $this->graphic_LOGO[0][color] = LaserColor::CYAN;

    $this->startScreen_timer_logoFX++;

    /*
     | anim Logo slide
    */
    $this->animShape( array(
      "id"           =>   '',
      "frameBase"    =>   $this->startScreen_timer_global,
      "showOnFrame"  =>   0,
      "animDelay"    =>   0,
      "animDuration" =>   3,
      "hideOnFrame"  =>   5,
      "stateStart"   =>   array( 50,  0,  1,    0,  $this->graphic_LOGO),
      "stateEnd"     =>   array( 50,  100,  1,  0,  $this->graphic_LOGO),
    ));


    /*
     | anim bt X slide
    */
    $this->animShape( array(
      "id"           =>   '',
      "frameBase"    =>   $this->startScreen_timer_global,
      "showOnFrame"  =>   0,
      "animDelay"    =>   0,
      "animDuration" =>   3,
      "hideOnFrame"  =>   3,
      "stateStart"   =>   array( 250,  500,  1.1,  0,  $this->graphic_BT_X),
      "stateEnd"     =>   array( 250,  350,  1.1,  0,  $this->graphic_BT_X),
    ));

    /*
     | anim bt X push
    */
    if ( $this->startScreen_timer_btX == $this->startScreen_timer_btX_loop ) $this->startScreen_timer_btX = 3;

    $this->animShape( array(
      "id"           =>   '',
      "frameBase"    =>   $this->startScreen_timer_btX,
      "showOnFrame"  =>   3,
      "animDelay"    =>   3,
      "animDuration" =>   3,
      "hideOnFrame"  =>   6,
      "stateStart"   =>   array( 250,  350,  1.1,  0,  $this->graphic_BT_X),
      "stateEnd"     =>   array( 250,  350,  1,    0,  $this->graphic_BT_X),
    ));

    $this->animShape( array(
      "id"           =>   '',
      "frameBase"    =>   $this->startScreen_timer_btX,
      "showOnFrame"  =>   6,
      "animDelay"    =>   3,
      "animDuration" =>   3,
      "hideOnFrame"  =>   $this->startScreen_timer_btX_loop,
      "stateStart"   =>   array( 250,  350,  1.1,  0,  $this->graphic_BT_X),
      "stateEnd"     =>   array( 250,  350,  1,    0,  $this->graphic_BT_X),
    ));

    $this->startScreen_timer_btX++;


    if ( $this->startScreen_timer_global < 4 ) $this->startScreen_timer_global++;

  }


  /*** #level
  *
  * ------------
  * LEVEL INIT  => init screen
  * ------------
  *
  ***/
  public function levelInit(){

    $this->levelScreen_timer_global = 0;
    $this->levelScreen_timer_CPU = 0;

    $this->levelSubScreen = 'intro';
    $this->levelState = 'CPU';

    $this->levelPosMax = 0;
    $this->levelPosReader = 0;
    $this->levelPos = 0;

    $this->pushSubmit = null;
    $this->pushSubmitArray = array();
    $this->player_score = 0;

  }

  /*** #level
  *
  * ------------
  * LEVEL SCREEN  => screen loop
  * ------------
  *
  ***/
  public function levelScreen(){

    switch ($this->levelSubScreen) {

      case 'intro':
        $this->menuControl();
        $this->levelScreenIntro();
        $this->levelScreen_timer_global++;
        if ( $this->levelScreen_timer_global == 46 ) $this->levelSubScreen = 'play';
      break;

      case 'play':

        if ( $this->levelState == 'CPU' ) {

          $this->pushCPU();
          $this->drawText('LOOK',10,470,LaserColor::BLUE,.3);

          $this->levelScreen_timer_CPU++;

        }

        if ( $this->levelState == 'player' ) {

          $this->pushPlayer();
          $this->drawText('PLAY',10,470,LaserColor::LIME,.3);

        }

        if ( $this->levelState == 'success' ) {

          $this->levelNext();
          $this->drawText('SUCCESS',450,470,LaserColor::LIME,.3);

        }

        if ( $this->levelState == 'error' ) {

          $this->levelReplay();
          $this->drawText('ERROR',450,470,LaserColor::RED,.3);

        }

        $this->drawText('SCORE',10,10,LaserColor::YELLOW,.3);

        $this->levelScreenPlay();

      break;

      case 'outro':
        $this->levelScreenIntro();
        //$this->levelScreen_timer_global--;
      break;

    }

  }

  /*** #level
  *
  * -----------------------
  * LEVEL SUBSCREEN > INTRO
  * -----------------------
  *
  ***/
  public function pushCPU(){


    if ( $this->levelPosReader <= count($this->levelArray) ) { //if not finish level array

      $display = ($this->levelPosReader*8)+10;

      if ( $this->levelScreen_timer_CPU == $display ){

        $this->pushNeeded = $this->levelArray[$this->levelPosReader];

        switch ( $this->pushNeeded ) { // display the current key

          case 'X':
            $this->pushX = true;
            $this->pushY = false;
            $this->pushB = false;
            $this->pushA = false;
          break;
          case 'Y':
            $this->pushX = false;
            $this->pushY = true;
            $this->pushB = false;
            $this->pushA = false;
          break;
          case 'B':
            $this->pushX = false;
            $this->pushY = false;
            $this->pushB = true;
            $this->pushA = false;
          break;
          case 'A':
            $this->pushX = false;
            $this->pushY = false;
            $this->pushB = false;
            $this->pushA = true;
          break;
          default:
            $this->pushX = false;
            $this->pushY = false;
            $this->pushB = false;
            $this->pushA = false;
          break;

        }

      }

      if ( $this->levelScreen_timer_CPU == $display ){

        if ( $this->levelPosReader <= $this->levelPosMax ) {

            $this->levelPosReader++;

        } else {

          $this->levelState = 'player';

        }

      }

    } else {

      echo 'YOU BEAT SIMON' . PHP_EOL;

    }

  }

  /*** #control
  *
  * ------------
  * CONTROL PUSH  => ...
  * ------------
  *
  ***/
  public function pushPlayer(){

    $keyListener = XboxKey::getKeys();

    switch( $keyListener[0] ) {

      case XboxKey::P1_X :

        $this->pushX = true;
        $this->pushY = false;
        $this->pushB = false;
        $this->pushA = false;
        $this->pushSubmit = 'X';
        $this->keyRelease = false;

      break;

      case XboxKey::P1_Y :

        $this->pushX = false;
        $this->pushY = true;
        $this->pushB = false;
        $this->pushA = false;
        $this->pushSubmit = 'Y';
        $this->keyRelease = false;

      break;

      case XboxKey::P1_B :

        $this->pushX = false;
        $this->pushY = false;
        $this->pushB = true;
        $this->pushA = false;
        $this->pushSubmit = 'B';
        $this->keyRelease = false;

      break;

      case XboxKey::P1_A :

        $this->pushX = false;
        $this->pushY = false;
        $this->pushB = false;
        $this->pushA = true;
        $this->pushSubmit = 'A';
        $this->keyRelease = false;

      break;

      case XboxKey::P1_ARROW_RIGHT :

        $this->levelSubScreen = 'outro';

      break;

      case XboxKey::P1_ARROW_LEFT :

        $this->screen = 'start';

      break;

      default:

       $this->pushX = false;
       $this->pushY = false;
       $this->pushB = false;
       $this->pushA = false;
       $this->keyRelease = true;

      break;

    }

    if ( $this->keyRelease && $this->pushSubmit) {

      $this->pushSubmitArray[$this->levelPos] = $this->pushSubmit;
      $this->keyRelease = false;
      $this->pushSubmit = null;

      if ( $this->levelArray[$this->levelPos] == $this->pushSubmitArray[$this->levelPos] ){

        if ( $this->levelPos == $this->levelPosMax ){

          $this->levelState = 'success';

        } else {

          $this->levelPos++;

        }

      } else {

        $this->levelState = 'error';

      }

    }

  }

  /*** #level
  *
  * ----------
  * LEVEL NEXT
  * ----------
  *
  ***/
  public function levelNext(){

    $this->levelPos = 0;
    $this->pushSubmitArray = array();
    $this->levelScreen_timer_CPU = 0;
    $this->levelPosReader = 0;
    $this->levelPosMax++;
    $this->levelState = 'CPU';

  }

  /*** #level
  *
  * ------------
  * LEVEL REPLAY
  * ------------
  *
  ***/
  public function levelReplay(){

    $this->levelPos = 0;
    $this->pushSubmitArray = array();
    $this->levelScreen_timer_CPU = 0;
    $this->levelPosReader = 0;
    $this->levelState = 'CPU';

  }


  /*** #level
  *
  * -----------------------
  * LEVEL SUBSCREEN > INTRO
  * -----------------------
  *
  ***/
  public function levelScreenPlay(){

    /*
     | Pusch X
    */
    if ( $this->pushX ) {

      $this->drawShape( 240,  250,  1,  135,  $this->graphic_push_X);

    } else {

      $this->drawShape( 240,  250,  .7,  135,  $this->graphic_push_X_fade);

    }

    /*
     | Pusch Y
    */
    if ( $this->pushY ) {

      $this->drawShape( 250,  240,  1,  -135,  $this->graphic_push_Y);

    } else {

      $this->drawShape( 250,  240,  .7,  -135,  $this->graphic_push_Y_fade);

    }

    /*
     | Pusch B
    */
    if ( $this->pushB ) {

      $this->drawShape( 260,  250,  1,  -45,  $this->graphic_push_B);

    } else {

      $this->drawShape( 260,  250,  .7,  -45,  $this->graphic_push_B_fade);

    }

    /*
     | Pusch A
    */
    if ( $this->pushA ) {

      $this->drawShape( 250,  260,  1,  45,  $this->graphic_push_A);

    } else {

      $this->drawShape( 250,  260,  .7,  45,  $this->graphic_push_A_fade);

    }

  }

  /*** #level
  *
  * -----------------------
  * LEVEL SUBSCREEN > INTRO
  * -----------------------
  *
  ***/
  public function levelScreenIntro(){

    /*
     | draw level background
    */
    $this->animShape( array(
      "id"           =>   'X',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   0,
      "animDelay"    =>   0,
      "animDuration" =>   4,
      "hideOnFrame"  =>   20,
      "stateStart"   =>   array( 240,  250,  0,  135-30,  $this->graphic_push_X),
      "stateEnd"     =>   array( 240,  250,  1,  135,  $this->graphic_push_X),
    ));

    $this->animShape( array(
      "id"           =>   'Y',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   0,
      "animDelay"    =>   4,
      "animDuration" =>   4,
      "hideOnFrame"  =>   20,
      "stateStart"   =>   array( 250,  240,  0,  -135-30,  $this->graphic_push_Y),
      "stateEnd"     =>   array( 250,  240,  1,  -135,  $this->graphic_push_Y),
    ));

    $this->animShape( array(
      "id"           =>   'B',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   0,
      "animDelay"    =>   8,
      "animDuration" =>   4,
      "hideOnFrame"  =>   20,
      "stateStart"   =>   array( 260,  250,  0,  -45-30,  $this->graphic_push_B),
      "stateEnd"     =>   array( 260,  250,  1,  -45,  $this->graphic_push_B),
    ));

    $this->animShape( array(
      "id"           =>   '',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   0,
      "animDelay"    =>   12,
      "animDuration" =>   4,
      "hideOnFrame"  =>   20,
      "stateStart"   =>   array( 250,  260,  0,  45-30,  $this->graphic_push_A),
      "stateEnd"     =>   array( 250,  260,  1,  45,  $this->graphic_push_A),
    ));

    /*
     | draw level background
    */
    $this->animShape( array(
      "id"           =>   'X',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   20,
      "animDelay"    =>   20,
      "animDuration" =>   8,
      "hideOnFrame"  =>   9999,
      "stateStart"   =>   array( 240,  250,  1,  135,  $this->graphic_push_X),
      "stateEnd"     =>   array( 240,  250,  .7,  135,  $this->graphic_push_X_fade),
    ));

    $this->animShape( array(
      "id"           =>   'Y',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   20,
      "animDelay"    =>   22,
      "animDuration" =>   8,
      "hideOnFrame"  =>   9999,
      "stateStart"   =>   array( 250,  240,  1,  -135,  $this->graphic_push_Y),
      "stateEnd"     =>   array( 250,  240,  .7,  -135,  $this->graphic_push_Y_fade),
    ));

    $this->animShape( array(
      "id"           =>   'B',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   20,
      "animDelay"    =>   24,
      "animDuration" =>   8,
      "hideOnFrame"  =>   9999,
      "stateStart"   =>   array( 260,  250,  1,  -45,  $this->graphic_push_B),
      "stateEnd"     =>   array( 260,  250,  .7,  -45,  $this->graphic_push_B_fade),
    ));

    $this->animShape( array(
      "id"           =>   'A',
      "frameBase"    =>   $this->levelScreen_timer_global,
      "showOnFrame"  =>   20,
      "animDelay"    =>   26,
      "animDuration" =>   8,
      "hideOnFrame"  =>   9999,
      "stateStart"   =>   array( 250,  260,  1,  45,  $this->graphic_push_A),
      "stateEnd"     =>   array( 250,  260,  .7,  45,  $this->graphic_push_A_fade),
    ));

  }


  /*** #success
  *
  * --------------
  * SUCCESS SCREEN  => screen loop
  * --------------
  *
  ***/
  public function succesScreen(){

    //$this->drawText('BOOOOM|you|beat|simon',100,100,LaserColor::BLUE,1);
    //$this->drawText('GREAT|you|beat|simon',50,50,LaserColor::BLUE,1);

  }


  /*** #error
  *
  * ------------
  * ERROR SCREEN  => screen loop
  * ------------
  *
  ***/
  public function errorrScreen(){

    //$this->drawText('ERROR',100,200,LaserColor::RED,8);

  }



  /*** #control
  *
  * ------------
  * CONTROL MENU  => ...
  * ------------
  *
  ***/
	public function menuControl(){

   $keyListener = XboxKey::getKeys();

   switch( $keyListener[0] ) {

     case XboxKey::P1_X :

      $this->screen = 'level';

     break;

     case XboxKey::P1_ARROW_RIGHT :

       $this->levelSubScreen = 'outro';

     break;

     case XboxKey::P1_ARROW_LEFT :

       $this->screen = 'start';

     break;

   }

  }


  /*** #control
  *
  * ---------------
  * CONTROL SUCCESS  => ...
  * ---------------
  *
  ***/
  public function successControl(){

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


  /*** #control
  *
  * ---------------
  * CONTROL ERROR  => ...
  * ---------------
  *
  ***/
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


  /*** #tool
  *
  * --------------
  * GENERATE LEVEL
  * --------------
  *
  ***/
  public function levelGenerate($num = 1) {

    $key_array = array("X", "Y", "B", "A");
    $level = array();

    for ( $i=0; $i<=($num-1); $i++ ) {

      $random = array_rand($key_array);
      if ( $level[$i-1] != $key_array[$random] ) {
        $level[$i] = $key_array[$random];
      } else {
          $i = $i-1;
      }

    }

    return $level;

  }

  /*** #tool
  *
  * -------------
  * ANIMATE SHAPE => anim drawshape
  * -------------
  *
  ***/
  public function animShape( $params ){

    $id = $params['id'];
    $frameCount = $params['frameBase'];
    $show = $params['showOnFrame'];
    $hide = $params['hideOnFrame'];
    $delay = $params['animDelay'];
    $duration = $params['animDuration'];
    $drawShape_start = $params['stateStart'];
    $drawShape_end = $params['stateEnd'];

    if ( $frameCount >= $show && $frameCount <= $hide  ) {

      if ( $frameCount <= ($delay + $duration) ) {

        if (  $frameCount <= $delay  ) {

          $this->drawShape( $drawShape_start[0],  $drawShape_start[1],  $drawShape_start[2],  $drawShape_start[3],  $drawShape_start[4] );

        } else if (  $frameCount >= $delay  ) {

          $animX = floor($drawShape_start[0] + ((($drawShape_end[0] - $drawShape_start[0])/$duration)*(($frameCount)-$delay)) );
          $animY = floor($drawShape_start[1] + ((($drawShape_end[1] - $drawShape_start[1])/$duration)*(($frameCount)-$delay)) );
          $animZoom = $drawShape_start[2] + ((($drawShape_end[2] - $drawShape_start[2])/$duration)*(($frameCount)-$delay));
          $animDeg = floor($drawShape_start[3] + ((($drawShape_end[3] - $drawShape_start[3])/$duration)*(($frameCount)-$delay)) );

          $this->drawShape( $animX,  $animY,  $animZoom,  $animDeg,  $drawShape_start[4] );

        }

      } else {

        $this->drawShape( $drawShape_end[0],  $drawShape_end[1],  $drawShape_end[2],  $drawShape_end[3],  $drawShape_end[4] );

      }

    }

  }

  /*** #tool
  *
  * ----------
  * DRAW SHAPE
  * ----------
  *
  ***/
  public function drawShape($x,$y,$zoom,$deg,$items){

    foreach ( $items as $item ) {

      $color = $item['color'];
      $type = $item['type'];
      $origin = $item['origin'];

      //get bigX and bigY
      $bigX = 0;
      $bigY = 0;
      foreach ( $item['coord'] as $coords_key => $coords ) {

        foreach ($coords as $key => $coord) {

          if ( $key == 0 || $key == 2 ) {
            if ( $bigX < $coord ) $bigX = $coord*$zoom;
          }

          if ( $key == 1 || $key == 3 ) {
            if ( $bigY < $coord ) $bigY = $coord*$zoom;
          }

        }

      }

      //apply rotation
      foreach ( $item['coord'] as $key => $coords ) {

          $x1 = $coords[0]*$zoom;
          $y1 = $coords[1]*$zoom;
          $x2 = $coords[2]*$zoom;
          $y2 = $coords[3]*$zoom;

          switch ( $origin ) {

            case 'center':
              if ( $type != 'circle' ) {
                $x1 = $x1-($bigX/2);
                $y1 = $y1-($bigY/2);
                $x2 = $x2-($bigX/2);
                $y2 = $y2-($bigY/2);
              }
            break;

          }

          $temp_x1 = $x1 * cos(deg2rad($deg)) - $y1 * sin(deg2rad($deg));
          $temp_y1 = $x1 * sin(deg2rad($deg)) + $y1 * cos(deg2rad($deg));
          $temp_x2 = $x2 * cos(deg2rad($deg)) - $y2 * sin(deg2rad($deg));
          $temp_y2 = $x2 * sin(deg2rad($deg)) + $y2 * cos(deg2rad($deg));

          $item['coord'][$key][0] = $temp_x1;
          $item['coord'][$key][1] = $temp_y1;
          $item['coord'][$key][2] = $temp_x2;
          $item['coord'][$key][3] = $temp_y2;

      }

      foreach ( $item['coord'] as $key => $coords  ) {

        //apply zoom
        $x1 = $coords[0];
        $y1 = $coords[1];
        $x2 = $coords[2];
        $y2 = $coords[3];

        //apply global pos
        $x1 += $x;
        $y1 += $y;
        $x2 += $x;
        $y2 += $y;

        switch ($type) {

          case 'line':
            $this->game->addLine(floor($x1),floor($y1),floor($x2),floor($y2),$color);
          break;

          case 'rectangle':
            $this->game->addRectangle(floor($x1),floor($y1),floor($x2),floor($y2),$color);
          break;

          case 'circle':
            $dim = floor($coords[2]*$zoom);
            if ( !$dim ) $dim = 20;
            $this->game->addCircle(floor($x1),floor($y1),floor($dim),$color);
          break;

        }

      }

    }

  }


  /*** #tool
  *
  * ---------
  * DRAW TEXT
  * ---------
  *
  ***/
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


  /*** #graphics
  *
  * -------------
  * DRAW GRAPHICS
  * -------------
  *
  ***/
  public function setGraphics(){

    /*
    | LOGO #graphics
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
    | BUTTON X #graphics
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
    | SIMON ON PRESS #graphics
    */
    $this->graphic_SIMON_PRESS = array(

      array(
        "id" => "",
        "type" => "line",
        "origin" => "center",
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
        "origin" => "center",
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
        "origin" => "center",
        "color" => LaserColor::BLUE,
        "coord" => array(
          array(195,116,116,195),
          array(116,195,36,116),
          array(36,116,116,36),
          array(116,36,195,116),
        )
      ),


    );

    /*
    | SIMON BACK #graphics
    */
    $this->graphic_push_Y = array(
      array(
        "id" => "backY",
        "type" => "line",
        "axis" => "65,65",
        "origin" => "left",
        "color" => LaserColor::YELLOW,
        "coord" => array(

          //Y
          array(134,104,106,104),
          array(106,104,106,132),
          array(106,104,92,90),

          //back
          array(158,158,30,158),
          array(30,158,30,72),
          array(30,72,72,72),
          array(72,72,72,30),
          array(72,30,158,30),
          array(158,30,158,158),

          array(72,72,0,0),
          array(30,72,0,0),
          array(72,30,0,0),
          array(0,0,158,30),
          array(0,0,30,158),

        )
      ),
    );
    $this->graphic_push_B = array(
      array(
        "id" => "backY",
        "type" => "line",
        "axis" => "65,65",
        "origin" => "left",
        "color" => LaserColor::RED,
        "coord" => array(

          //B
          array(105,76,77,105),
          array(126,98,112,112),
          array(119,119,105,133),
          array(105,76,126,98),
          array(77,105,105,133),
          array(91,91,119,119),

          //back
          array(158,158,30,158),
          array(30,158,30,72),
          array(30,72,72,72),
          array(72,72,72,30),
          array(72,30,158,30),
          array(158,30,158,158),

          array(72,72,0,0),
          array(30,72,0,0),
          array(72,30,0,0),
          array(0,0,158,30),
          array(0,0,30,158),

        )
      ),
    );
    $this->graphic_push_A = array(
      array(
        "id" => "backY",
        "type" => "line",
        "axis" => "65,65",
        "origin" => "left",
        "color" => LaserColor::GREEN,
        "coord" => array(

          //A
          array(91,89,133,103),
          array(105,132,91,89),
          array(102,121,123,100),

          //back
          array(158,158,30,158),
          array(30,158,30,72),
          array(30,72,72,72),
          array(72,72,72,30),
          array(72,30,158,30),
          array(158,30,158,158),

          array(72,72,0,0),
          array(30,72,0,0),
          array(72,30,0,0),
          array(0,0,158,30),
          array(0,0,30,158),

        )
      ),
    );
    $this->graphic_push_X = array(
      array(
        "id" => "backY",
        "type" => "line",
        "axis" => "65,65",
        "origin" => "left",
        "color" => LaserColor::BLUE,
        "coord" => array(

          //X
          array(134,104,78,104),
          array(106,76,106,132),

          //back
          array(158,158,30,158),
          array(30,158,30,72),
          array(30,72,72,72),
          array(72,72,72,30),
          array(72,30,158,30),
          array(158,30,158,158),

          array(72,72,0,0),
          array(30,72,0,0),
          array(72,30,0,0),
          array(0,0,158,30),
          array(0,0,30,158),

        )
      ),
    );

    $fadeColor = LaserColor::FUCHSIA;
    $this->graphic_push_X_fade = $this->graphic_push_X;
    $this->graphic_push_Y_fade = $this->graphic_push_Y;
    $this->graphic_push_B_fade = $this->graphic_push_B;
    $this->graphic_push_A_fade = $this->graphic_push_A;
    $this->graphic_push_X_fade[0]['color'] = $fadeColor;
    $this->graphic_push_Y_fade[0]['color'] = $fadeColor;
    $this->graphic_push_B_fade[0]['color'] = $fadeColor;
    $this->graphic_push_A_fade[0]['color'] = $fadeColor;

    $successColor = LaserColor::GREEN;
    $this->graphic_push_X_success = $this->graphic_push_X;
    $this->graphic_push_Y_success = $this->graphic_push_Y;
    $this->graphic_push_B_success = $this->graphic_push_B;
    $this->graphic_push_A_success = $this->graphic_push_A;
    $this->graphic_push_X_success[0]['color'] = $successColor;
    $this->graphic_push_Y_success[0]['color'] = $successColor;
    $this->graphic_push_B_success[0]['color'] = $successColor;
    $this->graphic_push_A_success[0]['color'] = $successColor;

    $errorColor = LaserColor::RED;
    $this->graphic_push_X_error = $this->graphic_push_X;
    $this->graphic_push_Y_error = $this->graphic_push_Y;
    $this->graphic_push_B_error = $this->graphic_push_B;
    $this->graphic_push_A_error = $this->graphic_push_A;
    $this->graphic_push_X_error[0]['color'] = $errorColor;
    $this->graphic_push_Y_error[0]['color'] = $errorColor;
    $this->graphic_push_B_error[0]['color'] = $errorColor;
    $this->graphic_push_A_error[0]['color'] = $errorColor;

  }

  /*** #tool #fonts
  *
  * --------------
  * DRAW CHARACTER
  * --------------
  *
  ***/
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

      $this->drawShape($posx,$posy,$size,0,array(

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

}

$SIMONLAZER = new SIMONLAZER();

?>
