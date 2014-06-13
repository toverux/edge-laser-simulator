var WebSocketServer = require('websocket').server;
var http = require('http');
var actionManager = require('./Action.js');
var clientManager = require('./Client.js');

var server = http.createServer(function(request, response) {});

server.listen(4244, function() {});

wsServer = new WebSocketServer({
	httpServer: server
});

log('Simulator websocket server listening on 127.0.0.1:4244');

var connection;

wsServer.on('request', function(request) {
	connection = request.accept(null, request.origin);

	exports.sendAction({
		type: 'UpdateClients',
		clients: clientManager.getClients()
	});

	connection.on('message', function(message) {

		action = JSON.parse(message.utf8Data);

		switch(action.type)
		{
			case 'SimulatorReady':
				log('Simulator is ready');
				break;

			case 'CallGame':
				actionManager.callGame(action.gameId);
				break;

			case 'PauseCurrentGame':
				actionManager.pause();
				break;

			default:
				log('DATA ' + JSON.stringify(action))
		}

	});

	connection.on('close', function(connection) {

		log('Laser simulator disconnected');

	});
});


exports.sendAction = function(action) {

	if(connection)
	{
		json = JSON.stringify(action);
		connection.send(json);

		if(action.type != 'UpdateFPS')
			log('SEND ' + json);
	}

}


function log(msg)
{
	if(typeof msg == 'object')
		msg = JSON.stringify(msg).grey;

	console.log(('[SIMU] ' + msg).grey);
}