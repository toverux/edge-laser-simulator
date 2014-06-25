var logger = require('./Logger.js');

var clients = [];

var baseGameId = 0;

exports.get = function(gameId, address, port) {

	for (var i = 0; i < clients.length; i++) {
		if(clients[i].gameId == gameId)
			return clients[i];
	}

	newclient = {
		address: address,
		port: port,
		status: 'Registering',
		gameName: 'Unknow',
		gameId: ++baseGameId
	}

	clients.push(newclient);

	logger.log('New client connected ' + address + ':' + port);

	return newclient;

};


exports.getClients = function() {

	return clients;

}