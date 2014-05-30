var dgram = require('dgram');
var logger = require('./Logger.js');
var colors = require('./Colors.js');
var simulator = require('./Simulator.js');
var clientManager = require('./Client.js');
var action = require('./Action.js');
var validator = require('./ActionValidator.js');

var server = dgram.createSocket('udp4');

var HOST = '127.0.0.1';
var PORT = 4242;


server.on('listening', function () {

	var address = server.address();
	logger.log('Server listening on ' + HOST +':'+ PORT);

	action.setServer(server);

});


server.on('message', function (message, remote) {

	clientaction = action.parse(message);

	client = clientManager.get(clientaction.gameId, remote.address, remote.port);

	logger.log('DATA ' + client.gameName + '#' + client.gameId + ' ' + message + ' // Decimal dump: ↵');
	logger.log(message);

	if(validator.validate(clientaction, client)) {
		action.execute(clientaction, client);
	}

});


server.bind(PORT, HOST);