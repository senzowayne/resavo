<template>
    <div class="col mt-3 pl-2">
           <span class="pb-2">
            <strong>
                Vous avez selectionner la salle :
            </strong>
        </span>
        <select id="reservation_seance" name="reservation[meeting]" class="form-control" v-model="meetingSelected">
            <option v-for="meeting in meetings" :value="meeting.id" @input="handleMeetingSelected($event)">
                {{ meeting.label }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "Meeting",
        props: ['room'],
        data() {
            return {
                meetingSelected: 1,
                meetings: [],
                message: 'Hello Vue'
            }
        },
        created() {
            this.getMeeting();
        },
        watch: {
            room: function(newVal, oldVal) {
                this.getMeeting();
            }
        },
        methods: {
            handleMeetingSelected(val) {
                this.meetingSelected = val;
            },
            getMeeting() {
                axios.get(`https://127.0.0.1:8000/api/meetings?room=${this.room}`)
                    .then(({data}) => {
                        // handle success
                        this.meetings = data['hydra:member'];
                        console.log(data['hydra:member']);
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