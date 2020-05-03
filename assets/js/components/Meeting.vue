<template>
    <div>
        <div class="col mt-3 pl-2" v-if="meetings.length > 0 && this.room !== null">
           <span class="pb-2">
            <strong>
                Vous avez sélectionné la séance :
            </strong>
        </span>
            <select id="reservation_seance" name="reservation[meeting]" class="form-control" v-model="meetingSelected"
                    :data-seance="meetings.length">
                <option :value=null disabled>Sélectionnez votre séance</option>
                <option v-for="meeting in meetings" :value="meeting.id" @input="handleMeetingSelected($event)">
                    {{ meeting.label }}
                </option>
            </select>
            <small><span style="color: red">{{this.meetings.length}}</span> {{this.meetings.length > 1 ? 'séances' :
                'séance'}} disponible</small>
            <hr>
            <Available v-bind:room="this.room" v-bind:meeting="meetingSelected" :date="this.date"/>
        </div>
        <div v-else-if="this.room == null" class="col mt-3 pl-2">

        </div>
        <div v-else class="col mt-3 pl-2" id="no-seance">
           <span class="pb-2">
            <strong>
               Aucune séance n'est disponible pour cette salle !
            </strong>
        </span>
        </div>
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
                meetingSelected: null,
                meetings: [],
            }
        },
        mounted() {
            this.getMeeting();
        },
        watch: {
            room: function () {
                this.meetingSelected = null
                this.getMeeting();
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
                if (this.room !== null && this.date !== null) {
                    axios.get(`/api/meetings?room=${this.room}&date=${this.date}`)
                        .then(({data, headers}) => {
                            this.meetings = data['hydra:member'];
                            if (this.meetings.length === 0) {
                                let val = document.getElementById('reservation_room').value
                                document.getElementById('reservation_room').options[val - 1].disabled = true
                            } else {
                                setTimeout(function () {
                                    document.getElementById('reservation_seance').options['selectedIndex'] = 0
                                }, 100)
                            }
                            let hubUrl = "http://localhost:3000/.well-known/mercure"
                            const url = new URL(`${hubUrl}?topic=${document.location.origin}/api/meetings/{id}`);
                            const eventSource = new EventSource(url);
                            // The callback will be called every time an update is published
                            eventSource.onmessage = (e) => {
                                this.getMeeting()
                            }
                        }).catch((error) => console.log(error));
                }
            }
        }
    }
</script>