from __future__ import print_function
import sys
from gevent import socket
import math

from construct import *
from construct import macros

HOST="localhost"
PORT=4242

OneChar = Struct("OneChar", String("one",1))

class Socket(object):
    def __init__(self, socket):
        self.sock = socket
        self.internalbuffer = ""

    def bytesAvail(self):
        self.getFromSocket()
        return len(self.internalbuffer)

    def getFromSocket(self):
        tmp=None
        try :
            data, address = self.sock.recvfrom(65535, socket.MSG_DONTWAIT)
            self.internalbuffer+=data
        except socket.error as exc:
            if exc.errno == 35:
                pass
            #print "Got {} from socket".format(data);

    def read(self, byteCount):
        buffer=''
        size=0
        while self.bytesAvail() < byteCount:
            pass

        buffer = self.internalbuffer[0:byteCount]
        self.internalbuffer=self.internalbuffer[byteCount:]
        return buffer

    def peek(self, byteCount):
        buffer=''
        size=0
        while self.bytesAvail() < byteCount:
            pass

        buffer = self.internalbuffer[0:byteCount]
        return buffer


class AbstractCommand(object):
    def parse_type(self, data):
        parsed = OneChar.parse(data)

        # print ("Parsed : {}".format(parsed.one))

        return parsed.one

    def parse(self, socket, game):
        pass

class PlayerKeyCommand(AbstractCommand):

    def __init__(self):
        self.key = None
        self.player = None
        self.type = None


    def parse(self, socket, game):
        if not self.parse_type(socket.peek(1)) == 'I' :
            return False

        socket.read(1)

        data = socket.read(2)

        packet = PlayerKeyPacket.parse(data)

        self.player1 = packet.player1
        self.player2 = packet.player2

        # print("Player {} key {}".format(self.player, self.key))
        # print("Player 1 {}".format(self.player1))
        # print("Player 2 {}".format(self.player2))

        game.player1_keys = self.player1
        game.player2_keys = self.player2

        return True

AckPacket = Struct("AckPacket",
                                   ULInt8("id"),
)

class AckCommand(AbstractCommand):



    def __init__(self):
        self.id = None

    def parse(self, socket, game):
        if not self.parse_type(socket.peek(1)) == 'A' :
            return False

        socket.read(1)

        data = socket.read(1)

        packet = AckPacket.parse(data)

        self.id = packet.id

        print("Game ID {}".format(self.id))

        game.gameid = self.id

        return True


class GoCommand(AbstractCommand):

    def parse(self, socket, game):
        if not self.parse_type(socket.peek(1)) == 'G' :
            return False

        socket.read(1)

        game.stopped = False

        return True

class StopCommand(AbstractCommand):

    def parse(self, socket, game):
        if not self.parse_type(socket.peek(1)) == 'S' :
            return False

        socket.read(1)

        game.stopped = True

        return True

HelloPacket = Struct("HelloPacket",
     Const(Bytes("id", 1), '\x00'),
     Magic('H'),
     macros.CString("gamename"),
)

LinePacket = Struct("LinePacket",
    # Magic("C"),
    ULInt8("gameid"),
    Magic("L"),
    ULInt16("x1"),
    ULInt16("y1"),
    ULInt16("x2"),
    ULInt16("y2"),
    ULInt8("color"),
)

CirclePacket = Struct("CirclePacket",
    # Magic("C"),
    ULInt8("gameid"),
    Magic("C"),
    ULInt16("x"),
    ULInt16("y"),
    ULInt16("diam"),
    ULInt8("color"),
)

RectPacket = Struct("RectPacket",
    # Magic("C"),
    ULInt8("gameid"),
    Magic("D"),
    ULInt16("x1"),
    ULInt16("y1"),
    ULInt16("x2"),
    ULInt16("y2"),
    ULInt8("color"),
)

RefreshPacket = Struct("RefreshPacket",
    # Magic("C"),
    ULInt8("gameid"),
    Magic("R"),
)

PausePacket = Struct("PausePacket",
    # Magic("C"),
    ULInt8("gameid"),
    Magic("S"),
)

