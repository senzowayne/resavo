<template>
    <div>
        <div v-show="isAvailable && display" class="alert alert-success" role="alert"><i class="fas fa-check"></i>{{message}}</div>
        <div v-show="!isAvailable && display" class="alert alert-danger" role="alert"><i class="fas fa-times"></i>{{message}}</div>
    </div>
</template>

<script>
    export default {
        name: "available",
        props: ['room', 'meeting'],
        data() {
            return {
                message: '',
                display: false,
                isAvailable: true,
            }
        },
        watch: {
            room: function (newVal, oldVal) {
                this.getAvailable();
            },
            meeting: function (newVal, oldVal) {
                this.getAvailable();
            },
        },
        methods: {
            handleIsAvailable(val) {
                this.isAvailable = val;
            },
            getAvailable() {
                var date = document.getElementById('dateSelected').value;
                axios({
                    url: `https://127.0.0.1:8000/reservation/verif/dispo?room=${this.room}&meeting=${this.meeting}&date=${date}`,
                    method: 'get'
                }).then(({data}) => {
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