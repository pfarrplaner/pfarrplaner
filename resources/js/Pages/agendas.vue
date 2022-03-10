<template>
    <admin-layout enable-control-sidebar="true" title="Agenden bearbeiten">
        <info-pane v-if="null != selectedAgenda" :agenda="selectedAgenda" />
        <div v-if="agendas.length > 0">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Agende</th>
                    <th>Beschreibung</th>
                    <th>Quelle</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="agenda in agendas" @click="openAgenda(agenda)" style="cursor: pointer" title="Klicken, um auszuwählen">
                    <td valign="top">{{ agenda.title }}</td>
                    <td valign="top">{{ agenda.description }}</td>
                    <td valign="top">{{ agenda.internal_remarks }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>Es sind noch keine Agenden angelegt.</div>
        <button class="btn btn-success" @click="createAgenda">Neue Agende anlegen</button>
    </admin-layout>
</template>

<script>
import moment from 'moment';

const InfoPane = () => import('../components/AgendaEditor/Pane/InfoPane');

export default {
    props: ['agendas'],
    components: {
        InfoPane,
    },
    data() {
        return {
            selectedAgenda: null,
            blockIndex: null,
            itemIndex: null,
            element: null,
        }
    },
    methods: {
        title(service) {
            return 'Liturgie für ' + moment(service.date).locale('de-DE').format('DD.MM.YYYY') + ', ' + service.timeText;
        },
        updateFocus(blockIndex, itemIndex, element) {
            this.blockIndex = blockIndex;
            this.itemIndex = itemIndex;
            this.element = element;
            this.showModal = true;
        },
        createAgenda() {
            axios.get(route('liturgy.agenda.create'))
                .then(response => {
                    return response.data;
                }).then(data => {
                this.selectedAgenda = data;
            });
        },
        openAgenda(agenda) {
            this.$inertia.get(route('liturgy.editor', agenda.slug));
        }
    }
}
</script>
<style scoped>
</style>
