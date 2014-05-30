var logger = require('./Logger.js');

exports.validate = function(action, client) {

	switch(action.type) {

		case 'AskingForId':
			if(action.closingChar == 0) {
				if(client.status == 'Registering') return true;
				else logger.log('Illegal Action (AskingForId while not Registering)', 'protocol_error');
			} else {
				logger.log('Bad Format - Game name string must end with \\0', 'protocol_error');
			}
			break;

		case 'Draw':
			if(client.status == 'Registering') {
				logger.log('Illegal Action (Game is not registered)', 'protocol_error')
			} else {
				if(client.status == 'Running') return true;
				else logger.log('Illegal Action (Game paused)', 'protocol_error');
			}
			break;

		case 'RefreshScreen':
			if(client.status == 'Registering') {
				logger.log('Illegal Action (Game is not registered)', 'protocol_error')
			} else {
				if(client.status == 'Running') return true;
				else logger.log('Illegal Action (Game paused)', 'protocol_error');
			}
			break;

		case 'SelfStop':
			if(client.status == 'Registering') {
				logger.log('Illegal Action (Game is not registered)', 'protocol_error')
			} else {
				if(client.status == 'Running') return true;
				else logger.log('Illegal Action (Game paused)', 'protocol_error');
			}
			break;

		case 'UnknownAction':
			logger.log('Unknown Action "' + action.action + '"', 'protocol_error');
			break;

	}

	return false;

}