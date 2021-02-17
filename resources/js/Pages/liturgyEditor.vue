<template>
    <admin-layout enable-control-sidebar="true" :title="title(service)">
        <info-pane v-if="!agendaMode" :service="service"/>
        <agenda-info-pane v-if="agendaMode" :agenda="service"/>
        <div class="row">
            <div class="col-12">
                <liturgy-tree :service="service" :sheets="agendaMode ? {} : liturgySheets" :agenda-mode="agendaMode"
                              :auto-focus-block="autoFocusBlock" :auto-focus-item="autoFocusItem"
                              :ministries="ministries"
                               @update-focus="updateFocus"/>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import moment from 'moment';

const InfoPane = () => import('../components/LiturgyEditor/Pane/InfoPane');
const AgendaInfoPane = () => import('../components/AgendaEditor/Pane/InfoPane');
const LiturgyTree = () => import('../components/LiturgyEditor/Pane/LiturgyTree');

export default {
    props: {
        service: Object,
        liturgySheets: Object,
        autoFocusBlock: {
            type: Number,
            default: null,
        },
        autoFocusItem: {
            type: Number,
            default: null,
        },
        ministries: {
            type: Object,
            default: [],
        },
    },
    components: {
        InfoPane,
        AgendaInfoPane,
        LiturgyTree,
    },
    data() {
        return {
            blockIndex: null,
            itemIndex: null,
            element: null,
            agendaMode: moment(this.service.day.date).format('YYYYMMDD') == 19780305,
        }
    },
    methods: {
        title(service) {
            if (this.agendaMode) return 'Agende bearbeiten';
            return 'Liturgie für ' + moment(service.day.date).locale('de-DE').format('DD.MM.YYYY') + ', ' + service.timeText;
        },
        updateFocus(blockIndex, itemIndex, element) {
            this.blockIndex = blockIndex;
            this.itemIndex = itemIndex;
            this.element = element;
            this.showModal = true;
        }
    }
}
</script>
<style scoped>
</style>
