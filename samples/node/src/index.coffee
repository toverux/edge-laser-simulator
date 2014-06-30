HOST = '127.0.0.1'
PORT = 4242

dgram = require 'dgram'
client = dgram.createSocket 'udp4'

class Message
	constructor: (@game) ->

	send: (msg) ->		
		client.send msg, 0, msg.length, PORT, HOST	

	hello: () -> 
		console.log '[SAY] hello'
		@send(new Buffer '\0H' + @game.name + '\0')

	addCircle: (x, y, diameter, color) ->		
		message = new Buffer 24
		message.writeUInt8(@game.id, 0)
		message.write("C", 1, 1, "ascii")
		message.writeUInt16LE(x, 2)
		message.writeUInt16LE(y, 4)
		message.writeUInt16LE(diameter, 6)
		message.writeUInt8(color, 8)
		@send(message)

	addLine: (x1, y1, x2, y2, color) ->		
		message = new Buffer 24
		message.writeUInt8(@game.id, 0)
		message.write("L", 1, 1, "ascii")
		message.writeUInt16LE(x1, 2)
		message.writeUInt16LE(y1, 4)
		message.writeUInt16LE(x2, 6)
		message.writeUInt16LE(y2, 8)
		message.writeUInt8(color, 10)
		@send(message)

	addTxt: (x, y, size, color, text) ->
		message = new Buffer 24
		message.writeUInt8(@game.id, 0)
		message.write("T", 1, 1, "ascii")
		message.writeUInt16LE(x, 2)
		message.writeUInt16LE(y, 4)
		message.writeUInt8(color, 6)
		message.write(text + '\0', 7, text.length + 1, "ascii")
		@send(message)

	refresh: () ->
		message = new Buffer 24
		message.writeUInt8(@game.id, 0)
		message.write("R", 1, 1, "ascii")
		@send(message)	
		
	stop: ()->
		console.log '[SAY] stop'
		@send(new Buffer @game.id + 'S')

