<template>
    <admin-layout enable-control-sidebar="true" :title="title(service)">
        <info-pane :service="service" />
        <div class="row">
            <div class="col-6">
                <liturgy-tree :service="service" @update-focus="updateFocus"/>
            </div>
            <div class="col-6">
                <div v-if="element == null">Wähle ein Element aus der Ablaufliste, um es zu bearbeiten.</div>
                <details-pane v-else :element="element" />
            </div>
        </div>
    </admin-layout>
</template>

<script>
import moment from 'moment';
import EventBus from "../plugins/EventBus";
const InfoPane = () => import('../components/LiturgyEditor/Pane/InfoPane');
const LiturgyTree = () => import('../components/LiturgyEditor/Pane/LiturgyTree');
const DetailsPane = () => import('../components/LiturgyEditor/Pane/DetailsPane');

export default {
    props: ['service'],
    components: {
        InfoPane,
        LiturgyTree,
        DetailsPane,
    },
    data() {
        return {
            blockIndex: null,
            itemIndex: null,
            element: null,
        }
    },
    methods: {
        title(service) {
            return 'Liturgie für '+moment(service.day.date).locale('de-DE').format('DD.MM.YYYY')+', '+service.timeText;
        },
        updateFocus(blockIndex, itemIndex, element) {
            this.blockIndex = blockIndex;
            this.itemIndex = itemIndex;
            this.element = element;
        }
    }
}
</script>
<style scoped>
</style>