PlayerKeyPacket = Struct("PlayerKeyPacket",
    # Magic("I"),
    BitStruct("player2",
              Flag("xp"),
              Flag("xn"),
              Flag("yp"),
              Flag("yn"),
              Flag("x"),
              Flag("y"),
              Flag("a"),
              Flag("b"),
              ),
    BitStruct("player1",
              Flag("xp"),
              Flag("xn"),
              Flag("yp"),
              Flag("yn"),
              Flag("x"),
              Flag("y"),
              Flag("a"),
              Flag("b"),
              ),
)


class LaserGame(object):
    HOST = '127.0.0.1'
    PORT = 4242

    def __init__(self,gamename):
        self.gameid = 0
        self.gamename = gamename
        self.sock = None
        self.stopped = True
        self.multiplicator = 0.0
        self.color = LaserColor.LIME # Because it's REALLY awesome

        self.sock = socket.socket(type=socket.SOCK_DGRAM)
        self.sock.connect((HOST, PORT))
        self.sock.setblocking(0)

        self.sendCmd(HelloPacket.build(Container(id='\x00', gamename=gamename)))

        self.socket = Socket(self.sock)
        self.player1_keys = None
        self.player2_keys = None


    def sendCmd(self, data):
        # print("Sending '{}'".format(str(data)))
        return self.sock.send(data)

    def sendPacket(self, cls, **kwargs):
        # print("Sending {} {}".format(cls.name, ", ".join(["{}={}".format(k,v) for k,v in kwargs.iteritems()])))
        self.sendCmd(cls.build(Container(**kwargs)))

    def setResolution(self, px):
        self.multiplicator = math.floor(65535.0/px);

        return self

    def setDefaultColor(self, color):
        self.color = color
        return self

    def isStopped(self):
        return self.stopped



    def receiveServerCommands(self):
        commands = []

        # print("Game id = {}".format(self.gameid))


        if not self.socket.bytesAvail():
            return commands

        for cls in [ PlayerKeyCommand, GoCommand, StopCommand, AckCommand ]:
            inst = cls()

            if inst.parse(self.socket, self):
                # print(str(inst))
                break



        return commands

    def addLine(self, x1, y1, x2, y2, color = None):
        m = self.multiplicator

        # data = LinePacket.build(Container(gameid=self.gameid, x1=x1, y1=y1, x2=x2, y2=y2, color=color or self.color))

        # self.sendCmd(data)

        self.sendPacket(LinePacket, gameid=self.gameid, x1=x1*m, y1=y1*m, x2=x2*m, y2=y2*m, color=color or self.color)

        return self


    def addCircle(self, x, y, dim, color = None):
        m = self.multiplicator

        # data = CirclePacket.build(Container(gameid=self.gameid, x=x, y=y, diam=dim, color=color or self.color))

        # self.sendCmd(data)

        self.sendPacket(CirclePacket, gameid=self.gameid, x=x*m, y=y*m, diam=dim*m, color=color or self.color)

        return self


    def addRectangle(self, x1, y1, x2, y2, color = None):
        m = self.multiplicator

        # data = RectPacket.build(Container(gameid=self.gameid, x1=x1, y1=y1, x2=x2, y2=y2, color=color or self.color))

        # self.sendCmd(data)

        self.sendPacket(RectPacket, gameid=self.gameid, x1=x1*m, y1=y1*m, x2=x2*m, y2=y2*m, color=color or self.color)

        return self

    def refresh(self):

        # data = RefreshPacket.build(Container(gameid=self.gameid))

        # self.sendCmd(data)

        self.sendPacket(RefreshPacket, gameid=self.gameid)

        return self

    def pause(self):

        # data = PausePacket.build(Container(gameid=self.gameid))

        # self.sendCmd(data)

        self.sendPacket(PausePacket, gameid=self.gameid)

        return self



class LaserColor(object):
    RED = 0x1
    LIME = 0x2
    GREEN = 0x2
    YELLOW = 0x3
    BLUE = 0x4
    FUCHSIA = 0x5
    CYAN = 0x6
    WHITE = 0x7