class Game
	constructor: (@name, @id) ->
		@msg = new Message(@)
		@stop = true
		@lastInteraction = null
		@color = 
			red : 0x1
			lime : 0x2
			green : 0x2
			yellow : 0x3
			blue : 0x4
			fuchsia : 0x5
			cyan : 0x6
			white : 0x7
		@colors = [
			0x1
			0x2
			0x2
			0x3
			0x4
			0x5
			0x6
			0x7
		]
		
		@board = [null, null, null, null, null, null , null, null, null]
		@isPlaying = false
		@states =
			p1:
				color: @color.green
				type: "o"
				cursor: 0
			p2:
				color: @color.red
				type: "x"
				cursor: 8
		@state = @states.p1

	start: () -> 
		console.log '[Start] ' + @name
		@msg.hello()

	stop: () -> 
		console.log '[Stop] ' + @name
		@msg.stop()
		@stop = true	

	delay: (ms, func) -> setTimeout func, ms

	drawGrid: ()->
		@msg.addLine(21845, 5000, 21845, 60535, @color.cyan)
		@msg.addLine(43690, 5000, 43690, 60535, @color.cyan)					
		@msg.addLine(5000, 21845, 60535, 21845, @color.cyan)
		@msg.addLine(5000, 43690, 60535, 43690, @color.cyan)

	isFull: () ->
		return @board[0]?  and 
		@board[1]? and  
		@board[2]? and  
		@board[3]? and  
		@board[4]? and  
		@board[5]? and  
		@board[6]? and  
		@board[7]? and  
		@board[8]? 

	play: () -> 
		@isPlaying = true
		game.stop = false
		displayWinner = 0
		limiteDisplayWinner = 50

		looplay = () =>
				if(!game.stop)					

					if @isWinner('x')
						if displayWinner < limiteDisplayWinner
							@TextXIsWin()
						else
							@board = [null, null, null, null, null, null , null, null, null]
							displayWinner = 0
						displayWinner++
					else if @isWinner('o')
						if displayWinner < limiteDisplayWinner
							@TextOIsWin()
						else
							@board = [null, null, null, null, null, null , null, null, null]
							displayWinner = 0
						displayWinner++
					else if @isFull()
						@board = [null, null, null, null, null, null , null, null, null]
					else						
						@drawGrid()

						for position in [0..8]
							switch @board[position]
								when "x" then @addCross(position)
								when "o" then @addCircle(position)
						
						@doSelect(@state.cursor, @state)

					@msg.refresh()
				else
					@board = [null, null, null, null, null, null , null, null, null]
		setInterval looplay, 150

	doSelect: (position, state) ->
		x = y = 0

		switch position
			when 1 then x = 21845  
			when 2 then x = 43690
			when 3 then y = 21845
			when 4 then x = 21845; y = 21845
			when 5 then x = 43690; y = 21845
			when 6 then y = 43690
			when 7 then x = 21845; y = 43690
			when 8 then x = 43690; y = 43690
		color = @colors[Math.floor(Math.random() * (@colors.length))]
		if state == @states.p1
			@msg.addCircle(10923 + x, 7000 + y, 1500, color)
			@msg.addLine(10923 + x, 12000 + y, 10923 + x, 9000 + y, color)
			@msg.addLine(10000 + x, 10000 + y, 10923 + x, 12000 + y, color)
			@msg.addLine(11846 + x, 10000 + y, 10923 + x, 12000 + y, color)
		else
			@msg.addLine(9923 + x, 6000 + y, 11923 + x, 8000 + y, color)
			@msg.addLine(11923 + x, 6000 + y, 9923 + x, 8000 + y, color)
			@msg.addLine(10923 + x, 12000 + y, 10923 + x, 9000 + y, color)
			@msg.addLine(10000 + x, 10000 + y, 10923 + x, 12000 + y, color)
			@msg.addLine(11846 + x, 10000 + y, 10923 + x, 12000 + y, color)

	TextXIsWin: ()->
		color = @colors[Math.floor(Math.random() * (@colors.length))]
		@addCross 4, color
	
	TextOIsWin: ()->
		color = @colors[Math.floor(Math.random() * (@colors.length))]
		@addCircle 4, color

	p1_right: ()->
		if @state.type == "o"
			if @state.cursor < 8				
				@state.cursor++
			else 
				@state.cursor = 0
				
	p1_left: ()-> 
		if @state.type == "o"
			if @state.cursor > 0
				@state.cursor--
			else 
				@state.cursor = 8
		
	p1_up: ()-> 
		if @state.type == "o"
			if @state.cursor > 2
				@state.cursor -= 3
			else 
				@state.cursor += 6
		
	p1_down: () -> 
		if @state.type == "o"
			if @state.cursor < 6
				@state.cursor += 3
			else 
				@state.cursor -= 6
		
	p1_select: ()->
		if @state.type == "o" and not @board[@state.cursor]?
			@board[@state.cursor] = "o"
			@state = @states.p2

	p2_right: ()->
		if @state.type == "x" 
			if @state.cursor < 8				
					@state.cursor++
			else 
				@state.cursor = 0
	
	p2_left: ()-> 
		if @state.type == "x"
			if @state.cursor > 0
				@state.cursor--
			else 
				@state.cursor = 8
	
	p2_up: ()-> 
		if @state.type == "x"
			if @state.cursor > 2
				@state.cursor -= 3
			else 
				@state.cursor += 6
	
	p2_down: () -> 
		if @state.type == "x"
			if @state.cursor < 6
				@state.cursor += 3
			else 
				@state.cursor -= 6

	p2_select: ()->
		if @state.type == "x" and not @board[@state.cursor]?
			@board[@state.cursor] = "x"
			@state = @states.p1

	addCross: (position, color) ->
		switch position
			when 0 then @msg.addLine(5923, 5923, 15923, 15923, color ? @color.red);@msg.addLine(15923, 5923, 5923, 15923, color ? @color.red)
			when 1 then @msg.addLine(27768, 5923, 37768, 15923, color ? @color.red);@msg.addLine(27768, 15923, 37768, 5923, color ? @color.red)
			when 2 then @msg.addLine(49613, 5923, 59613, 15923, color ? @color.red);@msg.addLine(49613, 15923, 59613, 5923, color ? @color.red) 
			when 3 then @msg.addLine(5923, 27768, 15923, 37768, color ? @color.red);@msg.addLine(15923, 27768, 5923, 37768, color ? @color.red)
			when 4 then @msg.addLine(27768, 27768, 37768, 37768, color ? @color.red);@msg.addLine(37768, 27768, 27768, 37768, color ? @color.red)
			when 5 then @msg.addLine(49613, 27768, 59613, 37768, color ? @color.red);@msg.addLine(49613, 37768, 59613, 27768, color ? @color.red)
			when 6 then @msg.addLine(5923, 49613, 15923, 59613, color ? @color.red);@msg.addLine(15923, 49613, 5923, 59613, color ? @color.red)
			when 7 then @msg.addLine(27768, 49613, 37768, 59613, color ? @color.red);@msg.addLine(37768, 49613, 27768, 59613, color ? @color.red)
			when 8 then @msg.addLine(49613, 49613, 59613, 59613, color ? @color.red);@msg.addLine(49613, 59613, 59613, 49613, color ? @color.red)
		
	addCircle: (position, color) ->
		switch position
			when 0 then @msg.addCircle(10923, 10923, 10000, color ? @color.green)
			when 1 then @msg.addCircle(32768, 10923, 10000, color ? @color.green)
			when 2 then @msg.addCircle(54613, 10923, 10000, color ? @color.green)
			when 3 then @msg.addCircle(10923, 32768, 10000, color ? @color.green)
			when 4 then @msg.addCircle(32768, 32768, 10000, color ? @color.green)
			when 5 then @msg.addCircle(54613, 32768, 10000, color ? @color.green)
			when 6 then @msg.addCircle(10923, 54613, 10000, color ? @color.green)
			when 7 then @msg.addCircle(32768, 54613, 10000, color ? @color.green)
			when 8 then @msg.addCircle(54613, 54613, 10000, color ? @color.green)
	
	interaction: (message) ->
		
		console.log message.readUInt16LE(1)

		switch message.readUInt16LE(1)
			when 32768 then @p1_right()
			when 16384 then @p1_left()
			when 8192 then @p1_up()
			when 4096 then @p1_down()
			when 2048 then @p1_select()
			when 128 then @p2_right()
			when 64 then @p2_left()
			when 32 then @p2_up()
			when 16 then @p2_down()
			when 8 then @p2_select()

	isWinner: (player) ->
		if player is @board[0] and player is @board[1] and player is @board[2] then return true
		if player is @board[3] and player is @board[4] and player is @board[5] then return true
		if player is @board[6] and player is @board[7] and player is @board[8] then return true
		if player is @board[0] and player is @board[3] and player is @board[6] then return true
		if player is @board[1] and player is @board[4] and player is @board[7] then return true
		if player is @board[2] and player is @board[5] and player is @board[8] then return true
		if player is @board[0] and player is @board[4] and player is @board[8] then return true
		if player is @board[2] and player is @board[4] and player is @board[6] then return true
		return false

game = new Game('Tic Tac Toe')
game.start()

client.on 'message',  (message, remote) ->
	console.log "[MSG] " + message + " " + message[1]
	switch String.fromCharCode(message[0])
		when "A" then game.id = message[1]
		when "G" then game.play()
		when "S" then game.stop = true
		when "I" then game.interaction(message)
		else console.log "UNKNOW MESSAGE -> " + message

client.on 'error',  (message, remote) ->
    console.log "[MSG] !!!!!! error -> " + message
    game.stop()


