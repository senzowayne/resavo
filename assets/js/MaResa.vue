<template>
    <div>
        <select id="reservation_seance" name="reservation[meeting]" class="form-control" v-model="meetingSelected">
            <option v-for="meeting in meetings" :value="meeting.id" @input="handleMeetingSelected($event)">
                {{ meeting.label }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "MaResa",
        data() {
            return {
                meetingSelected: '',
                meetings: [],
                message: 'Hello Vue'
            }
        },
        created() {
            this.getMeeting();
        },
        methods: {
            handleMeetingSelected(val) {
                this.meetingSelected = val;
            },
            getMeeting() {
                axios.get(`https://127.0.0.1:8000/api/meetings`)
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