<template>
    <div class="col mt-3 pl-2">
           <span class="pb-2">
            <strong>
                Vous avez sélectionné la séance :
            </strong>
        </span>
        <select id="reservation_seance" name="reservation[meeting]" class="form-control" v-model="meetingSelected">
            <option v-for="meeting in meetings" :value="meeting.id" @input="handleMeetingSelected($event)">
                {{ meeting.label }}
            </option>
        </select>
        <hr>
        <Available v-bind:room="this.room" v-bind:meeting="meetingSelected" :date="this.date"/>
    </div>
</template>

<script>
    import Available from "./Available";
    import store from "../resavoStore";

    export default {
        name: "Meeting",
        components: {Available},
        props: ['room', 'date'],
        store: store,
        data() {
            return {
                meetingSelected: 1,
                meetings: [],
            }
        },
        mounted() {
            this.getMeeting();
        },
        watch: {
            room: function () {
                this.getMeeting();
                this.meetingSelected = document.getElementById('reservation_seance').options[0].value
            },
            date: function () {
                const seance = document.getElementById('reservation_seance')
                this.meetingSelected = seance.options[seance.options['selectedIndex']].value
            },
            meetingSelected: (newVal) => {
                store.commit('CHANGE_MEETING', newVal)
            }
        },
        methods: {
            handleMeetingSelected(val) {
                this.meetingSelected = val;
            },
            getMeeting() {
                axios.get(`/api/meetings?room=${this.room}&date=${this.date}`)
                    .then(({data}) => {
                        this.meetings = data['hydra:member'];
                        setTimeout(function () {
                            document.getElementById('reservation_seance').options['selectedIndex'] = 0
                        }, 100)
                    }).catch((error) => console.log(error));
            }
        }
    }
</script>