var simulator;
var simctx;
var draws = [];
var socket;
var connected = false;
var nrefresh = 0;
var dm = 65535/500;

$(document).ready(function() {

	simulator = document.getElementById("simulator");
	simctx = simulator.getContext("2d");
	simctx.lineWidth = 2;

});


function createWebSocket(host) {
	if(window.MozWebSocket) {
		window.WebSocket=window.MozWebSocket;
	}
	if(!window.WebSocket) {
		log('Votre navigateur ne supporte pas les websockets !');
		return false;
	}
	else {
		socket = new WebSocket(host);

		socket.onopen = function() { log('Socket is ready'); connected = true; }
		socket.onclose = function() { log('Socket closed'); connected = false; }
		socket.onerror = function(error) { log('Socket error') }

		socket.onmessage = function(msg) {

			action = JSON.parse(msg.data);

			switch(action.type) {

				case 'UpdateClients':
					updateClients(action.clients);
					break;

				case 'UpdateFPS':
					$('#fps_serv').html(action.rate);
					break;

				case 'RefreshScreen':
					refresh();
					break;

				case 'SelfStoppedGame':
					log(action.gameName + ' #' + action.gameId + ' has stopped');
					break;

				case 'Draw':
					draws.push(action);
					break;

			}

		}
	}

	setTimeout(waitServer, 200);
}

createWebSocket('ws://127.0.0.1:4244');

function waitServer() {

	if(socket.readyState == 1)
		sendMessage({
			type: 'SimulatorReady'
		});
	else
		setTimeout(waitServer, 200);

}


function sendMessage(data) {

	if(typeof data == 'object') {
		data = JSON.stringify(data);
	}

	socket.send(data);
}


function updateClients(clients) {

	$('#clients').html('');

	for(i = 0; i < clients.length; i++) {

		$('#clients').html($('#clients').html() + '<a href="javascript:play(' + clients[i].gameId + ')" title="' + clients[i].address + ':' + clients[i].port + '">' + clients[i].gameName + '@' + clients[i].gameId + '</a> ');

	}

	log('Updated clients list (' + clients.length + ' clients)');

}


function play(id) {

	if(connected) {
		sendMessage({
			type: 'CallGame',
			gameId: id
		});

		log('Attempting to run game #' + id);
	} else {
		log('Socket closed. If the server is running, please refresh the page.');
	}

}


function pause() {

	if(connected) {
		sendMessage({
			type: 'PauseCurrentGame'
		});

		log('Attempting to pause current game');
	} else {
		log('Socket closed. If the server is running, please refresh the page.');
	}

}


function refresh() {

	simctx.clearRect(0, 0, simulator.width, simulator.height);

	for(var i = 0; i < draws.length; i++) {

		action = draws[i];

		simctx.strokeStyle = action.color;

		switch(action.element) {

			case 'Line':
				simctx.beginPath();
				simctx.moveTo(action.x1/dm, action.y1/dm);
				simctx.lineTo(action.x2/dm, action.y2/dm);
				simctx.stroke();
				break;

			case 'Rectangle':
				simctx.beginPath();
				simctx.rect(action.x1/dm, action.y1/dm, (action.x2-action.x1)/dm, (action.y2-action.y1)/dm);
				simctx.stroke();
				break;

			case 'Circle':
				simctx.beginPath();
				simctx.arc(action.x/dm, action.y/dm, action.diameter/2/dm, 0, 2*Math.PI);
				simctx.stroke();
				break;

		}

	}

	draws = [];
	nrefresh++;

}


//Keylogger
var keys = {};
var keys_precedent = {};

$(document).keydown(function (e) {
	keys_precedent = clone(keys);
	keys[e.which] = true;

	if(JSON.stringify(keys) !== JSON.stringify(keys_precedent)) {
		sendMessage({
			type: 'KeyStroke',
			keys: keys
		});
	}
});

$(document).keyup(function (e) {
	delete keys[e.which];
	sendMessage({
		type: 'KeyStroke',
		keys: keys
	});
});


//Log message on pseudo-teletype
function log(msg) {
	$('#log').html($('#log').html() + msg + '<br>');
	$("#log").animate({ scrollTop: $("#log").prop("scrollHeight") }, 10);
}


//Get framerate
setInterval(function() {

	$('#fps_simu').html(nrefresh*2);
	nrefresh = 0;

}, 500);


//JavaScript is soooooo limited :'(((((
//This is the fastest way to clone an object :/
function clone(obj) {

	var target = {};

	for (var i in obj) {
		if (obj.hasOwnProperty(i)) {
			target[i] = obj[i];
		}
	}

	return target;

}