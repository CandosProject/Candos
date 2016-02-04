import serial


with serial.Serial(port="/dev/tty.usbmodem1055261", baudrate=9600, timeout=1, writeTimeout=1) as port_serie:
    if port_serie.isOpen():
        while True:
            fichier = open('lecture_serial.txt', "a")
            ligne = port_serie.read()
            fichier.write(ligne);
            fichier.close