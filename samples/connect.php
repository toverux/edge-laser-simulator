<?php

	$gamename = 'SuperTetris';
	$server = '127.0.0.1';
	$port = 4242;

	$sock = socket_create(AF_INET, SOCK_DGRAM, 0);
	socket_set_nonblock($sock);

	$cmd = pack('C', 0) . pack('Z*', 'H' . $gamename);

	socket_sendto($sock, $cmd, strlen($cmd), 0, $server, $port);

	$stopped = true;
	$gameId = 0;
	$coeff = 0;

	while(true)
	{
		$reply = null;
		socket_recv($sock, $reply, 4096, MSG_WAITALL);

		if(!is_null($reply))
		{
			switch(chr(unpack('C', $reply)[1]))
			{
				case 'A':
					$gameId = unpack('C2', $reply)[2];
					echo "Received ID " . $gameId . "\n";
					break;

				case 'G':
					$stopped = false;
					echo "Received 'GO' order !\n";
					break;

				case 'S':
					$stopped = true;
					echo "Received 'STOP' order !\n";
					break;
			}
		}

		if(!$stopped)
		{
			$coeff++;
			usleep(200000); //Some calculations

			$cmd = pack('C', $gameId) . 'R';
			socket_sendto($sock, $cmd, strlen($cmd), 0, $server, $port);

			for($i = 0; $i < 8; $i++)
			{
				$fposx = pack('v', (($i+$coeff)*5)*131);
				$fposy = pack('v', 5*131);
				$sposx = pack('v', (($i+$coeff)*5+25)*131);
				$sposy = pack('v', 200*131);
				$cmd = pack('C', $gameId) . 'L' . $fposx . $fposy . $sposx . $sposy;
				socket_sendto($sock, $cmd, strlen($cmd), 0, $server, $port);
			}
		}
	}

?>