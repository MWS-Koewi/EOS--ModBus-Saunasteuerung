# EOSModBus
Beschreibung des Moduls.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Es können alle Werte der EOS Modbus Steuerung gelesen und geschrieben werden. Eine Modbus Instanz ist wird benötigt wird aber vom MOdul ach selbst erzeugt wenn keine vorhanden ist.
* Eine Besonderheit ist der Fehler status: Wenn eine Sicherheitseinrichtung wie ein Ofengitter verwendet wird und diese auslöst ist die Steuerung nicht mehr erreichbar. Ich habe mir damit geholfen eine Türerkennung (über HomeMatic) zu installieren welche die Steuerung im Webfront deaktiviert wenn sie auslöstö.

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.0

### 3. Software-Installation

* Über den Module Store das 'EOSModBus'-Modul installieren.
* Alternativ über das Module Control folgende URL hinzufügen: https://github.com/MWS-Koewi/EOS--ModBus-Saunasteuerung

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'EOSModBus'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
         |
         |

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
       |         |
       |         |

#### Profile

Name   | Typ
------ | -------
       |
       |

### 6. WebFront

Die Funktionalität, die das Modul im WebFront bietet.

### 7. PHP-Befehlsreferenz

`boolean EOS_BeispielFunktion(integer $InstanzID);`
Erklärung der Funktion.

Beispiel:
`EOS_BeispielFunktion(12345);`
