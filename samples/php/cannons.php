<?php

	include('EdgeLaser.ns.php');

	use EdgeLaser\LaserGame;
	use EdgeLaser\LaserColor;
	use EdgeLaser\LaserFont;
	use EdgeLaser\XboxKey;

	$game = new LaserGame('Cannons');

	$game->setResolution(500)->setDefaultColor(LaserColor::LIME)->setFramerate(20);

	$font_lcd = new LaserFont('fonts/lcd.elfc');

	while(true)
	{
		$commands = $game->receiveServerCommands();

		if(!$game->isStopped())
		{
			$game->newFrame();

			foreach(XboxKey::getKeys() as $key)
			{
				switch($key)
				{
					case XboxKey::P1_ARROW_LEFT : $cannon1->angle -= 1; break;
					case XboxKey::P1_ARROW_RIGHT : $cannon1->angle += 1; break;
					case XboxKey::P1_A : $cannon1->fire(); break;

					case XboxKey::P2_ARROW_LEFT : $cannon2->angle -= 1; break;
					case XboxKey::P2_ARROW_RIGHT : $cannon2->angle += 1; break;
					case XboxKey::P2_A : $cannon2->fire(); break;
				}
			}

			if(!$scene->win)
				$scene->move()->collide()->render();
			else
				$scene->win();

			$game->refresh()->endFrame();
		}

		else
		{
			sleep(1);

			$scene = new SceneManager;

			$cannon1 = new Cannon(1);
			$cannon2 = new Cannon(2);

			$map = new BlockMatrix;
			$map->loadRandom();

			$wind = new Wind;
			$wind->reevaluate();

			$scene->add($cannon1)->add($cannon2)->add($map)->add($wind);
		}
	}


	class SceneManager
	{
		private $smoid = 0;
		private $objects;
		public $playing = 1;
		public $win = 0;

		public function move()
		{
			foreach($this->objects as $obj)
			{
				$obj->move();
			}

			return $this;
		}

		public function collide()
		{
			foreach ($this->objects as $obj1)
			{
				if(get_class($obj1) == 'Bullet')
				{
					foreach($this->objects as $obj2)
					{
						if(get_class($obj2) != 'Bullet')
						{
							$obj1->collide($obj2);
						}
					}
				}
			}

			return $this;
		}

		public function render()
		{
			foreach($this->objects as $obj)
			{
				$obj->render();
			}

			return $this;
		}

		public function add($obj)
		{
			$obj->smoid = $this->smoid++;
			$this->objects[$obj->smoid] = $obj;

			return $this;
		}

		public function remove($obj)
		{
			if(get_class($obj) == 'Bullet')
			{
				global $scene;
				global $wind;

				if($obj->pcannon->fired)
				{
					$obj->pcannon->fired = false;
					$obj->pcannon->wait_bullet = false;
					$scene->playing = ($scene->playing == 1) ? 2 : 1;

					$wind->reevaluate();

					echo 'Player ' . $scene->playing . ' is now playing...' . PHP_EOL;
				}
			}

			unset($this->objects[$obj->smoid]);

			return $this;
		}

		public function win()
		{
			global $game;
			global $font_lcd;
			global $cannon1;

			$winner = ($cannon1->destroyed) ? 2 : 1;

			if($this->win <= 60)
				$font_lcd->render($game, 'PLAYER ' . $winner, 70, 200, LaserColor::WHITE, 4);
			elseif($this->win > 60 && $this->win < 150)
				$font_lcd->render($game, 'WIN', 120, 170, rand(1, 7), 8);
			else
				$game->stop();
			
			$this->win++;
		}
	}


	class Wind
	{
		public $force;

		public function render()
		{
			global $game;

			$game->addLine(250, 5, 250, 15, LaserColor::CYAN);
			$game->addLine(250, 10, 250 - $this->force * 600, 10, LaserColor::CYAN);
		}

		public function reevaluate()
		{
			$this->force = rand(-5, 5) / 30;
		}

		public function move(){}
	}


	class BlockMatrix
	{
		private $matrix;

		public function loadRandom()
		{
			global $scene;

			if($dh = opendir('.'))
			{
				while(($file = readdir($dh)) !== false)
				{
					if(substr($file, strlen($file)-4) == '.map')
						$maps[] = $file;
				}

				closedir($dh);
			}

			$map = explode("\n", file_get_contents($maps[rand(0, count($maps)-1)]));

			$y = 0;
			foreach ($map as &$line)
			{
				$x = 0;
				foreach(str_split($line) as $bool)
				{
					$bl = $this->matrix[$y][$x] = ($bool == '#') ? new Block($x, $y) : null;
					if($bl) $scene->add($bl);

					$x++;
				}

				$y++;
			}
		}

		public function render()
		{
			foreach ($this->matrix as $n_line => $line)
			{
				foreach ($line as $n_block => $block)
				{
					if($n_line > 0 and $block)
						if($this->matrix[$n_line-1][$n_block]) $block->neigh_top = true;
						else $block->neigh_top = false;

					if($n_line < 14 and $block)
						if($this->matrix[$n_line+1][$n_block]) $block->neigh_bottom = true;
						else $block->neigh_bottom = false;

					if($n_block > 0 and $block)
						if($this->matrix[$n_line][$n_block-1]) $block->neigh_left = true;
						else $block->neigh_left = false;

					if($n_block < 9 and $block)
						if($this->matrix[$n_line][$n_block+1]) $block->neigh_right = true;
						else $block->neigh_right = false;
				}
			}
		}

		public function remove($x, $y)
		{
			$this->matrix[$y][$x] = null;
		}

		public function move(){}
	}


	class Block
	{
		public $real_x;
		public $real_y;

		public $virt_x;
		public $virt_y;

		public $neigh_top;
		public $neigh_bottom;
		public $neigh_left;
		public $neigh_right;

		public function __construct($real_x, $real_y)
		{
			$this->real_x = $real_x;
			$this->real_y = $real_y;
			$this->virt_x = $real_x*33.3333 + 83.5;
			$this->virt_y = $real_y*33.3333;
		}

		public function render()
		{
			global $game;

			if(!$this->neigh_top)
				$game->addLine($this->virt_x, $this->virt_y, $this->virt_x+33.3333, $this->virt_y);
			if(!$this->neigh_right)
				$game->addLine($this->virt_x+33.3333, $this->virt_y, $this->virt_x+33.3333, $this->virt_y+33.3333);
			if(!$this->neigh_bottom)
				$game->addLine($this->virt_x+33.3333, $this->virt_y+33.3333, $this->virt_x, $this->virt_y+33.3333);
			if(!$this->neigh_left)
				$game->addLine($this->virt_x, $this->virt_y+33.3333, $this->virt_x, $this->virt_y);
		}

		public function move(){}
	}


	class Cannon
	{
		public $playerid;
		public $x;
		public $y;
		public $size;
		public $color;
		public $blink;
		public $angle;
		public $wait_bullet;
		public $fired;
		public $destroyed;

		public function __construct($playerid)
		{
			if($playerid == 1)
			{
				$this->x = 0;
				$this->y = 500;
				$this->color = LaserColor::CYAN;
				$this->angle = -45;
			}
			else
			{
				$this->x = 500;
				$this->y = 500;
				$this->color = LaserColor::RED;
				$this->angle = -135;
			}

			$this->playerid = $playerid;
			$this->size = 70;
			$this->wait_bullet = false;
			$this->fired = false;
			$this->destroyed = false;
		}

		public function render()
		{
			global $game;

			$color = ($this->blink) ? rand(1, 7) : $this->color;

			$game->addCircle($this->x, $this->y, $this->size, $color);

			$game->addLine(
				cos(deg2rad($this->angle))*35+$this->x,
				sin(deg2rad($this->angle))*35+$this->y,
				cos(deg2rad($this->angle))*60+$this->x,
				sin(deg2rad($this->angle))*60+$this->y,
				$color
			);
		}

		public function fire()
		{
			global $scene;

			if(!$this->wait_bullet and $scene->playing == $this->playerid)
			{
				$this->wait_bullet = true;

				$bullet = new Bullet($this);
				$scene->add($bullet);

				$this->fired = true;

				echo ' - Player ' . $scene->playing . ' has fired!' . PHP_EOL;
			}
		}

		public function move(){}
	}


	class Bullet
	{
		public $x;
		public $y;
		public $size;
		public $color;
		public $pcannon;
		public $cannon;
		public $angle;

		public function __construct($cannon)
		{
			$this->x = cos(deg2rad($cannon->angle))*60+$cannon->x;
			$this->y = sin(deg2rad($cannon->angle))*60+$cannon->y;
			$this->force_x = (cos(deg2rad($cannon->angle))*68+$cannon->x) - $this->x;
			$this->force_y = (sin(deg2rad($cannon->angle))*68+$cannon->y) - $this->y;
			$this->size = 12;
			$this->color = $cannon->color;
			$this->pcannon = $cannon;
			$this->cannon = clone $cannon;
		}

		public function render()
		{
			global $game;

			$game->addCircle($this->x, $this->y, $this->size, $this->color);
		}

		public function move()
		{
			global $scene;

			$this->force_y = $this->force_y + 0.15;

			$this->x += $this->force_x;
			$this->y += $this->force_y;

			if($this->x < 0 or $this->x > 500 or $this->y < 0 or $this->y > 500)
			{
				echo ' - Bullet is out of borders...' . PHP_EOL;

				$scene->remove($this);
			}
		}

		public function collide($target)
		{
			global $scene;

			switch(get_class($target))
			{
				case 'Cannon':
					if( $this->x+$this->size/2 > $target->x-$target->size/2 and
						$this->x-$this->size/2 < $target->x+$target->size/2 and
						$this->y+$this->size/2 > $target->y-$target->size/2)
					{
						if(!$target->blink)
						{
							echo ' - Cannon damaged' . PHP_EOL;
							$target->blink = true;
							$scene->remove($this);
						}
						else
						{
							echo ' - Cannon destroyed' . PHP_EOL;
							$target->destroyed = true;
							$scene->win = 1;
						}
					}
					break;

				case 'Block':
					if( $this->x+$this->size/2 > $target->virt_x and
						$this->x-$this->size/2 < $target->virt_x+33.3333 and
						$this->y+$this->size/2 > $target->virt_y and
						$this->y-$this->size/2 < $target->virt_y+33.3333)
					{
						echo ' - Block destroyed' . PHP_EOL;
						
						global $map;

						$map->remove($target->real_x, $target->real_y);
						$scene->remove($target);
						$scene->remove($this);
					}
					break;

				case 'Wind':
					$this->force_x -= $target->force;
					break;
			}
		}
	}

?>
