<?php

/*
//$this->anim1 = new animShape( $this->game, 5, 10, array( 375,  250,  0,  -135,  $this->graphic_SIMON_back), array( 375,  250,  1,  -135,  $this->graphic_SIMON_back) );
*/

class animShape {

  /*
  *
  * ---------
  * CONSTRUCT
  * ---------
  *
  */
  public function __construct( $game, $delay, $duration, $drawShape_start , $drawShape_end ) {

    $this->game = $game;

    if ( !$this->animShapeFrame ) $this->animShapeFrame = 0;

    if ( $this->animShapeFrame !== 'complete' && $this->animShapeFrame < $delay  ) {

      $this->drawShape( $drawShape_start[0],  $drawShape_start[1],  $drawShape_start[2],  $drawShape_start[3],  $drawShape_start[4] );

    }

    if ( $this->animShapeFrame !== 'complete' && $this->animShapeFrame > $delay  ) {

      $animZoom = $drawShape_start[2] + ((($drawShape_end[2] - $drawShape_start[2])/$duration)*($this->animShapeFrame-50));

      $this->drawShape( $drawShape_start[0],  $drawShape_start[1],  $animZoom,  $drawShape_start[3],  $drawShape_start[4] );

    }

    if ( $this->animShapeFrame == 'complete' ) {

      $this->drawShape( $drawShape_end[0],  $drawShape_end[1],  $drawShape_end[2],  $drawShape_end[3],  $drawShape_end[4] );

    }

    if ( $this->animShapeFrame !== 'complete' ) $this->animShapeFrame++;

    if ( $this->animShapeFrame == ($delay + $duration + 1) ) $this->animShapeFrame = 'complete';

    if ( $this->animShapeFrame !== 'complete' ) echo 'Frame(' . $this->animShapeFrame . ') $animZoom = ' . $animZoom . PHP_EOL;

  }

  /*
  *
  * ----------
  * DRAW SHAPE
  * ----------
  *
  */
  public function drawShape($x,$y,$zoom,$deg,$items){

    foreach ( $items as $item ) {

      $color = $item['color'];
      $type = $item['type'];
      $origin = $item['origin'];

      //get bigX and bigY to get middle axis
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
      /*
      //reset from 0 coord
      //get the more negative coord
      $negativeX = 0;
      $negativeY = 0;
      foreach ( $item['coord'] as $coords_key => $coords ) {

        foreach ($coords as $key => $coord) {

          if ( $key == 0 || $key == 2 ) {
            if ( $negativeX > $coord ) $negativeX = $coord;
          }

          if ( $key == 1 || $key == 3 ) {
            if ( $negativeY > $coord ) $negativeY = $coord;
          }

        }

      }

      foreach ( $item['coord'] as $key => $coords ) {

          $x1 = $coords[0];
          $y1 = $coords[1];
          $x2 = $coords[2];
          $y2 = $coords[3];

          $temp_x1 = $x1 + floor(abs($negativeX));
          $temp_y1 = $y1 + floor(abs($negativeY));
          $temp_x2 = $x2 + floor(abs($negativeX));
          $temp_y2 = $y2 + floor(abs($negativeY));

          $item['coord'][$key][0] = $temp_x1;
          $item['coord'][$key][1] = $temp_y1;
          $item['coord'][$key][2] = $temp_x2;
          $item['coord'][$key][3] = $temp_y2;

      }



*/
      //apply zoom


      //add global coord

      // align origin



      foreach ( $item['coord'] as $key => $coords  ) {

        //apply zoom
        $x1 = $coords[0];//*$zoom;
        $y1 = $coords[1];//*$zoom;
        $x2 = $coords[2];//*$zoom;
        $y2 = $coords[3];//*$zoom;

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
            $dim = $coords[2]*$zoom;
            if ( !$dim ) $dim = 20;
            $this->game->addCircle(floor($x1),floor($y1),floor($dim),$color);
          break;

        }

      }

    }

  }


}

?>
