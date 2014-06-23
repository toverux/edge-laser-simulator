var logger = require('./Logger.js');
var simulator = require('./Simulator.js');
var clientManager = require('./Client.js');
var server;
var currentClient = null;
var nrefresh = 0;


exports.setServer = function(socket) {
	server = socket;
}


exports.parse = function(rawaction) {

	gameId = rawaction.readUInt8(0);
	action = rawaction.slice(1, 2).toString('ascii');
	parameters = rawaction.slice(2, rawaction.length);

	switch(action) {

		case 'H':
			return {
				type: 'AskingForId',
				gameId: gameId,
				gameName: parameters.slice(0, rawaction.length-3).toString('ascii'),
				closingChar: rawaction.readUInt8(rawaction.length-1)
			}
			break;

		case 'L':
			return {
				type: 'Draw',
				element: 'Line',
				gameId: gameId,
				x1: parameters.readUInt16LE(0),
				y1: parameters.readUInt16LE(2),
				x2: parameters.readUInt16LE(4),
				y2: parameters.readUInt16LE(6),
				color: getColorName(parameters.readUInt8(8))
			}
			break;

		case 'D':
			return {
				type: 'Draw',
				element: 'Rectangle',
				gameId: gameId,
				x1: parameters.readUInt16LE(0),
				y1: parameters.readUInt16LE(2),
				x2: parameters.readUInt16LE(4),
				y2: parameters.readUInt16LE(6),
				color: getColorName(parameters.readUInt8(8))
			}
			break;

		case 'C':
			return {
				type: 'Draw',
				element: 'Circle',
				gameId: gameId,
				x: parameters.readUInt16LE(0),
				y: parameters.readUInt16LE(2),
				diameter: parameters.readUInt16LE(4),
				color: getColorName(parameters.readUInt8(6))
			}
			break;

		case 'R':
			return {
				type: 'RefreshScreen',
				gameId: gameId
			}
			break;

		case 'S':
			return {
				type: 'SelfStop',
				gameId: gameId
			}
			break;

		default:
			return {
				type: 'UnknownAction',
				gameId: gameId,
				action: action
			}
			break;

	}

}


function getColorName(colorCode) {

	switch(colorCode)
	{
		case 1: return 'red'
		case 2: return 'lime'
		case 3: return 'yellow'
		case 4: return 'blue'
		case 5: return 'fuchsia'
		case 6: return 'cyan'
		case 7: return 'white'
	}

}


exports.execute = function(action, client) {
	
	if(client == null)
		client = currentClient;

	switch(action.type) {

		case 'AskingForId':
			client.gameName = action.gameName;

			simulator.sendAction({
				type: 'UpdateClients',
				clients: clientManager.getClients()
			});

			message = new Buffer(2);
			message.write('A', 0, 1, 'ascii');
			message.writeUInt8(client.gameId, 1);
			server.send(message, 0, message.length, client.port, client.address);
			
			logger.log('New game "' + client.gameName + '" created with ID ' + client.gameId);
			break;

		case 'RefreshScreen':
			nrefresh++;
			simulator.sendAction(action);
			break;

		case 'Draw':
			simulator.sendAction(action);
			break;

		case 'SelfStop':
			client.status = 'Paused';
			simulator.sendAction({
				type: 'SelfStoppedGame',
				gameId: client.gameId,
				gameName: client.gameName
			});
			currentClient = null;
			break;

		case 'KeyStroke':
			if(client != null) {

				message = new Buffer(3);
				message.write('I', 0, 1, 'ascii');
				message.writeUInt16LE(action.keysbin, 1);
				server.send(message, 0, message.length, client.port, client.address);

			}

			else logger.log('No client currently running - not sending your keystrokes');
			break;

	}

}


exports.startGame = function(gameId) {

	client = clientManager.get(gameId);
	currentClient = client;

	client.status = 'Running';
	message = new Buffer(1);
	message.write('G', 0, 1, 'ascii');
	server.send(message, 0, message.length, client.port, client.address);

	logger.log('INFO ' + client.gameName + ' is now running');

}


exports.stopGame = function(gameId) {

	client = clientManager.get(gameId);
	currentClient = null;

	message = new Buffer(1);
	message.write('S', 0, 1, 'ascii');
	server.send(message, 0, message.length, client.port, client.address);
	client.status = 'Paused';

	logger.log('INFO ' + client.gameName + ' has been stopped');

}


exports.callGame = function(gameId) {

	if(currentClient != null)
		exports.stopGame(currentClient.gameId);

	exports.startGame(gameId);

}


exports.pause = function() {

	if(currentClient != null)
		exports.stopGame(currentClient.gameId);
	else
		logger.log('INFO No game running, no game to pause')

}


setInterval(function() {

	simulator.sendAction({
		type: 'UpdateFPS',
		rate: (nrefresh*2)
	});

	if(nrefresh*2 <= 4 && currentClient)
		logger.log('WARNING: under 4 frames/s your game will be stopped', 'protocol_error');

	nrefresh = 0;

}, 500);