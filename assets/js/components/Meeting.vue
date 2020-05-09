<template>
    <div>
        <div v-if="this.refresh && meetings.length > 0 && this.room !== null" class="col mt-3 pl-2">
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
        <div v-else-if="meetings.length > 0 && this.room" class="col mt-3 pl-2" id="no-seance">
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
                refresh: true,
                hubLink: null
            }
        },
        mounted() {
            this.getMeeting();
            // mercure hub
            let hubUrl = this.hubLink ?? "http://localhost:3000/.well-known/mercure"
            const url = new URL(`${hubUrl}?topic=${document.location.origin}/api/meetings/{id}`);
            const eventSource = new EventSource(url);
            // The callback will be called every time an update is published
            eventSource.onmessage = (e) => {
                let data = JSON.parse(e.data)
                const meetings = [...this.meetings]

                meetings.forEach(function (m, index) {
                    if (m.id === data.id) {
                       meetings[index] = data
                    }
                })
                this.refresh = false
                this.meetings = meetings
                this.refresh = true
            }
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
                            this.hubLink = headers['link'].match(/<([^>]+)>;\s+rel="[^"]*mercure[^"]*"/)[1].replace('mercure/', 'localhost:3000/')
                            this.meetings = data['hydra:member'];
                            if (this.meetings.length === 0) {
                                let val = document.getElementById('reservation_room').value
                                document.getElementById('reservation_room').options[val - 1].disabled = true
                            } else {
                                setTimeout(function () {
                                    document.getElementById('reservation_seance').options['selectedIndex'] = 0
                                }, 100)
                            }
                        }).catch((error) => console.log(error));
                }
            }
        }
    }
</script>