#!/usr/bin/php

<?php

	echo 'PHP ELF Font Compiler v.1' . PHP_EOL;

	if(isset($argv[1]))
	{
		$file = $argv[1];

		$fontcsv = file_get_contents($file);

		$newfile = explode('.', $file)[0] . '.elfc';
		@unlink($newfile);

		$fontdatas = explode("\n", $fontcsv);

		$bindata = '';

		$header = true;

		foreach ($fontdatas as $line)
		{
			$line = explode(';', $line);

			if($header)
			{
				foreach ($line as $opt)
				{
					list($key, $val) = explode('=', $opt);
					
					switch ($key) {
						case 'font':
							echo ' - Font name: ' . $val . PHP_EOL;
							break;

						case 'author':
							echo ' - Font author: ' . $val . PHP_EOL;
							break;

						case 'spacing':
							$bindata = $bindata . pack('C', $val) . "\0";
							echo ' - Char spacing: ' . $val . PHP_EOL;
							break;
						
						default:
							echo ' - Unrecognized option: ' . $key . PHP_EOL;
							break;
					}
				}

				echo ' - Number of chars: ' . (count($fontdatas)-1) . PHP_EOL . PHP_EOL;

				$header = false;
				continue;
			}

			$chr = true;
			foreach ($line as $data)
			{
				if($chr)
				{
					echo '  Compiling char \'' . $data . '\'...' . PHP_EOL;

					$bindata = $bindata . pack('C', ord($data));

					$chr = false;
					continue;
				}

				$bindata = $bindata . pack('C', $data);
			}

			$bindata = $bindata . "\0";
		}

		echo PHP_EOL;

		echo 'Writing ELFC file...' . PHP_EOL . PHP_EOL;
		$bindata = gzcompress($bindata, 9);
		file_put_contents($newfile, $bindata, FILE_APPEND);

		$fszold = filesize($file);
		$fsznew = filesize($newfile);
		$deflated = 100 - floor(100 * $fsznew / $fszold);

		echo 'Compilation terminated:' . PHP_EOL;
		echo ' - Uncompilated : ' . $fszold . ' B' . PHP_EOL;
		echo ' - Compilated   : ' . $fsznew . ' B (deflated ' . $deflated . '%)' . PHP_EOL;
	}

	else
	{
		echo 'Usage: ./makefont.php filename' . PHP_EOL;
	}

?>