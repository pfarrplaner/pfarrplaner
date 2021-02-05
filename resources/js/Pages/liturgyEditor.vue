<template>
    <admin-layout enable-control-sidebar="true" :title="title(service)">
        <info-pane :service="service" />
        <div class="row">
            <div class="col-12">
                <liturgy-tree :service="service" :sheets="liturgySheets" @update-focus="updateFocus"/>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import moment from 'moment';

const InfoPane = () => import('../components/LiturgyEditor/Pane/InfoPane');
const LiturgyTree = () => import('../components/LiturgyEditor/Pane/LiturgyTree');

export default {
    props: ['service', 'liturgySheets'],
    components: {
        InfoPane,
        LiturgyTree,
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
            this.showModal = true;
        }
    }
}
</script>
<style scoped>
</style>
