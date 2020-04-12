<template>
    <div>
        <div v-show="isAvailable && display" class="alert alert-success" role="alert">
            Vous avez selectionné : <br>
            <small>Date : <strong>{{ this.date }}</strong> | Salle : <strong>{{ this.roomText }}</strong> | Séance : <strong>{{ this.meetingText }}</strong></small>

            <br>
            <i class="fas fa-check"></i>{{message}}</div>
        <div v-show="!isAvailable && display" class="alert alert-danger" role="alert"><i class="fas fa-times"></i>{{message}}</div>

        <span style="display: none;" id="resume" :data-date="this.date" :data-room="this.room" :data-meeting="this.meeting" :data-available="this.isAvailable"></span>
    </div>
</template>

<script>
    export default {
        name: "available",
        props: ['room', 'meeting', 'date'],
        data() {
            return {
                message: '',
                display: false,
                isAvailable: true,
                roomText: null,
                meetingText: null
            }
        },
        watch: {
            room: function () {
                this.getAvailable();
            },
            meeting: function () {
                this.getAvailable();
            },
            date: function () {
                this.getAvailable();
            }
        },
        methods: {
            handleIsAvailable(val) {
                this.isAvailable = val;
            },
            getAvailable() {
                var date = document.getElementById('dateSelected').value;
                axios({
                    url: `/reservation/verif/dispo?room=${this.room}&meeting=${this.meeting}&date=${date}`,
                    method: 'get'
                }).then(({data}) => {
                    this.roomText = document.getElementById('reservation_room').options[document.getElementById('reservation_room').selectedIndex].text
                    this.meetingText = document.getElementById('reservation_seance').options[document.getElementById('reservation_seance').selectedIndex].text
                    if (data.available == false) {
                        this.isAvailable = false
                        this.message = " Cette séance est déjà prise."
                    } else {
                        this.isAvailable = true
                        this.message = " Cette séance est disponible."
                    }
                    this.display = true;
                    console.log(data);
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