=== WooRechnung ===
Contributors: ZWEISCHNEIDER
Donate link: https://woorechnung.com/
Tags: woocommerce, fastbill, automatic, monsum, debitoor, billomat, easybill, sevdesk, abrechnung, rechnung, rechnungen, lieferschein, lieferscheine, iex, iex integration
Requires at least: 3.0.1
Tested up to: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WooRechnung ermöglicht Ihnen Rechnungen, Kunden und Produkte aus WooCommerce direkt in vielen Providern automatisch zu erzeugen.

== Description ==

Erstelle direkt völlig frei gestaltbare Rechnungen über WooCommerce oder verbinde es mit **Billomat**, **Debitoor**, **easybill**, **FastBill**, **Monsum by FastBill**, **sevDesk**. WooRechnung ermöglicht Ihnen Rechnungen, Gutschriften, Kunden und Produkte aus dem WordPress Plugin WooCommerce direkt zu erzeugen und aktualisieren. Dadurch erhalten Sie die Vorteile aus beiden Welten und sparen dabei sehr viel Zeit!

**Freie Gestaltung:**
Du bist der Herr über das Layout deiner Rechnung. Wähle aus verschiedenen Vorlagen und passe diese mit HTML und der Templatesprache Mustache nach deinen Vorstellungen an oder erstelle dein eigenes Layout!

**Automatisierte Abläufe:**
Erstellen und Aktualisieren von Kundendaten, Erzeugen von Rechnungen und Lieferscheinen bei Bestellungen und Gutschriften bei Widerruf. Bereitstellen von Rechnungen als Download und Erzeugen von Versandmarken – alles direkt in WooCommerce und alles automatisch.

**Multiple Shops:**
Verbinden Sie mehrere WooCommerce Shops mit WooRechnung und behalten Sie den Überblick über Ihre gesamte Rechnungsstellung.

**Individuelle Layouts:**
Wählen Sie aus Ihren Rechnungsvorlagen und erstellen Sie stets zu Ihren hinterlegten Einstellungen passende und ansprechende Rechnungen.

