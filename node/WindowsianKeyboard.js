var action = require('./Action.js');
var keypress = require('keypress');

exports.bind = function() {

	keypress(process.stdin);

	process.stdin.on('keypress', function (ch, key) {

		if (key && key.ctrl && key.name == 'c') {
			process.stdin.pause();
			process.exit(code=0);
		}

		if(key != undefined) {

			switch(key.name) {

				case 'return': keycode = 0; player = 0; break; //EXIT or SELECT

				//Player One
				case 'z': keycode = 1; player = 1; break;
				case 'q': keycode = 2; player = 1; break;
				case 's': keycode = 3; player = 1; break;
				case 'd': keycode = 4; player = 1; break;

				case 't': keycode = 5; player = 1; break;
				case 'f': keycode = 6; player = 1; break;
				case 'g': keycode = 7; player = 1; break;
				case 'h': keycode = 8; player = 1; break;

				//Player Two
				case 'i': keycode = 1; player = 2; break;
				case 'j': keycode = 2; player = 2; break;
				case 'k': keycode = 3; player = 2; break;
				case 'l': keycode = 4; player = 2; break;

				case 'up':    keycode = 5; player = 2; break; // {
				case 'left':  keycode = 6; player = 2; break; // 	This is on the numpad
				case 'clear': keycode = 7; player = 2; break; // 	De-activate num. lock
				case 'right': keycode = 8; player = 2; break; // }

				default: keycode = -1; break;

			}

			if(keycode > -1) {

				action.execute({
					type: 'KeyPress',
					player: player,
					keycode: keycode
				});

			}

		}

	});

	process.stdin.setRawMode(true);
	process.stdin.resume();

}