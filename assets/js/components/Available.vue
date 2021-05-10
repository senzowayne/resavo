<template>
    <div>
        <transition name="custom-classes-transition"
                    enter-active-class="animated fadeIn"
                    appear mode="in-out">
            <div v-show="isAvailable && display" class="alert light-blue lighten-5" role="alert">
                Vous avez sélectionné : <br>
                <small>Date : <strong>{{ this.date }}</strong> | Salle : <strong> {{ this.$store.getters.roomText
                    }}</strong> | Séance :
                    <strong>{{ this.$store.getters.meetingText }}</strong></small>
                <br>
                <i class="fas fa-check"></i>{{message}}
            </div>
        </transition>

        <transition name="custom-classes-transition"
                    enter-active-class="animated fadeIn"
                    appear mode="out-in">
            <div v-show="!isAvailable && display" class="alert deep-orange lighten-5" role="alert">
                Vous avez sélectionné : <br>
                <small>Date : <strong>{{ this.date }}</strong> | Salle : <strong> {{ this.$store.getters.roomText
                    }}</strong> | Séance :
                    <strong>{{ this.$store.getters.meetingText }}</strong></small>
                <br>
                <i class="fas fa-times"></i>{{message}}
            </div>
        </transition>
    </div>
</template>

<script>
    import store from "App/resavoStore";

    export default {
        name: "available",
        props: ['room', 'meeting', 'date'],
        store: store,
        data() {
            return {
                message: '',
                display: false,
                isAvailable: false,
            }
        },
        watch: {
            meeting: function () {
                this.getAvailable();
            },
            date: function () {
                this.getAvailable();
            },
            isAvailable: function (newVal) {
                store.commit('IS_AVAILABLE', newVal)
            }
        },
        methods: {
            getAvailable() {
                if (this.meeting !== null && this.room !== null && this.date !== null) {
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