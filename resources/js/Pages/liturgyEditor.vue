<template>
    <admin-layout enable-control-sidebar="true" :title="title(service)">
        <template slot="navbar-left">
            <inertia-link class="btn btn-light" :href="route('service.edit', service.slug)"
                          title="Gottesdienst bearbeiten"><span class="mdi mdi-pencil"></span> Gottesdienst
            </inertia-link>&nbsp;
            <inertia-link class="btn btn-light" :href="route('service.sermon.editor', service.slug)"
                          title="Predigt zu diesem Gottesdienst bearbeiten"><span class="mdi mdi-microphone"></span>
                Predigt
            </inertia-link>&nbsp;
            <slot name="toolbar" />
        </template>
        <template slot="control-sidebar">
            <form-check label="Zeitangaben runden" v-model="$page.props.settings.liturgy_times_rounded"
                        @input="setLiturgyTimesRounded"/>
            <form-input class="mt-2" label="Sprechgeschwindigkeit" v-model="$page.props.settings.wpm" @input="setWPM"
                        help="Wörter pro Minute"/>
            <button class="btn btn-sm btn-primary" @click.prevent.stop="reloadPage">Anwenden</button>
        </template>
        <template slot="after-flash">
            <info-pane v-if="!agendaMode" :service="service" @info="infoWindow = true"/>
            <agenda-info-pane v-if="agendaMode" :agenda="service"/>
        </template>
        <liturgy-tree :service="service" :sheets="agendaMode ? {} : liturgySheets" :agenda-mode="agendaMode"
                      :auto-focus-block="autoFocusBlock" :auto-focus-item="autoFocusItem"
                      :ministries="ministries" :markers="markers"
                      @update-focus="updateFocus"/>
        <info-window v-if="infoWindow" @close="infoWindow = false" :service="service"/>
    </admin-layout>
</template>

<script>
import moment from 'moment';
import InfoWindow from "../components/LiturgyEditor/Pane/InfoWindow";
import FormCheck from "../components/Ui/forms/FormCheck";
import FormInput from "../components/Ui/forms/FormInput";

const InfoPane = () => import('../components/LiturgyEditor/Pane/InfoPane');
const AgendaInfoPane = () => import('../components/AgendaEditor/Pane/InfoPane');
const LiturgyTree = () => import('../components/LiturgyEditor/Pane/LiturgyTree');

export default {
    props: {
        service: Object,
        liturgySheets: Object,
        autoFocusBlock: {
            type: String,
            default: null,
        },
        autoFocusItem: {
            type: String,
            default: null,
        },
        ministries: {
            type: Object,
            default: [],
        },
        markers: {
            type: Object,
            default: null,
        }
    },
    components: {
        FormInput,
        FormCheck,
        InfoWindow,
        InfoPane,
        AgendaInfoPane,
        LiturgyTree,
    },
    data() {
        if (undefined == this.$page.props.settings.liturgy_times_rounded) this.$page.props.settings.liturgy_times_rounded = false;
        if (undefined == this.$page.props.settings.liturgy_times_rounded) this.$page.props.settings.liturgy_times_rounded = 110;

        return {
            blockIndex: null,
            itemIndex: null,
            element: null,
            infoWindow: false,
            agendaMode: moment(this.service.date).format('YYYYMMDD') == 19780305,
        }
    },
    methods: {
        title(service) {
            if (this.agendaMode) return 'Agende bearbeiten';
            return 'Liturgie für ' + moment(service.date).locale('de-DE').format('DD.MM.YYYY') + ', ' + service.timeText;
        },
        updateFocus(blockIndex, itemIndex, element) {
            this.blockIndex = blockIndex;
            this.itemIndex = itemIndex;
            this.element = element;
            this.showModal = true;
        },
        setLiturgyTimesRounded() {
            this.$forceUpdate();
            this.$inertia.post(route('setting.set', {
                user: this.$page.props.currentUser.data.id,
                key: 'liturgy_times_rounded'
            }), {
                value: this.$page.props.settings.liturgy_times_rounded,
            });
            window.location.reload();
        },
        setWPM() {
            this.$forceUpdate();
            this.$inertia.post(route('setting.set', {user: this.$page.props.currentUser.data.id, key: 'wpm'}), {
                value: this.$page.props.settings.wpm,
            });
        },
        reloadPage() {
            window.location.reload();
        }
    }
}
</script>
<style scoped>
</style>
