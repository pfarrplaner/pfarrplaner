<template>
    <div v-if="loading"><span class="fa fa-spin fa-spinner"></span></div>
    <div v-else class="service-entry editable"     style="cursor: pointer;"
         title="Klicken, um diesen Eintrag zu bearbeiten" onclick="">
        <template v-if="null != service.special_location">
            <div class="service-time">
                {{ service.timeText }}
            </div>
            <span class="separator">|</span>
            <div class="service-location">{{ service.special_location }}</div>
        </template>
        <template v-else>
            <div class="service-time">
                {{ service.timeText }}
            </div>
            <span class="separator">|</span>
            <div class="service-location">{{ service.location.name }}</div>
            <img v-if="service.cc" src="/img/cc.png" :title="ccInfo()"/>
            <div class="service-team service-pastor">
                <span class="designation">P: </span>
                <span v-if="service.need_predicant" class="need-predicant">Prädikant benötigt</span>
                <span v-for="person in service.pastors">{{ person.name }} </span>
            </div>
            <div class="service-team service-organist">
                <span class="designation">O: </span>
                <span v-for="person in service.organists">{{ person.name }} </span>
            </div>
            <div class="service-team service-sacristan">
                <span class="designation">M: </span>
                <span v-for="person in service.sacristans">{{ person.name }} </span>
            </div>
            <div class="service-description" v-html="service.descriptionText"></div>
            <div class="service-description">
                <span v-if="service.baptisms.length > 0" :title="service.baptismsText"><span class="fa fa-water" ></span>{{ service.baptims.length }}</span>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "CalendarService",
        props: [
            'serviceId'
        ],
        data() {
            return {
                service: [],
                loading: true,
            }
        },
        created() {
            this.getData();
        },

        methods: {
            getData(api_url) {
                api_url = api_url || '/api/service/' + this.serviceId;
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.service = response;
                        this.loading = false;
                    })
                    .catch(err => console.log(err));
            },
            ccInfo: function() {
                return 'Parallel Kinderkirche ('+service.cc_location+') zum Thema '+service.cc_lesson+': '+service.cc_staff;
            }
        }
    }
</script>

<style scoped>

</style>