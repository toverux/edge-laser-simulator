exports.log = function (msg, type) {

	if(typeof msg == 'object')
		msg = JSON.stringify(msg).yellow;

	switch(type)
	{
		case 'protocol_error':
			console.log('[SERV] '.yellow + 'PROTOCOL'.red.inverse + ' ' + msg.red);
			break;
		default:
			console.log(('[SERV] ' + msg).yellow);
			break;
	}
}