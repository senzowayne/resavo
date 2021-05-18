<template>
    <div v-if="refresh" id="resa" class="row mt-3">
        <div class="col-sm-12 col-md-6 mb-4"> <!-- start col -->
            <v-app id="inspire" style="height: 390px!important;margin: 10px;overflow: hidden;">
                    <v-date-picker :min="min" :max="max"
                                   dark
                                   height="100px"
                                   locale="fr"
                                   :landscape="$vuetify.breakpoint.smAndUp"
                                   class="mt-4"
                                   width="auto"
                                   year-icon="mdi-calendar-blank"
                                   prev-icon="mdi-skip-previous"
                                   next-icon="mdi-skip-next"
                                   v-model="picker"
                                   :disabled="maintenance"
                                   :allowed-dates="allowedDates"
                                   first-day-of-week="1"
                    >
                    </v-date-picker>
            </v-app>
        </div> <!-- end col -->
        <div class="col">
            <Room v-bind:date="picker" :maintenance="maintenance"/>
        </div>
    </div>
</template>

<script>
    import Room from "Components/Room";
    import Meeting from "Components/Meeting";
    import store from "App/resavoStore";
    import axios from "axios";

    export default {
        props: ['maintenance'],
        components: {Room, Meeting},
        created() {
            store.commit('CHANGE_DATE', this.picker)
            this.getDisableDate();
        },
        watch: {
            picker: function (newVal) {
                store.commit('CHANGE_DATE', newVal)
            },
            refresh: function () {

            }
        },
        methods: {
            allowedDates(date) {
                // Ne pas tomber sur currentDate si celle-ci est bloquée
                if (this.disableDate.includes(this.picker) && (this.min < date && this.max > date)) {
                    this.picker = date
                }
                return !this.disableDate.includes(date);
            },
            getDisableDate() {
                axios.get(`/api/date_blockeds`)
                    .then(({data}) => {
                        this.disableDate = data['hydra:member']
                        this.refresh = true
                        // disable current date
                        let date = new Date().toISOString().substr(0, 10)
                        this.disableDate.push(date)
                    })
            }
        },
        data() {
            return {
                picker: new Date().toISOString().substr(0, 10),
                min: new Date().toISOString().substr(0, 10),
                max: (new Date(new Date().setMonth(new Date().getMonth() + 3))).toISOString().substr(0, 10),
                disableDate: [],
                refresh: false
            }
        }
    }
</script>