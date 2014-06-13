<?php

	/* EdgeLaserPHP
	 * Version: 0.1
	 * Author: Morgan Touverey-Quilling (morgan-linux) */

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

			public function bytesAvail()
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
					// echo "Got $read from socket\n";
				}

			}

			public function read($byteCount)
			{
					$buffer='';
					$size=0;

					// echo "want to read $byteCount\n";

					while($this->bytesAvail() < $byteCount)
					{
							;
					}

					$buffer = substr($this->internalBuffer,0,$byteCount);
					$this->internalBuffer = substr($this->internalBuffer,$byteCount);

					// echo "read : '".$buffer."'\n";

					return $buffer;
			}

			function peek($byteCount)
			{
				$buffer='';
				$size=0;

				// echo "want to peek $byteCount\n";

				while($this->bytesAvail() < $byteCount)
				{
						;
				}

				$buffer = substr($this->internalBuffer,0,$byteCount);

				// echo "peek : '".$buffer."'\n";

				return $buffer;
			}

		}

		class AbstractCommand
		{
			public function parse($socket)
			{
				return false;
			}
		}

		class PlayerKeyCommand
		{

			public $key;
			public $player;
			public $type;

			public function parse($socket)
			{
				$this->type = chr(unpack('C', $socket->peek(1))[1]);

				if($this->type!='K')
				{
					return false;
				}

				$socket->read(1);

				$data=$socket->read(2);

				$this->player = unpack('C1', $data[0]);
				$this->key = unpack('C1', $data[1]);

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

				if($this->type!='A')
				{
					return false;
				}

				$socket->read(1);


				$data=$socket->read(1);

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

				if($this->type!='G')
				{
					return false;
				}

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

				if($this->type!='S')
				{
					return false;
				}

				$socket->read(1);

				return true;
			}
		}

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

				$this->socket = new Socket($this->sock);

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
				$commands = array();

				// echo "1";

				if(!$this->socket->bytesAvail())
				{
					return $commands;
				}

				foreach(array('EdgeLaser\PlayerKeyCommand', 'EdgeLaser\GoCommand', 'EdgeLaser\StopCommand', 'EdgeLaser\AckCommand') as $class)
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
