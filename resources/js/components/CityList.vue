<template>
    <div>
        <div class="card mb-2" v-for="city in cities" v-bind:key="city.id">
            <div class="card-body ">
                <h4 class="card-title">{{ city.name }}</h4>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                cities: []
            };
        },

        created() {
            this.getCities();
        },

        methods: {
            getCities(api_url) {
                api_url = api_url || '/api/cities';
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.cities= response;
                    })
                    .catch(err => console.log(err));
            }
        }
    };
</script>