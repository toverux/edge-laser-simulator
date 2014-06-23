exports.format = function(action) {

	keysbin = 0;

	for (var key in action.keys) {

			switch(key) {

				//Player 1
				case '68': keysbin = (keysbin | 32768);  break; //X+
				case '81': keysbin = (keysbin | 16384);  break; //X-
				case '90': keysbin = (keysbin | 8192);   break; //Y-
				case '83': keysbin = (keysbin | 4096);   break; //Y+
				case '70': keysbin = (keysbin | 2048);   break; //X
				case '84': keysbin = (keysbin | 1024);   break; //Y
				case '71': keysbin = (keysbin | 512);    break; //A
				case '72': keysbin = (keysbin | 256);    break; //B

				//Player 2
				case '76': keysbin = (keysbin | 128); break; //X+
				case '74': keysbin = (keysbin | 64);  break; //X-
				case '73': keysbin = (keysbin | 32);  break; //Y-
				case '75': keysbin = (keysbin | 16);  break; //Y+
				case '100': case '37': keysbin = (keysbin | 8); break; //X
				case '104': case '38': keysbin = (keysbin | 4); break; //Y
				case '101': case '12': keysbin = (keysbin | 2); break; //A
				case '102': case '39': keysbin = (keysbin | 1); break; //B

			}

	}

	return {
		type: 'KeyStroke',
		keysbin: keysbin
	};

}