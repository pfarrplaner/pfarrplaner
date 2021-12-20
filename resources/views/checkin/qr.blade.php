<!DOCTYPE html>
<html>
<body style="font-size: 2em; text-align:center;">
<h1>Einchecken {{ $location->at_text ?? $location->name }}</h1>
<p>Keine Lust, zu warten, bis alle deine Kontaktdaten aufgeschrieben sind? Hier kannst du einfach online einchecken und deine Daten eingeben.</p>
<barcode code="{{ route('checkin.create', $location) }}" size="4" type="QR" error="M" class="barcode"
         disableborder="1"/><hr />
<small>Die Erhebung dieser Daten erfolgt nach §8 Abs. 1 CoronaVO. Nach §8 Abs. 2-3 CoronaVO
    können Sie nur
    nach korrekter Angabe Ihrer Kontaktdaten am Gottesdienst teilnehmen. Wir bitten hierfür
    um
    Verständnis.</small>
</body>
</html>
