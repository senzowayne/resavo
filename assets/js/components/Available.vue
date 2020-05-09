<template>
    <div>
        <transition name="custom-classes-transition"
                    enter-active-class="animated fadeIn"
                    appear mode="in-out">
            <div v-show="refresh && isAvailable && display" class="alert light-blue lighten-5" role="alert">
                Vous avez sélectionné : <br>
                <small>Date : <strong>{{ this.date }}</strong> | Salle : <strong>{{ this.roomText }}</strong> | Séance :
                    <strong>{{ this.meetingText }}</strong></small>
                <br>
                <i class="fas fa-check"></i>{{message}}
            </div>
        </transition>

        <transition name="custom-classes-transition"
                    enter-active-class="animated fadeIn"
                    appear mode="out-in">
            <div v-show="refresh && !isAvailable && display" class="alert deep-orange lighten-5" role="alert">
                Vous avez sélectionné : <br>
                <small>Date : <strong>{{ this.date }}</strong> | Salle : <strong>{{ this.roomText }}</strong> | Séance :
                    <strong>{{ this.meetingText }}</strong></small>
                <br>
                <i class="fas fa-times"></i>{{message}}
            </div>
        </transition>
        <span style="display: none;" id="resume" :data-date="this.date" :data-room="this.room"
              :data-meeting="this.meeting" :data-available="this.isAvailable"></span>
    </div>
</template>

<script>
    import store from "../resavoStore";

    export default {
        name: "available",
        props: ['room', 'meeting', 'date'],
        store: store,
        data() {
            return {
                message: '',
                display: false,
                refresh: true,
                isAvailable: false,
                roomText: '',
                meetingText: ''
            }
        },
        mounted() {
            // mercure hub
            let hubUrl = this.hubLink ?? "http://localhost:3000/.well-known/mercure"
            const url = new URL(`${hubUrl}?topic=${document.location.origin}/api/bookings/{id}`);
            const eventSource = new EventSource(url);
            // The callback will be called every time an update is published
            eventSource.onmessage = (e) => {
                let data = JSON.parse(e.data)
                console.log(data)
                if (data.room === `/api/rooms/${this.room}` &&
                    data.bookingDate.substr(0, 10) === this.date &&
                    data.meeting === `/api/meetings/${this.meeting}`
                ) {
                    this.getAvailable()
                }
            }
        },
        watch: {
            room: function () {
                this.handleText()
            },
            meeting: function () {
                this.handleText()
                this.getAvailable();
            },
            date: function () {
                if (typeof document.getElementById('reservation_room').options[document.getElementById('reservation_room').value] != 'undefined')
                    this.roomText = document.getElementById('reservation_room').options[document.getElementById('reservation_room').selectedIndex].text

                if (typeof document.getElementById('reservation_seance').options[document.getElementById('reservation_seance').value] != 'undefined')
                    this.meetingText = document.getElementById('reservation_seance').options[document.getElementById('reservation_seance').selectedIndex].text

                this.getAvailable();
            },
            isAvailable: function (newVal) {
                store.commit('IS_AVAILABLE', newVal)
            }
        },
        methods: {
            handleText() {
                this.roomText = document.getElementById('reservation_room').options[document.getElementById('reservation_room').selectedIndex].text
                this.meetingText = document.getElementById('reservation_seance').options[document.getElementById('reservation_seance').selectedIndex].text
            },
            getAvailable() {
                if (this.roomText !== '' && this.meetingText !== '' && this.meeting !== null && this.room !== null) {
                    axios({
                        url: "/api/booking/available",
                        method: 'post',
                        data: {
                            room: this.room,
                            meeting: this.meeting,
                            bookingDate: this.date
                        }
                    }).then(({data}) => {

                        if (data === false) {
                            this.isAvailable = false
                            this.message = " Cette séance n'est pas disponible."
                        } else {
                            this.isAvailable = true
                            this.message = " Cette séance est disponible."
                        }
                        this.display = true;
                    }).catch((error) => console.log(error))
                } else {
                    this.isAvailable = false
                    this.display = false
                }
            }
        }
    }
</script>