<?php

	/* EdgeLaserPHP
	 * Version: 0.1
	 * Authors: Morgan Touverey-Quilling (morgan-linux) && Yannick Jost */

	namespace EdgeLaser
	{

		class Socket
		{

			private $sock;
			private $internalBuffer;

			public function __construct($socket)
			{
					$this->sock = $socket;
			}

			public function bytesAvailable()
			{
				$this->getFromSocket();
				return strlen($this->internalBuffer);
			}

			private function getFromSocket()
			{
				$tmp='';
				$read = socket_recv($this->sock, $tmp, 65535, MSG_DONTWAIT);

				if ($read > 0)
				{
					$this->internalBuffer.= $tmp;
				}

			}

			public function read($byteCount)
			{
					$buffer='';
					$size=0;

					while($this->bytesAvailable() < $byteCount)
					{
							;
					}

					$buffer = substr($this->internalBuffer,0,$byteCount);
					$this->internalBuffer = substr($this->internalBuffer,$byteCount);

					return $buffer;
			}

			function peek($byteCount)
			{
				$buffer='';
				$size=0;

				while($this->bytesAvailable() < $byteCount)
				{
						;
				}

				$buffer = substr($this->internalBuffer,0,$byteCount);

				return $buffer;
			}

		}

		class PlayerKeyCommand
		{

			public $keys;
			public $player;
			public $type;

			public function parse($socket)
			{
				$this->type = chr(unpack('C', $socket->peek(1))[1]);

				if($this->type != 'I')
					return false;

				$socket->read(1);

				$this->keys = array();

				$keysbin = unpack('v', $socket->read(2))[1];

				//Player 1
				if($keysbin & 32768) $this->keys[] = XboxKey::P1_ARROW_RIGHT;
				if($keysbin & 16384) $this->keys[] = XboxKey::P1_ARROW_LEFT;
				if($keysbin & 8192)  $this->keys[] = XboxKey::P1_ARROW_UP;
				if($keysbin & 4096)  $this->keys[] = XboxKey::P1_ARROW_DOWN;
				if($keysbin & 2048)  $this->keys[] = XboxKey::P1_X;
				if($keysbin & 1024)  $this->keys[] = XboxKey::P1_Y;
				if($keysbin & 512)   $this->keys[] = XboxKey::P1_A;
				if($keysbin & 256)   $this->keys[] = XboxKey::P1_B;

				//Player 2
				if($keysbin & 128) $this->keys[] = XboxKey::P2_ARROW_RIGHT;
				if($keysbin & 64)  $this->keys[] = XboxKey::P2_ARROW_LEFT;
				if($keysbin & 32)  $this->keys[] = XboxKey::P2_ARROW_UP;
				if($keysbin & 16)  $this->keys[] = XboxKey::P2_ARROW_DOWN;
				if($keysbin & 8)   $this->keys[] = XboxKey::P2_X;
				if($keysbin & 4)   $this->keys[] = XboxKey::P2_Y;
				if($keysbin & 2)   $this->keys[] = XboxKey::P2_A;
				if($keysbin & 1)   $this->keys[] = XboxKey::P2_B;

				XboxKey::$keys = $this->keys;

				return true;
			}
		}

		class AckCommand
		{

			public $gameid;
			public $type;

			public function parse($socket)
			{
				$this->type = chr(unpack('C', $socket->peek(1))[1]);

				if($this->type != 'A')
					return false;

				$socket->read(1);


				$data = $socket->read(1);

				$this->gameid = unpack('C', $data)[1];

				echo "Game ID : ".$this->gameid."\n";

				return true;
			}
		}

		class GoCommand
		{
			public $type;

			public function parse($socket)
			{
				$this->type = chr(unpack('C', $socket->peek(1))[1]);

				if($this->type != 'G')
					return false;

				$socket->read(1);

				return true;
			}
		}

		class StopCommand
		{
			public $type;

			public function parse($socket)
			{
				$this->type = chr(unpack('C', $socket->peek(1))[1]);

				if($this->type != 'S')
					return false;

				$socket->read(1);

				return true;
			}
		}


		class LaserGame
		{
			const HOST = '127.0.0.1';
			const PORT = 4242;

			//Game management
			private $gameid;
			private $gamename;
			private $sock;
			private $stopped;
			//Display
			private $multiplicator;
			private $color;
			//Framerate control
			private $fps;
			private $frame_durata;
			private $ticks_base;
			private $frame_start_ticks;

			public function __construct($gameName)
			{
				$this->gameid = 0;
				$this->gamename = $gameName;
				$this->stopped = true;
				$this->multiplicator = 0;
				$this->color = LaserColor::LIME; //Because it's awesome

				$this->sock = socket_create(AF_INET, SOCK_DGRAM, 0);
				socket_set_nonblock($this->sock);

				$cmd = pack('C', 0) . pack('Z*', 'H' . $this->gamename);
				$this->sendCMD($cmd);

				$this->socket = new Socket($this->sock);
			}

			private function sendCMD($binary)
			{
				socket_sendto($this->sock, $binary, strlen($binary), 0, self::HOST, self::PORT);
			}

			public function setResolution($px)
			{
				$this->resolution = $px;

				$this->multiplicator = floor(65535/$px);

				return $this;
			}

			public function setDefaultColor($color)
			{
				$this->color = $color;

				return $this;
			}

			public function setFramerate($fps)
			{
				$this->fps = $fps;

				$this->frame_durata = 1000 / $fps;

				$this->ticks_base = gettimeofday(true);

				if($fps > 20)
					echo '[EdgeLaserPHP] WARNING - It is NOT recommended to set a framerate higher than 20~25 FPS.' . PHP_EOL;

				return $this;
			}

			public function newFrame()
			{
				$this->frame_start_ticks = $this->getTicks();
			}

			public function endFrame()
			{
				usleep(($this->frame_durata - ($this->getTicks() - $this->frame_start_ticks)) * 1000);
			}

			public function getTicks()
			{
				$diff = gettimeofday(true) - $this->ticks_base;
				$diff = explode('.', $diff);
				$diff = $diff[0] * 1000 + substr($diff[1], 0, 3);
				return $diff;
			}

			public function isStopped()
			{
				return $this->stopped;
			}

			public function receiveServerCommands()
			{
				$commands = array();

				if(!$this->socket->bytesAvailable())
				{
					return $commands;
				}

				foreach(array(
					'EdgeLaser\PlayerKeyCommand',
					'EdgeLaser\GoCommand',
					'EdgeLaser\StopCommand',
					'EdgeLaser\AckCommand')
				as $class)
				{
					$inst = new $class();

					if ($inst->parse($this->socket))
					{

						switch($inst->type)
						{
							case 'A' : $this->gameid = $inst->gameid; break;
							case 'G' : $this->stopped = false; break;
							case 'S' : $this->stopped = true; break;
							default: $commands[]=$inst;
						}

						break;
					}
				}

				return $commands;
			}

			public function addLine($x1, $y1, $x2, $y2, $color = null)
			{
				$x1 = $x1 > $this->resolution ? $this->resolution : $x1; $x2 = $x2 > $this->resolution ? $this->resolution : $x2; $y1 = $y1 > $this->resolution ? $this->resolution : $y1; $y2 = $y2 > $this->resolution ? $this->resolution : $y2;
				$x1 = $x1 < 0 ? 0 : $x1; $x2 = $x2 < 0 ? 0 : $x2; $y1 = $y1 < 0 ? 0 : $y1; $y2 = $y2 < 0 ? 0 : $y2;

				$m = $this->multiplicator;
				$color = is_null($color) ? $this->color : $color;

				$x1 = pack('v', $x1*$m);
				$y1 = pack('v', $y1*$m);
				$x2 = pack('v', $x2*$m);
				$y2 = pack('v', $y2*$m);
				$color = pack('C', $color);

				$cmd = pack('C', $this->gameid) . 'L' . $x1 . $y1 . $x2 . $y2 . $color;
				$this->sendCMD($cmd);

				return $this;
			}

			public function addCircle($x, $y, $diameter, $color = null)
			{
				$x = $x > $this->resolution ? $this->resolution : $x; $y = $y > $this->resolution ? $this->resolution : $y;
				$x = $x < 0 ? 0 : $x; $y = $y < 0 ? 0 : $y;

				$m = $this->multiplicator;
				$color = is_null($color) ? $this->color : $color;

				$x = pack('v', $x*$m);
				$y = pack('v', $y*$m);
				$diameter = pack('v', $diameter*$m);
				$color = pack('C', $color);

				$cmd = pack('C', $this->gameid) . 'C' . $x . $y . $diameter . $color;
				$this->sendCMD($cmd);

				return $this;
			}

			public function addRectangle($x1, $y1, $x2, $y2, $color = null)
			{
				$x1 = $x1 > $this->resolution ? $this->resolution : $x1; $x2 = $x2 > $this->resolution ? $this->resolution : $x2; $y1 = $y1 > $this->resolution ? $this->resolution : $y1; $y2 = $y2 > $this->resolution ? $this->resolution : $y2;
				$x1 = $x1 < 0 ? 0 : $x1; $x2 = $x2 < 0 ? 0 : $x2; $y1 = $y1 < 0 ? 0 : $y1; $y2 = $y2 < 0 ? 0 : $y2;

				$m = $this->multiplicator;
				$color = is_null($color) ? $this->color : $color;

				$x1 = pack('v', $x1*$m);
				$y1 = pack('v', $y1*$m);
				$x2 = pack('v', $x2*$m);
				$y2 = pack('v', $y2*$m);
				$color = pack('C', $color);

				$cmd = pack('C', $this->gameid) . 'D' . $x1 . $y1 . $x2 . $y2 . $color;
				$this->sendCMD($cmd);

				return $this;
			}

			public function refresh()
			{
				$cmd = pack('C', $this->gameid) . 'R';
				$this->sendCMD($cmd);

				return $this;
			}

			public function pause()
			{
				$cmd = pack('C', $this->gameid) . 'S';
				$this->sendCMD($cmd);

				return $this;
			}
		}

		class LaserFont
		{
			private $letters;
			private $spacing;

			public function __construct($filename)
			{
				$data = explode("\0", trim(gzuncompress(file_get_contents($filename)), "\0"));

				echo '[EdgeLaserPHP] Font ' . basename($filename) . ' data loaded, now parsing.' . PHP_EOL;

				$header = true;
				foreach ($data as $line)
				{
					if($header)
					{
						$this->spacing = unpack('C', $line)[1];
						$header = false;
						continue;
					}

					$chars = str_split($line);
					
					$charnum = 0;
					$line = 0;
					$create_line = false;

					$letter = true;
					foreach ($chars as $char)
					{
						if($letter)
						{
							$lettername = chr(unpack('C', $char)[1]);
							$letter = false;
							continue;
						}

						if($create_line)
						{
							$line++;
							$create_line = false;
						}

						$letter_lines[$lettername][$line][] = unpack('C', $char)[1];

						if($charnum++ == 3)
						{
							$create_line = true;
							$charnum = 0;
						}
					}
				}

				$this->letters = $letter_lines;

				echo '[EdgeLaserPHP] Font ' . basename($filename) . ' ready-to-use, ' . (count($data) - 1) . ' chars, ' . $this->spacing . ' spacing' . PHP_EOL;
			}

			public function render(LaserGame $ctx, $text, $x, $y, $color, $coeff)
			{
				foreach(str_split($text) as $char)
				{
					if(isset($this->letters[$char]))
					{
						$xmax = 0;

						foreach ($this->letters[$char] as $line)
						{
							if($line[2] > $xmax) $xmax = $line[2];

							$ctx->addLine($line[0]*$coeff+$x, $line[1]*$coeff+$y, $line[2]*$coeff+$x, $line[3]*$coeff+$y, $color);
						}

						$x += $xmax*$coeff + $this->spacing*$coeff;
					}
					elseif ($char == ' ')
					{
						$x += $this->spacing * 2 * $coeff;
					}
				}
			}
		}

		abstract class LaserColor
		{
			const RED = 0x1;
			const LIME = 0x2;
			const GREEN = 0x2;
			const YELLOW = 0x3;
			const BLUE = 0x4;
			const FUCHSIA = 0x5;
			const CYAN = 0x6;
			const WHITE = 0x7;
		}

		abstract class XboxKey
		{
			public static $keys = array();

			const P1_ARROW_LEFT = 0x1;
			const P1_ARROW_RIGHT = 0x2;
			const P1_ARROW_UP = 0x3;
			const P1_ARROW_DOWN = 0x4;
			const P1_X = 0x5;
			const P1_Y = 0x6;
			const P1_A = 0x7;
			const P1_B = 0x8;

			const P2_ARROW_LEFT = 0x9;
			const P2_ARROW_RIGHT = 0x10;
			const P2_ARROW_UP = 0x11;
			const P2_ARROW_DOWN = 0x12;
			const P2_X = 0x13;
			const P2_Y = 0x14;
			const P2_A = 0x15;
			const P2_B = 0x16;

			public static function getKeys()
			{
				return self::$keys;
			}
		}
	}

?>
