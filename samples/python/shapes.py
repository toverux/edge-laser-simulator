import EdgeLaser
import time

game = EdgeLaser.LaserGame('SuperTetris')

game.setResolution(500).setDefaultColor(EdgeLaser.LaserColor.LIME)

coeff = 0

posx = 450

posy = 450

while game.isStopped():
    game.receiveServerCommands()


while not game.isStopped():

    commands = game.receiveServerCommands()

    # for cmd in commands:

    if game.player1_keys:
        if game.player1_keys.yp  :
            posy-=5
        if game.player1_keys.xn :
            posx-=5
        if game.player1_keys.yn :
            posy+=5
        if game.player1_keys.xp :
            posx+=5

    game.addRectangle(posx, posy, posx+10, posy+10, EdgeLaser.LaserColor.RED)

    coeff = 0 if coeff > 499 else coeff + 4

    game.addLine(250, 0, coeff, 250, EdgeLaser.LaserColor.CYAN) \
	    .addLine(250, 500, coeff, 250, EdgeLaser.LaserColor.CYAN) \
	    .addCircle(250, 250, coeff, EdgeLaser.LaserColor.FUCHSIA) \
	    .addRectangle(10, 10, coeff, coeff) \
		.refresh()
    time.sleep(0.05)
