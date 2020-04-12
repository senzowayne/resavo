<template>
    <div id="resa" class="row mt-3">
        <div class="mt-3 pl-2 col-4 col-sm offset-1">
            <v-app id="inspire" style="height: 360px!important;">
                <v-row align="start">
                    <v-date-picker dark locale="fr" full-width :landscape="$vuetify.breakpoint.smAndUp" class="mt-4"
                                   v-model="picker"></v-date-picker>
                    <input id="dateSelected" type="hidden" :value="picker">
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

    export default {
        components: {Room, Meeting},
        data() {
            return {
                picker: new Date().toISOString().substr(0, 10)
            }
        }
    }
</script>