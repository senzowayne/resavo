<template>
    <div id="resa" class="row mt-3">
        <div class="mt-3 pl-2 col-4 col-sm offset-1"> <!-- start col -->
            <v-app id="inspire" style="height: 360px!important;">
                <v-row align="start">
                    <v-date-picker :min="min" :max="max"
                                   dark
                                   full-width
                                   locale="fr"
                                   :landscape="$vuetify.breakpoint.smAndUp"
                                   class="mt-4"
                                   year-icon="mdi-calendar-blank"
                                   prev-icon="mdi-skip-previous"
                                   next-icon="mdi-skip-next"
                                   v-model="picker"
                                   :allowed-dates="allowedDates">
                    </v-date-picker>
                </v-row>
            </v-app>
        </div> <!-- end col -->
        <div class="col">
            <Room v-bind:date="picker"/>
        </div>
    </div>
</template>

<script>
    import Room from "./Room";
    import Meeting from "./Meeting";
    import store from "../resavoStore";

    export default {
        components: {Room, Meeting},
        created() {
            store.commit('CHANGE_DATE', this.picker)
            this.getDisableDate();
        },
        watch: {
            picker: function (newVal) {
                store.commit('CHANGE_DATE', newVal)
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
                        let response = data['hydra:member'];
                        this.disableDate = response.map(function (date) {
                            return date.blockedDate.substr(0, 10);
                        })
                    })
            }
        },
        data() {
            return {
                picker: new Date().toISOString().substr(0, 10),
                min: new Date().toISOString().substr(0, 10),
                max: (new Date(new Date().setMonth(new Date().getMonth() + 3))).toISOString().substr(0, 10),
                disableDate: []
            }
        }
    }
</script>