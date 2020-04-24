<template>
    <div class="col mt-3 pl-2">
           <span class="pb-2">
            <strong>
                Vous avez selectionné la séance :
            </strong>
        </span>
        <select id="reservation_seance" name="reservation[meeting]" class="form-control" v-model="meetingSelected">
            <option v-for="meeting in meetings" :value="meeting.id" @input="handleMeetingSelected($event)">
                {{ meeting.label }}
            </option>
        </select>
        <hr>
        <Available v-bind:room="this.room" v-bind:meeting="meetingSelected" :date="this.date" />
    </div>
</template>

<script>

    import Available from "./Available";
    import store from "../resavoStore";

    export default {
        name: "Meeting",
        components: { Available },
        props: ['room', 'date'],
        store: store,
        data() {
            return {
                meetingSelected: 1,
                meetings: [],
            }
        },
        created() {
            this.getMeeting();

        },
        watch: {
            room: function(newVal, oldVal) {
                this.getMeeting();
                this.setDefaultValue();
            },
            meetingSelected: function (newVal, oldVal) {
                store.commit('CHANGE_MEETING', newVal)
            }
        },
        methods: {
            setDefaultValue() {
                switch (this.room) {
                    case 1 :
                        this.meetingSelected = 1;
                        break;
                    case 2 :
                        this.meetingSelected = 6;
                        break;
                    case 3 :
                        this.meetingSelected = 11;
                        break;
                }
            },
            handleMeetingSelected(val) {
                this.meetingSelected = val;
            },
            getMeeting() {
                axios.get(`/api/meetings?room=${this.room}`)
                    .then(({data}) => {
                        // handle success
                        this.meetings = data['hydra:member'];
                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error);
                    })
                    .then(function () {
                        // always executed
                    });
            }
        }
    }
</script>