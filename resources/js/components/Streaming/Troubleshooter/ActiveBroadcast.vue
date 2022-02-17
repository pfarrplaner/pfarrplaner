<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/pfarrplaner/pfarrplaner
  - @version git: $Id$
  -
  - Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
  -
  - Pfarrplaner is based on the Laravel framework (https://laravel.com).
  - This file may contain code created by Laravel's scaffolding functions.
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation, either version 3 of the License, or
  - (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <div class="active-broadcast">
        <div class="alert alert-light">
            <b>Aktiver Stream:</b>
            <div v-if="broadcast.service">
                {{ broadcast.service.titleText }}<br/>
                {{ moment(broadcast.service.day.date).locale('de-DE').format('LL') }}, {{
                    broadcast.service.timeText
                }}<br/>
                {{ broadcast.service.locationText }}
            </div>
            <div v-else>
                <i>Kein Gottesdienststream</i><br/>
                {{ broadcast.snippet.title }}<br/>
                {{ moment(broadcast.snippet.scheduledStartTime).locale('de-DE').format('LLL') }}
            </div>
        </div>
        <button class="btn btn-danger" @click="$emit('wrong')">Ist das der falsche Stream?</button>
        <hr/>
        <checked-process-item :check="true" positive="Korrekter Streamingschlüssel"/>
        <checked-process-item :check="Math.abs(moment().diff(moment(broadcast.snippet.scheduledStartTime), 'minutes')) <=15"
                              positive="Startzeit weicht um 15 Minuten oder weniger ab">
            <template slot="negative">
                <span v-if="moment().diff(moment(broadcast.snippet.scheduledStartTime), 'minutes') < 0">
                    Dieser Stream soll laut den hinterlegten Angaben erst {{ moment(broadcast.snippet.scheduledStartTime).locale('de-DE').fromNow() }} starten.
                </span>
                <span v-else>
                    Dieser Stream sollte laut den hinterlegten Angaben bereits {{ moment(broadcast.snippet.scheduledStartTime).locale('de-DE').fromNow() }} starten.
                </span>
                <button class="btn btn-danger" @click="$emit('wrong')">Ist das vielleicht der falsche Stream?</button>
            </template>
        </checked-process-item>
        <div v-if="broadcast.service">
            <checked-process-item :check="moment(broadcast.snippet.scheduledStartTime).locale('de-DE').format('LL') == moment(broadcast.service.day.date).locale('de_DE').format('LL')"
                                  positive="Datum in Pfarrplaner und YouTube stimmt überein">
                <template slot="negative">
                    Pfarrplaner und YouTube scheinen unterschiedliche Daten für diesen Gottesdienst zu haben.
                    <button class="btn btn-danger" @click="$emit('wrong')">Ist das vielleicht der falsche Stream?</button>
                    <br />
                    Du kannst auch versuchen, dieses Problem zu beheben, indem du den Stream noch einmal neu aktivierst.
                    <button class="btn btn-success" @click="reactivateService">Problem beheben</button>
                </template>
            </checked-process-item>
            <checked-process-item
                v-if="broadcast.service.city.youtube_auto_startstop"
                :check="broadcast.contentDetails.enableAutoStart"
                positive="Auto-Start aktiviert">
                <template slot="negative">
                    Auto-Start ist nicht aktiviert!<br/>
                    <button class="btn btn-success" @click="reactivateService">Problem beheben</button>
                </template>
            </checked-process-item>
            <checked-process-item
                v-if="broadcast.service.city.youtube_auto_startstop"
                :check="broadcast.contentDetails.enableAutoStop"
                positive="Auto-Stop aktiviert">
                <template slot="negative">
                    Auto-Stop ist nicht aktiviert!<br/>
                    <button class="btn btn-success" @click="reactivateService">Problem beheben</button>
                </template>
            </checked-process-item>
        </div>
        <div v-else>
            <checked-process-item :check="false" color-negative="#ffc107" icon-negative="mdi mdi-alert">
                <template slot="negative">
                    Dieser Stream wurde nicht über den Pfarrplaner angelegt und ist auch keinem Gottesdienst zugeordnet.
                    Eventuell handelt es sich um eine besondere Veranstaltung. Wenn nicht, solltest du überprüfen, ob
                    es sich um den korrekten Stream für deine Veranstaltung handelt.
                    <button class="btn btn-warning" @click="$emit('wrong')">Ist das vielleicht der falsche Stream?</button>
                </template>
            </checked-process-item>
        </div>
        <hr/>
        <div>
            Status: <span class="badge" :class="'badge-'+statusBadgeClass()">{{
                broadcast.status.lifeCycleStatus
            }}</span>
            <div style="font-size: .8em;">
                {{ statusHelp() }}
            </div>
        </div>
        <div v-if="statusNeedsResolving()" class="alert" :class="'alert-'+statusBadgeClass()">
            <div v-if="broadcast.service">
                Um dieses Problem zu lösen, kannst du versuchen, den Stream erneut zu aktivieren.
                <button class="btn btn-secondary" @click="reactivate">Erneut aktivieren</button>
                <hr/>
                Wenn das das Problem nicht löst, kannst du den Stream auch komplett zurücksetzen. Dabei wird der dem
                Gottesdienst
                zugeordnete Stream auf Youtube komplett gelöscht und ein neuer Stream angelegt.<br/>
                <b>Achtung!</b> Durch diese Aktion ändern sich auch alle Links zum Youtube-Video. Alle bisherigen
                Links
                führen dann zu einer Fehlermeldung. Diesen Lösungsversuch solltest du nur anwenden, wenn alles
                andere
                nicht geholfen hat.
                <button class="btn btn-danger" @click="reset">Komplett zurücksetzen</button>
                <br/>
            </div>
            <div v-else>
                Probleme mit einem direkt auf YouTube (nicht über den Pfarrplaner) angelegten Stream kannst du nur
                direkt im <a title="Zum Live" target="_blank"
                             :href="'https://studio.youtube.com/video/'+broadcast.id+'/livestreaming'">
                Live Control Room</a> lösen (vorausgesetzt, du hast Zugang dazu).
            </div>
        </div>
        <hr/>
        <a class="btn btn-secondary" title="Zum Youtube-Video" target="_blank"
           :href="'https://youtu.be/'+broadcast.id">
            <span class="mdi mdi-youtube"></span> YouTube
        </a>
        <a class="btn btn-secondary" title="Zum Live Control Room (Zugangsdaten benötigt!)" target="_blank"
           :href="'https://studio.youtube.com/video/'+broadcast.id+'/livestreaming'">
            <span class="mdi mdi-video"></span> Live Control Room
        </a>
        <div style="font-size: 0.8em;">
            Der Zugang zum Live Control Room ist nur mit entsprechenden Zugangsdaten (Benutzername, Passwort) möglich.
        </div>
        <hr />
        <div v-if="broadcast.contentDetails.monitorStream.enableMonitorStream">
            <h3>Monitoring</h3>
            <div class="video-container">
                <iframe :src="'https://www.youtube.com/embed/'+broadcast.id+'?autoplay=1'"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen frameborder="0"></iframe>
            </div>
        </div>
        <hr/>
        <h3>Allgemeine Problemlösung</h3>
        <div v-if="broadcast.service" class="alert alert-light">
            Um bisher nicht genannte Probleme zu lösen, kannst du versuchen, den Stream erneut zu aktivieren.
            <button class="btn btn-secondary" @click="reactivate">Erneut aktivieren</button>
            <hr/>
            Wenn das das Problem nicht löst, kannst du den Stream auch komplett zurücksetzen. Dabei wird der dem
            Gottesdienst
            zugeordnete Stream auf Youtube komplett gelöscht und ein neuer Stream angelegt.<br/>
            <b>Achtung!</b> Durch diese Aktion ändern sich auch alle Links zum Youtube-Video. Alle bisherigen Links
            führen dann zu einer Fehlermeldung. Diesen Lösungsversuch solltest du nur anwenden, wenn alles andere
            nicht geholfen hat.
            <button class="btn btn-danger" @click="reset">Komplett zurücksetzen</button>
            <br/>
        </div>
        <div v-else>
            Probleme mit einem direkt auf YouTube (nicht über den Pfarrplaner) angelegten Stream kannst du nur
            direkt im <a title="Zum Live" target="_blank"
                         :href="'https://studio.youtube.com/video/'+broadcast.id+'/livestreaming'">
            Live Control Room</a> lösen (vorausgesetzt, du hast Zugang dazu).
        </div>
    </div>
</template>

<script>
import CheckedProcessItem from "../../Ui/elements/CheckedProcessItem";

export default {
    name: "ActiveBroadcast",
    components: {CheckedProcessItem},
    props: ['broadcast'],
    methods: {
        reactivate() {
            if (this.broadcast.service) this.reactivateService();
        },
        reactivateService() {
            this.$inertia.post(route('streaming.troubleshooter.activateService', {service: this.broadcast.service.id}), {preserveState: false});
        },
        reactivateBroadcast() {
            this.$inertia.post(route('streaming.troubleshooter.activateBroadcast', {broadcast: this.broadcast.id}), {preserveState: false});
        },
        reset() {
            this.$inertia.post(route('streaming.troubleshooter.resetService', {service: this.broadcast.service.id}), {preserveState: false});
        },
        ccLink(title) {
            return '<a target="blank" href="https://studio.youtube.com/video/' + this.broadcast.id + '/livestreaming">' + title + '</a>';
        },
        statusNeedsResolving() {
            switch (this.broadcast.status.lifeCycleStatus) {
                case 'complete':
                case 'created':
                case 'revoked':
                case 'testStarting':
                case 'testing':
                    return true;
                    break;
                case 'livestarting':
                case 'ready':
                case 'live':
                    return false;
                    break;
            }
            return true;
        },
        statusHelp() {
            switch (this.broadcast.status.lifeCycleStatus) {
                case 'complete':
                    return 'Der Stream ist bereits beendet. Wenn das nicht so sein sollte, kannst du hier versuchen, das Problem zu lösen:';
                    break;
                case 'created':
                    return 'Der Stream ist angelegt, hat aber unvollständige Einstellungen und ist nicht bereit, live zu gehen. Klicke auf "Problem lösen" oder überprüfe die Einstellungen direkt im ' + this.ccLink('Live Control Room') + '.';
                    break;
                case 'live':
                    return 'Der Stream ist auf Sendung.';
                    break;
                case 'livestarting':
                    return 'Der Stream geht gerade auf Sendung.';
                    break;
                case 'ready':
                    var text = 'Der Stream ist bereit, auf Sendung zu gehen. ';
                    if (this.broadcast.contentDetails.enableAutoStart) {
                        text += 'Sende deine Daten, um automatisch live zu gehen.'
                    } else {
                        text += 'Stelle sicher, dass du Daten sendest und schalte den Stream anschließend im '
                            + this.ccLink('Live Control Room') + ' auf live.';
                    }
                    return text;
                    break;
                case 'revoked':
                    return 'Der Stream ist wurde durch einen Admin entfernt. Da gibt es vermutlich nichts, was du jetzt selbst machen kannst, um das Problem zu lösen.';
                    break;
                case 'testStarting':
                    return 'Der Stream wechselt gerade in den Teststatus.';
                    break;
                case 'testing':
                    return 'Der Stream ist im Testmodus und nur für den Kanalinhaber sichtbar.';
                    break;
            }
            return 'Unbekannter Status';
        },
        statusBadgeClass() {
            switch (this.broadcast.status.lifeCycleStatus) {
                case 'complete':
                    return 'danger';
                    break;
                case 'created':
                    return 'warning';
                    break;
                case 'live':
                    return 'success';
                    break;
                case 'livestarting':
                    return 'info';
                    break;
                case 'ready':
                    return 'info';
                    break;
                case 'revoked':
                    return 'danger';
                    break;
                case 'testStarting':
                    return 'warning';
                    break;
                case 'testing':
                    return 'warning';
                    break;
            }
            return 'warning';

        }


    }

}
</script>

<style scoped>

.alert a.btn {
    text-decoration: none;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%;
    padding-top: 0;
    height: 0;
    overflow: hidden;
}

.video-container iframe,
.video-container object,
.video-container embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.mdi.mdi-youtube {
    color: red;
}
</style>

