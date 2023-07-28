# EOSModBus
Beschreibung des Moduls.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-vorraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)
8. [Sonstiges](#8-sonstiges)
9. [Changelog](#9-changelog)

### 1. Funktionsumfang

* Es können alle Werte der EOS Modbus Steuerung gelesen und geschrieben werden. Eine Modbus Instanz ist wird benötigt wird aber vom Modul auch selbst erzeugt wenn keine vorhanden ist.
* Eine Besonderheit ist der Fehlerstatus: Wenn eine Sicherheitseinrichtung wie ein Ofengitter verwendet wird und diese auslöst ist die Steuerung nicht mehr erreichbar. Ich habe mir damit geholfen eine Türerkennung (über HomeMatic) zu installieren welche die Steuerung im Webfront deaktiviert wenn sie auslöst.

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.0
- EOS Saunasteurung ModBus (SMB-GLT-MOS)
- Die passende Saunasteuerung dazu
- Eine Modbusverbindung zu IP-Symcon 

### 3. Software-Installation

* Über den Module Store das 'EOSModBus'-Modul installieren. 
* Alternativ über das Module Control folgende URL hinzufügen: https://github.com/MWS-Koewi/EOS--ModBus-Saunasteuerung

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'EOSModBus'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name                    | Beschreibung
----------------------- | ----------------------------------------------------------------------------------------------------------------------------------
Abfrageintervall        | Das Intervall in dem die Register gepollt werden sollen in Millisekunden
Verdampfer              | Wenn ein Verdampfer vorhanden ist kann er eingeblendet und angesprochen werden, Sol- und Istfeuchte werden ebenso angezeigt
Potentialfreier Kontakt | Wenn der potentialfreie Kontakt genutzt werden soll kann er eingeblendet und angesprochen werden
Licht Sollwert          | Wenn der Sollwert des Lichtes eingestellt werden soll kann er eingeblendet und angesprochen werden
Infos                   | Die Informationen über Modelltyp und Firmwareversion die angezeigt werden können
Status Emulieren        | Wenn aktiv werden die Werte im Webfront sofort angezeigt und nicht auf die Reaktion der Steuerung gewartet. Wenn der Schaltvorgang nicht erfolgreich war wird beim nächsten Poll der in der Steuerung gesetzt Wert wieder angezeigt.

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

ID                  | Name                	| Typ    | Profil                   | Beschreibung
------------------- | ------------------------- | ------ | -------------------------| ---------------------------------------
eosModelType        | Gerät Modelltyp     	| Int    | EOSModBus.ModellType     | Modell des ModBus Gerätes
eosFirmware         | Firmwareversion     	| String | 		            | Firmwareversion des ModBus Gerätes
eosCurrentTemp      | Temperatur Istwert	| Int    | EOSModBus.Temperature2   | Ist Temperatur der Saunakabine
eosCurrentHumidity  | Feuchte Istwert     	| Int    | ~Humidity	     	    | Ist Luftfeuchtigkeit der Saunskabine
eosLightSwitch      | Licht               	| Bool   | ~Switch		    | Schalter für das Kabinenlicht
eosPFCSwitch        | Potentialfreier Kontakt 	| Bool   | ~Switch		    | Schalter für den potentialfreien Kontakt
eosHeaterSwitch     | Ofen                	| Bool   | ~Switch		    | Schalter für den Ofen
eosVaporizerSwitch  | Verdampfer          	| Bool   | ~Switch		    | Schalter für den Verdampfer
eosSetLightValue    | Licht Sollwert      	| Int    | ~Intensity.100	    | Soll Lichtintensität der Lampe
eosSetTempValue     | Temperatur Sollwert 	| Int    | EOSModBus.Temperature    | Soll Temperatur der Saunakabine
eosSetHumidityValue | Feuchte Sollwert    	| Int    | ~Intensity.100	    | Soll Luftfeuchtigkeit der Kabine

### 6. WebFront

Je nach gewähltem Modus werden im WebFront die einzelnen, der Instanz direkt untergeordneten Controls sichtbar/unsichtbar geschaltet.

### 7. PHP-Befehlsreferenz

Das Modul stellt folgende PHP-Befehle zur Verfügung.

Alle PHP-Befehle erhalten den Prefix EOS_

`RequestRead()`

Liest alle Werte der Steuerung aus

`SetHeaterSwitch(bool $Wert)`

Schaltet den Ofen. 

`SetPFCSwitch(bool $Wert)`

Schaltet den potentialfreien Kontakt. 

Parameter $Status: false (Off) / true (On)

`SetLightSwitch(bool $Wert)`

Schaltet das Licht auf die eingestellte Helligkeit.

Parameter $Status: false (Off) / true (On)

`SetVaporizerSwitch(bool $Wert)`

Schaltet den Verdampfer.

Parameter $Status: false (Off) / true (On)

`SetHumidityValue(int $Value)`

Stellt die gewünschte Luftfeuchte ein. 

Parameter $Value: Luftfeuchte von 0 .. 100

`SetLightValue(int $Value)`

Stellt die gewünschte Leuchtstärke ein. 

Parameter $Value: Leuchtstärke von 0 .. 100

### 8. Sonstiges
Verwendung auf eigene Gefahr, der Autor übernimmt weder Gewähr noch Haftung.

### 9. Changelog
Version 1.1.1 (2023-07-13)
* Fix: Anpassungen für PHP Version 8 
