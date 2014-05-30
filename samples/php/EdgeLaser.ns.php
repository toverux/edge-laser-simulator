<?php

	/* EdgeLaserPHP
	 * Version: 0.1
	 * Author: Morgan Touverey-Quilling (morgan-linux) */

	namespace EdgeLaser
	{
		class LaserGame
		{
			const HOST = '127.0.0.1';
			const PORT = 4242;

			private $gameid;
			private $gamename;
			private $sock;
			private $stopped;
			private $multiplicator;
			private $color;

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
			}

			private function sendCMD($binary)
			{
				socket_sendto($this->sock, $binary, strlen($binary), 0, self::HOST, self::PORT);
			}

			public function setResolution($px)
			{
				$this->multiplicator = floor(65535/$px);

				return $this;
			}

			public function setDefaultColor($color)
			{
				$this->color = $color;

				return $this;
			}

			public function isStopped()
			{
				return $this->stopped;
			}

			public function receiveServerCommands()
			{
				$reply = null;
				socket_recv($this->sock, $reply, 4096, MSG_WAITALL);

				if(!is_null($reply))
				{
					switch(chr(unpack('C', $reply)[1]))
					{
						case 'A':
							$this->gameid = unpack('C2', $reply)[2];
							echo "Received ID " . $this->gameid . "\n";
							break;

						case 'G':
							$this->stopped = false;
							echo "Received 'GO' order !\n";
							break;

						case 'S':
							$this->stopped = true;
							echo "Received 'STOP' order !\n";
							break;
					}
				}

				return $this;
			}

			public function addLine($x1, $y1, $x2, $y2, $color = null)
			{
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
	}

?>