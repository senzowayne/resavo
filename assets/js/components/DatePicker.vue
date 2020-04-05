<template>
    <v-app id="inspire">
        <v-row align="start">
            <v-date-picker dark locale="fr" full-width :landscape="$vuetify.breakpoint.smAndUp" class="mt-4" v-model="picker"></v-date-picker>
        </v-row>
    </v-app>
</template>

<script>
    new Vue({
        el: '#reservation_date_reservation',
        vuetify: new Vuetify(),
        data() {
            return {
                picker: new Date().toISOString().substr(0, 10)
            }
        }
    })
</script>