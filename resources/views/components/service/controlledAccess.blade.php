<div>
    <span style="color: red" class="fa fa-exclamation-triangle"></span> @if($service->controlled_access != 5)Zugangsbeschränkung:@endif
    @switch($service->controlled_access)
        @case (1)
        <span style="background-color: yellow; padding: 3px; border-radius: .25em; font-size: .9em;" title="Zu diesem Gottesdienst haben nur Geimpfte, Genesene oder Personen mit zertifiziertem Test Zugang.">3G</span>
        @break
        @case (2)
        <span style="background-color: green; color: white; padding: 3px; border-radius: .25em; font-size: .9em;"  title="Zu diesem Gottesdienst haben nur Geimpfte und Genese Zugang.">2G</span>
        @break
        @case (3)
        <span style="background-color: yellowgreen; padding: 3px; border-radius: .25em; font-size: .9em;"  title="Zu diesem Gottesdienst haben nur Geimpfte und Genese mit zusätzlichem zertifiziertem Test Zugang.">2G+</span>
        @break
        @case (4)
        <span style="background-color: dodgerblue; padding: 3px; border-radius: .25em; font-size: .9em;"  title="Zu diesem Gottesdienst haben nur Personen mit einem aktuellen zertifiziertem Test Zugang. Dies gilt auch für Geimpfte und Genesene">
            <span class="fa fa-vial"></span> Test erforderlich</span>
        @break
        @case (5)
        <span style="background-color: lightskyblue; padding: 3px; border-radius: .25em; font-size: .9em;"  title="Wir empfehlen vor dem Zutritt zum Gottesdienst einen aktuellen Schnell- oder PCR-Test, idealerweise von einer offiziellen Teststelle. Dies gilt auch für Geimpfte und Genesene">
            <span class="fa fa-vial"></span> Test empfohlen</span>
        @break
        @case (6)
        <span style="background-color: red; padding: 3px; border-radius: .25em; font-size: .9em;"  title="Zu diesem Gottesdienst hat nur eine bestimmte Personengruppe Zugang.">Geschlossene Gruppe</span>
        @break
    @endswitch
</div>