** Registrierung **
Sie benötigen einen WooRechnung Account. Registrieren Sie Ihren Account auf [woorechnung.com](https://woorechnung.com/ "Einfach Rechnungen über Billomat, Debitoor, easybill, FastBill, Monsum by FastBill oder sevDesk erzeugen")

== Installation ==

1. Laden Sie die Plugin-Daten in Ihr Verzeichniss `/wp-content/plugins/woorechnung`, oder installieren Sie das Plugin direkt über das WordPress Plugin-Management.
2. Aktivieren Sie WooRechnung über den Menüpunkt 'Plugin'
3. Klicken Sie auf WooCommerce -> Einstellungen -> WooRechnung um Ihre Einstellungen vorzunehmen. Den Lizenzkey finden Sie in Ihrem WooRechnung Account.
4. Passen Sie die Einstellungen nach Ihren Vorstellungen an.

== Frequently Asked Questions ==

= Benötige ich einen WooRechnung Account? =

Ja. Registrieren Sie sich unter https://woorechnung.com und folgen Sie dem Setup-Prozess.

= Kann ich kostenlos Rechnungen erzeugen? =

Ja! Bis zu 10 Rechnungen im Monat sind bei WooRechnung kostenlos. Wenn Sie mehr Rechnungen schreiben möchten, können Sie Ihren Account jederzeit upgraden.

= In welchem Format müssen die Preise angegeben werden? =

Wir empfehlen Preise immer in Netto anzugeben, da es oft zu Rundungsproblemen führt wenn WooCommerce die Bruttopreise zuerst in Netto umrechnet.

= Welche WooCommerce Version benötige ich? =

WooRechnung benötigt mindestens WooCommerce 3 (oder höher).

== Changelog ==

= 2.0.23 (2021-01-08) =
* Added: Support für WordPress 5.6
* Added: Support für WooCommerce 4.8.0
* Fixed: Vereinzelt aufgetretener get_plugin_data Fehler bei Bestellabschluss behoben

= 2.0.22 (2020-11-05) =
* Added: Support für WordPress 5.5.3 und WooCommerce 4.6.1
* Added: Verschiedene neue Variablen, die im Dateinamen der Rechnung genutzt werden können
* Added: Unterstützung zu EU-VAT Plugins, die die VAT-Nummer als _billing_eu_vat_number, _vat_number oder vat_number speichern
* Fixed: Zu Bestellungen, auf die die Mehrfachaktion "Rechnung zurücksetzen" angewandt wird, wird nicht mehr automatisch eine neue Rechnung generiert, falls die Bestellung in einem zur Erzeugung von Rechnungen eingestellten Status ist
* Fixed: Verbesserte Übertragung von rabattierten Bestellpositionen zwecks klarerer Darstellung auf den Rechnungsdokumenten

= 2.0.21 (2020-09-24) =
* Added: Support für WordPress 5.5.1
* Added: Support für WooCommerce 4.5.2
* Fixed: Kompatibilität mit Wunschlisten Plugin

= 2.0.20 (2020-08-31) =
* Fixed: Änderungen an "posix_getpwnam" aus Version 2.0.19 um Fatal Error zu vermeiden.

= 2.0.19 (2020-08-28) =
* Fixed: Verbesserung der Abfrage der PHP Einstellung "open_basedir", um bei Verwendung dieser PHP Einstellung Fehler zu umgehen

= 2.0.18 (2020-08-15) =
* Fixed: Entfernung von festen Datentypen bei Funktionsrückgaben um Fehler mit älteren PHP Versionen zu vermeiden

= 2.0.17 (2020-08-12) =
* Added: Support für WordPress 5.5 und WooCommerce 4.3.2
* Added: Die Einstellungen des Plugins sind jetzt aus der Plugin-Übersicht heraus verlinkt
* Added: %first_name% und %last_name% können als Variablen für Vor- und Nachname der Rechnungsadresse im Dateinamen der Rechnung genutzt werden
* Fixed: Unterstützung für Klassenerweiterungen von alternativen Mailer-Plugins, die nicht direkt die PHPMailer Klasse nutzen
* Fixed: Abfrage der PHP Einstellung "open_basedir", um bei Verwendung dieser PHP Einstellung Fehler zu umgehen

= 2.0.16 (2020-07-22) =
* Added: Es können jetzt mehrere WooCommerce E-Mails zum Versand der Rechnung konfiguriert werden
* Fixed: Fehler behoben, bei dem 0€ Produkte nicht mit dem hinterlegten Umsatzsteuersatz übertragen wurden
* Fixed: Fehler behoben, bei dem die Rechnung im Status "Zahlung ausstehend" nicht automatisiert erstellt werden konnte
* Fixed: Problem behoben, bei dem die Rechnung bei Verwendung anderer Plugins, die den Zeitpunkt des Versands der WooCommerce E-Mails beeinflussen, nicht an die E-Mail angehangen wurde
* Fixed: Problem behoben, dass beim Speichern sehr komplexer .html-Templates zum Versand der Rechnung als separate E-Mail aufgetreten ist

= 2.0.15 (2020-04-24) =
* Changed: Anpassung der Namen von Bestellstati an die aktuelle WooCommerce Übersetzung
* Changed: Änderungen an den Einstellungen zum E-Mail Versand der Rechnung
* Added: Dienstleistungs-Produkte werden nach Möglichkeit als solche bei Buchhaltungsanbietern gespeichert
* Added: Der Platzhalter %invoice_no% kann ebenfalls im Betreff oder Text von E-Mails verwendet werden, wenn die Rechnungsnummer referenziert werden soll
* Added: Der Platzhalter %order_no% kann in Text und Betreff von E-Mails, sowie im Dateinamen der Rechnung genutzt werden, um die WooCommerce Bestellnummer zu referenzieren
* Added: Die separate Rechnungs-E-Mail kann nun auch zeitversetzt nach Erstellung der Rechnung versandt werden
* Added: Die Rechnung kann jetzt auch an andere WooCommerce-Mails als die Bestellbestätigung angehangen werden
* Fixed: Fehler behoben, bei dem trotz gegenteiliger Konfiguration Rechnungen über 0€ erstellt wurden
* Fixed: Fehler behoben, der den Versand von Textmails verhindern konnte, solange keine HTML-Mail hinterlegt war
* Fixed: Fehler behoben, bei dem die Rechnung an durch andere Plugins versandte E-Mails angehangen wurde

= 2.0.14 (2019-07-17) =
* Added: Übertragung der Kundenanmerkungen zu Bestellungen
* Fixed: Behebung des fehlschlagenden E-Mail-Versands bei fehlenden Einstellungen
* Fixed: Behebung einer PHP-Warning bei Rechnugnserstellung

= 2.0.13 (2019-05-17) =
* Fixed: Rechnungsnummer wird (wenn vorhanden) in das Plugin zurückübertragen
* Fixed: Exportierte Rechnungen werden nach Rechnungsnummer benannt
* Fixed: Per E-Mail versendete Rechnungen können mit Platzhalter %invoice_no% benannt werden
* Fixed: Fehler beim Anhang der Rechnung an E-Mail werden besser abgefangen

= 2.0.12 (2019-04-29) =
* Added: Versandkosten können benannt werden
* Fixed: Versandkosten bei mix MwSt. Bestellungen werden nun richtig berechnet

= 2.0.11 (2019-04-25) =
* Added: Die Bestellnummer wird nun als Rechnungsname genommen
* Added: Verbesserte Fehlermeldungen

= 2.0.10 (2019-04-17) =
* Fixed: Versandkostenbeschreibung entfernt
* Fixed: Mit 0€ Rechnungen
* Fixed: Kompatibilität mit YITH Gift Card Plugin

= 2.0.9 (2019-04-17) =
* Fixed: MwSt. Berechnung bei Versandkosten

= 2.0.8 (2019-04-16) =
* Fixed: Rechnungs Bulk Export geht wieder

= 2.0.6 (2019-04-15) =
* Fixed: Rechnung als bezahlt markieren
* Fixed: Stornierungen gehen wieder
* Fixed: Nicht 0% MwSt. bei 0€ Versand
* Fixed: Variable Produktbeschreibung funktioniert wieder

= 2.0.0 (2019-04-10) =
* Changed: Komplettes Code-Refactoring

= 1.1.8 (2019-02-13) =
* Added: Vorbereitung auf unser neues großes Release

= 1.1.7 (2018-08-23) =
* Fixed: VATID aus Plugins werden nun besser erkannt

= 1.1.6 (2018-07-25) =
* Added: Du kannst nun pro Zahlungsart ganz einfach die Texte auf deinen Rechnungen anpassen. So kannst du auch die Zahlungsdaten deines Zahlungsanbieters auf die Rechnung schreiben sollte jemand per "Kauf auf Rechnung" bezahlen. https://woorechnung.com/anleitung/kauf-auf-rechnung

= 1.1.5 (2018-06-27) =
* Changed: Gutschein in Rabatt umbenannt

= 1.1.4 (2018-06-15) =
* Fixed: Actions Spalte wird nun automatisch aktiviert beim speichern des Lizenzkeys

= 1.1.3 (2018-05-02) =
* Fixed: Einige Fixes für großes Systeme und neuem WooRechnung

= 1.1.2 (2018-03-14) =
* Fixed: WooCommerce 3 Optimierungen

= 1.1.1 (2018-01-17) =
* Added: Nun wird ein Webhook nach der Rechnungserstellung von unserem Server zu deinem WordPress gesendet um sicher zu gehen dass die Daten der Rechnung in die Bestellung gespeichert wurden. Dadurch beheben wir doppelte Rechnungen bei Abbrüchen.
* Fixed: Beschleunigung der Erstellung
* Fixed: Bessere Fehlermeldungen
* Fixed: Zahlungsarten von älteren WooCommerce Systemen werden wieder erkannt

= 1.1.0 (2017-11-18) =
* Added: 0€ Rechnungen können nun aktiviert werden
* Added: Weitere Platzhalter für die Freitexte (E-Mail und VatID)
* Added: Bei sevDesk kann die Betreffzeile nun frei gewählt werden
* Added: WooRechnung kann nun auch Bruttorechnungen erzeugen
* Fixed: FastBill UNIT_PRICE Fehler bei 0€ Positionen

= 1.0.9 (2017-11-17) =
* Added: Mehr Daten stehen zum Export bereit
* Added: FastBill VatID wird mit übertragen
* Added: Download Namen der Rechnungen wurde angepasst so das die Rechnungsnummer als Name erscheint
* Fixed: Bessere Fehlermeldungen (401 & 403)
* Fixed: sevDesk erkennt nun mehr Länder
* Fixed: payment_method_title wurde angepasst da mit der neusten WooCommerce Version nicht mehr kompatibel

= 1.0.8 (2017-10-05) =
* Added: pebe smart & 1&1 Buchhaltung als Anbieter hinzugefügt
* Added: Bei Rechnungen in die USA wird der State mit in die PLZ gespeichert
* Fixed: Das Gewicht von Produkten mit Varianten wird nun auch richtig berechnet

= 1.0.7 (2017-09-20) =
* Fixed: WooRechnung: Wenn eine Bestellung storniert wurde, werden keine doppelten Rechnungen mehr erzeugt

= 1.0.6 (2017-09-19) =
* Added: Du kannst nun mehrere Stati für die Rechnungserstellung als auch den Mailversand wählen. Einfach mit STRG + Klick (WIN) oder CMD + Klick (OSX) mehrere auswählen und speichern
* Added: Anbindung zu pebesmart.ch und online-buchhaltung.1und1.de
* Fixed: Der Rechnungsexport exportiert nun mehr als 200 Zeilen

= 1.0.5 (2017-08-24) =
* Added: Rechnungen haben nun beim downloaden als auch in den E-Mails die Rechnungsnummer als Namen
* Added: Lieferadresse wurde als Platzhalter für die Einleitung als auch Schlußtext hinzugefügt
* Added: Wir haben den Anbieter "Ohne Anbieter" in "WooRechnung" geändert

= 1.0.4 (2017-08-18) =
* Fixed: Undefined Fehler behoben

= 1.0.3 (2017-08-17) =
* Added: Rechnungen bei "WooRechnung" können nun frei gestaltet werden. Du benötigst nur Kenntnisse in HTML und der Template-Sprache Mustache
* Added: Export Funktionen unter woorechnung.com - Historie
* Added: Du kannst einstellen ob die MwSt. ohne oder mit einer Nachkommastelle Gerundet werden. Dies ist z.B. wichtig für die MwSt. von 2,5% in den Schweiz
* Added: Das Feld Staat (State) wird nun an alle Anbieter übermittelt
* Added: "WooRechnung" fügt nun auch das Land mit auf die Rechnung wenn es sich um eine Rechnung ins Ausland handelt
* Added: Du kannst einstellen ob die Rechnungen gedownloaded oder direkt im Browser in einem neuen Tab angezeigt werden
* Fixed: Debitoor MwSt. Rundungsprobleme

= 1.0.2 (2017-07-31) =
* Added: Vorbereitungen für eine Export-Funktion
* Added: Zu jeder Bestellung wird nun auch die Rechnungsnummer gespeichert

= 1.0.1 (2017-07-25) =
* Added: Postversand der Rechnungen
* Fixed: Es werden keine doppelten Rechnungen mehr erzeugt
* Fixed: Undefined Index entfernt

= 1.0.0 (2017-07-07) =
* Added: Verbessertes logging bei Debitoor
* Fixed: 1 Cent Rungundsprobleme sind nun behoben
* Fixed: Doppelte Statusmeldungen beim speichern der WordPress Einstellungen

= 0.9.14 (2017-06-22) =
* Added: Option damit eine Versandmarke nach dem Erstellen direkt in einem neuen Tab geöffnet wird
* Added: Automatische Berechnung des Gewichtes für Versandmarken anhand der Produkte
* Added: Default Preset kann gewählt werden. So wird die Versandmarke unten immer automatisch damit befüllt
* Fixed: sevDesk Login Methode funktioniert nun wieder
* Fixed: Undefined Fehlermeldungen bei WooCommerce 3 behoben

= 0.9.13 (2017-06-14) =
* Fixed: Kompatibilitätsproblem mit WooCommerce 3.0.8

= 0.9.11 (2017-06-12) =
* Added: Du kannst bei sevDesk nun ein Zahlungsziel auswählen
* Added: Wir haben die Kleinunternehmerregelung zu "WooRechnung" hinzugefügt
* Added: Wir haben die Schweiz als Land für Debitoor hinzugefügt
* Added: Wir haben Österreich als Land für Debitoor hinzugefügt
* Fixed: Wir erzeugen keine doppelten Rechnungen mehr wenn Status der Rechnung als auch des E-Mail Versandes gleich sind
* Fixed: sevDesk: Intro- und Outrotext funktioniert nun mit Absätzen
* Fixed: easybill: Länder werden nun richtig gespeichert
* Fixed: Wir erzeugen keine Kunden mehr mit leeren E-Mailadressen
* Fixed: Debitoor: MwSt werden nun pro Land einzeln betrachtet
* Fixed: WooRechnung: Bezeichnung Bestellung in Rechnung geändert

= 0.9.10 (2017-03-01) =
* Added: Shipcloud Versicherungsbetrag kann eingegeben werden

= 0.9.9 (2017-02-28) =
* Added: Die Rechnungsnummer von "WooRechnung" kann nun durch weitere Variablen verfeinert werden
* Added: VAT Nummern aus dem Plugin "WooCommerce EU VAT Number" von WooThemes
* Fixed: Log "Permission" Probleme behoben
* Fixed: Debitoor funktioniert nun auch mit anderen Währungen als €

= 0.9.8 (2017-02-27) =
* Fixed: Undefined Variable

= 0.9.7 (2017-02-23) =
* Added: sevDesk Kleinunternehmerregelung kann nun aktiviert werden
* Added: sevDesk Bruttorechnungen können ab jetzt erzeugt werden
* Added: bei FastBill werden nun mehr Zahlungsarten erkannt und richtig zugewiesen
* Fixed: Versicherungs Datum wird nun wieder unter den Produkten angezeigt

= 0.9.6 (2017-02-20) =
* Added: VAT-Nummer zu "WooRechnung" hinzugefügt
* Fixed: Logger Fehlermeldung beim ersten Nutzen entfernt
* Fixed: Versicherung wird nun über "Fees" hinzugefügt
* Fixed: sevDesk Titel "Invoice" ind "Rechnung" geändert
* Fixed: Text Bestellung in Bestellnummer geändert
* Fixed: API Calls beschleunigt

= 0.9.5 (2017-01-26) =
* Added: Debitoor speichert nun auch die VatID (Plugin: WooCommerce EU VAT Assistant)
* Fixed: Debitoor Rechnungsnummer als Dateinamen für E-Mail und Download der Rechnungen

= 0.9.4 (2017-01-09) =
* Added: Sollten Rechnungen zu dem Zeitpunkt des E-Mail Versandes noch nicht erzeugt worden sein, werden diese nun erzeugt

= 0.9.3 (2017-01-05) =
* Added: Rechnungen können nun wie früher an bestehende WooCommerce E-Mails angehangen werden. Dazu gibt es eine neue Option in den Rechnungseinstellungen
* Added: Du findest deine Rechnungen nun in deinem woorechnung.com Account
* Fixed: Billomat: Rechnungsdatum funktioniert nun wie gewollt
* Fixed: Debitoor: Produkte können nun wieder aktuallisiert werden
* Fixed: Debitoor: z.Hd. wird nur noch gezeigt sollte auch ein Vor- / Nachname angegeben worden sein
* Fixed: Debitoor: Einige Probleme mit mehreren MwSt. Sätzen auf einer Rechnung wurden behoben
* Fixed: Easybill: ZIP Code wird nun wieder richtig gespeichert
* Fixed: Easybill: Berechnung bei Brutto Preisen funktioniert nun wieder

= 0.9.2 (2016-12-02) =
* Added: Lokale Log Datei für bessere Supportmöglichkeiten
* Fixed: API Key lässt keine Leerzeichen mehr zu
* Fixed: FastBill Adress2 wird nicht mehr in das Dokument eingefügt wenn diese leer ist

= 0.9.1 (2016-11-11) =
* Fixed: Rechnungen werden nun im TEMP Ordner des Systems geschrieben um Schreibfehler zu vermeiden
* Fixed: Wording

= 0.9.0 (2016-11-02) =
* Added: E-Mails werden ab jetzt über eine eigene E-Mail verschickt. Somit beheben wir das Problem das bei einigen Shops die Rechnung nicht an E-Mails angehangen wurde. Ihr könnt die E-Mail selbständig Layouten. Geht dazu unter WordPress - WooCommerce - WooRechnung auf Rechnungs E-Mail.
* Fixed: Es wurden Probleme mit der Verbindung zu sevDesk gelöst
* Fixed: Mit Billomat können nun auch andere Währungen als €genutzt werden

= 0.8.5 (2016-09-28) =
* Fixed: Manche Plugins verändern Preise von 0 zu NAN was zu problemen führte

= 0.8.4 (2016-09-26) =
* Fixed: Für Debitoor Kunden die als Hauptsitz nicht Deutschland haben, wird mit diesem Fix die Funktionalität von WooRechnung ermöglicht

= 0.8.3 (2016-09-23) =
* Fixed: gzip entfernt da es bei einigen Systemen Probleme erzeugt (Kryptische Zeichen wurden angezeigt)

= 0.8.2 (2016-08-29) =
* Added: Fees werden nun auf der Rechnung angezeigt

= 0.8.1 (2016-08-29) =
* Added: Mehr Länder zur Auswahl für Versandmarken
* Fixed: "Undefined Index" entfernt

= 0.8.0 (2016-08-24) =
* Added: Gutscheine erscheinen nun als Posten auf der Rechnung
* Added: Das Plugin "WooCommerce Pay for Payment" von Jörn Lund ist ab jetzt kompatibel mit WooRechnung
* Fixed: Preisberechnung verändert so das Gutscheine nicht mehr von den jeweiligen Post abgezogen werden sondern als eigener Posten auf der Rechnung erscheint

= 0.7.7 (2016-08-22) =
* Added: Produktkurzbeschreibung kann nun auf der Rechnung gespeichert werden
* Added: Anrede und Zusatzadresse werden gespeichert
* Fixed: FastBill: Versand-Land wird nun mit gespeichert

= 0.7.6 (2016-08-17) =
* Fixed: sevDesk Währungen werden nun übernommen
* Fixed: Rechnungen werden nun auch wieder mit der standard Mailfunktion verschickt (Wir empfehlen dennoch den Versand per SMTP Plugin z.B. WP Mail Bank)
* Fixed: Besseres Debug Log
* Fixed: easybill probleme mit kleinere Paketen gelöst
* Fixed: Versandkosten berechnung nach Update auf WooCommerce 2.6 gefixt

= 0.7.5 (2016-08-01) =
* Added: Im kostenlosen Tarif haben wir die Versandmarken von 5 auf 25 geändert
* Added: sevDesk Produkte können nun gespeichert und bearbeitet werden
* Added: Das Limit kann nun aufgehoben werden so dass mehr Rechnungen oder Versandmarken einzeln abgerechnet werden können
* Fixed: Debitoor Lieferscheine können nun auch für andere Länder als Deutschland ausgestellt werden
* Fixed: Fehlermeldungen von Debitoor und sevDesk vereinfacht
* Fixed: Debitoor Rechnungserstellung nach Großbritannien nun möglich

= 0.7.4 (2016-07-18) =
* Added: FastBill kann nun pro Land ein eigenes Template nutzen
* Fixed: Rechnungsicon

= 0.7.3 (2016-07-18) =
* Added: E-Mail Status kann nun selbständig gewählt werden
* Added: Adresse 2 wird mit übergeben
* Fixed: "WooRechnung" Berechnung

= 0.7.2 (2016-07-16) =
* Added: Rechnungen können nun in jedem Status manuell erzeugt werden
* Added: Bei "WooRechnung" kann die Währung nun geändert werden
* Added: Shipcloud kann nun WooRechnungs Kunden direkt erkennen und besser Supporten
* Added: Bei Debitoor kann das Zahlungsdatum frei gewählt werden
* Added: Bei Debitoor wird der Versand bei verschiedenen MwSt Sätzen auf einer Rechnung nun nach deutschem Recht gesplittet
* Fixed: FastBill UNIT_PRICE

= 0.7.1 (2016-07-08) =
* Added: Debitoor Lieferscheine können nun automatisch mit erzeugt werden.

= 0.7.0 (2016-07-04) =
* Added: sevDesk Anbindung
* Fixed: Undefined index

= 0.6.11 (2016-07-04) =
* Added: Status "Wartend" als Rechnungsstatus hinzugefügt
* Added: Bei versandmarken kann optional das E-Mail Feld frei gelassen werden
* Fixed: Bei den Debitoor Kundenakten werden keine leeren Kundennummern gespeichert
* Fixed: UPS wird nun bei der Versandmarkenerzeugung vorausgewählt
* Fixed: Bei EasyBill konnten keine Rechnungen erzeugt werden

= 0.6.10 (2016-06-28) =
* Fixed: Fehlerbehebung aus Version 0.6.9

= 0.6.9 (2016-06-28) =
* Added: Unterstüzung des Plugins "Shipping Details for WooCommerce" von "PatSaTECH". Der Trackingcode und der Versandanbieter werden automatisch ausgefüllt.
* Fixed: Undefined index

= 0.6.8 (2016-06-25) =
* Added: Multilanguage Support
* Fixed: Die Produktbeschreibung wird nur noch bei dem richtigen Setting mit übergeben

= 0.6.7 (2016-06-22) =
* Added: Einheit wird mit an die Rechnung übergeben
* Added: Unterstüzung für Monsum (ehemals FastBill Automatic)
* Added: Debitoor - Schlußtext mit Variablen hinzugefügt (Auch für Kleinunternehmer Klausel geeignet)
* Added: WooRechnung - Artikelbeschreibung hinzugefügt
* Fixed: WooRechnung - Logos werden nun resized

= 0.6.6 (2016-06-18) =
* Fixed: Variationsproblem seit WooCommerce 2.6.0 jetzt behoben

= 0.6.5 (2016-06-17) =
* Added: Bulk Download der Rechnungen
* Added: Unterstützung von "WooCommerce Order Status Manager"

= 0.6.4 (2016-06-15) =
* Added: Versandmarken Vorlagen für schnelleren Versand
* Fixed: Einige kleine Änderungen für WooCommerce 2.6.0

= 0.6.3 (2016-06-13) =
* Added: Nun können Rechnungen auch ohne externen Anbieter erzeugt werden

= 0.6.2 (2016-05-30) =
* Added: FastBill Introtext kann mit Platzhaltern versehen und überschrieben werden
* Added: WooRent integration
* Added: Die Verbindung zwischen WooRechnung und WordPress kann nun getestet werden
* Fixed: FastBill Zahlungsarten werden nun richtig zu den Kunden gespeichert

= 0.6.1 (2016-05-18) =
* Added: DEBUG Konsole bei Fehlermeldungen

= 0.6.0 (2016-05-16) =
* Added: Rechnungs- / Zahlungsstatus pro Zahlungsart wählbar
* Added: Rechnungen können ab jetzt auch als Entwurf gespeichert werden (Debitoor & FastBill)
* Fixed: Briefporto über shipcloud
* Fixed: TaxEnabled Fehler bei Debitoor behoben

= 0.5.10 =
* Added: Deutsche Post AG (Briefe und Buchsendungen) können nun per shipcloud verschickt werden

= 0.5.9 =
* Fixed: Rechnungen konnten nicht erzeugt werden. Ist nun wieder gefixt. Entschuldigt!

= 0.5.8 =
* Added: FastBill - Rechnungen werden als Bezahlt markiert

= 0.5.7 =
* Added: Das Rechnungsdatum kann in den Einstellungen geändert werden (Tag der Bestellung oder Tag der Rechnungserzeugung)
* Added: Versandkosten-Artikelnummer ist nun änderbar
* Fixed: Versandkostenberechnung mit unterschiedlichen "Germanize" Plugins gefixt

= 0.5.6 =
* Fixed: Automatische Rechnungen werden nun auch erzeugt, wenn die Einstellungen noch nicht gespeichert wurden

= 0.5.5 =
* Fixed: Rechnungen werden nun wieder automatisch erzeugt

= 0.5.4 =
* Added: Weitere Länder zu den Versandmarken hinzugefügt
* Fixed: Schnellere Ladezeiten

= 0.5.3 =
* Aktion: Wir haben die Preise um -50% verringert!
* Fixed: Einige Anpasungen

= 0.5.2 =
* Fixed: Bessere Fehlermeldungen für Rechnungen und shipcloud

= 0.5.1 =
* Added: Rechnungserstellung kann nun deaktiviert werden falls gewünscht
* Fixed: Splittung der Einstellungen in eigene Tabs

= 0.5.0 =
* Added: shipcloud

= 0.4.5 =
* Added: Prefix und Suffix für die Bestellnummer
* Added: Debitoor - Lieferadresse kann mit in den Rechnungs-Hinweis gespeichert werden
* Added: Vorbereitungen für shipcloud: Dieses Feature folgt in der nächsten Version
* Fixed: Versandkosten Artikelnummer auf "vk" reduziert

= 0.4.4 =
* Fixed: Leerzeichen vor und nach dem Lizenzkey werden nun ignoriert
* Fixed: Das Rechnungsicon in der Bestelübersicht wird nun auch bei anderen Stati als Fertiggestellt angezeigt
* Fixed: Die Rechnung wird nun mit der E-Mail versendet, zu der die Rechnung erzeugt wird

= 0.4.3 =
* Added: FastBill Brutto-Rechnung
* Added: Billomat Anbindung
* Added: easybill Anbindung

= 0.4.2 =
* Added: Es werden keine Rechnungen für 0€ Bestellungen erstellt

= 0.4.1 =
* Fixed: Artikelnummer bei Variationen wird nun auf der Rechnung angezeigt
* Fixed: Bei mehreren Steuersätzen auf einer Rechnung werden die Versandkosten nun anteilig aufgeteilt

= 0.4.0 =
* Fixed: Undefined index Fehler
* Added: Der Variantentitel kann nun als Produktbeschreibung an die Rechnung übergeben werden
* Added: Die Lieferadresse wird nun in FastBill zu den Kundendaten gespeichert

= 0.3.9 =
* Fixed: Debitoor - Rundungsfehler behoben
* Added: Nur noch ein request pro Rechnungserstellung
* Added: Debitoor - Produkte können nun gespeichert und bearbeitet werden
* Added: Artikelnummer wird mit übertragen
* Added: Zusätzlich zum Produktnamen wird nun auch die Produktbeschreibung gespeichert
* Added: Option um Produktbeschreibung anzupassen

= 0.3.8 =
* Fixed: "unexpected end of file" behoben

= 0.3.7 =
* Fixed: Bestellnummer

= 0.3.6 =
* Added: Nun speichert WooRechnung die Bestellnummer zu den Rechnungen

= 0.3.5 =
* Fixed: MwSt. Berechnung

= 0.3.4 =
* Rechnungen automatisch versenden

= 0.3.3 =
* Erstelle, downloade und storniere Rechnungen automatisch bei der Bearbeitung deiner WooCommerce bestellungen
* Rechnungs-Provider: Debitoor, FastBill und Automatic by FastBill

== Upgrade Notice ==

= 0.3.3 =
Erste Version
