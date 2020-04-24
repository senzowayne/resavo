<template>
    <div class="col">
        <div class="col pl-2">
        <span class="pb-2">
            <strong>
                Vous avez selectionné la salle :
            </strong>
        </span>
            <select id="reservation_room" name="reservation[room]" class="form-control" v-model="roomSelected">
                <option v-for="room in rooms" :value="room.id" @input="handleRoomSelected($event)">
                    {{ room.name }}
                </option>
            </select>
        </div>
        <Meeting v-bind:room="roomSelected" :date="this.date" />
    </div>
</template>

<script>
    import Meeting from "./Meeting";
    import store from "../resavoStore";

    export default {
        name: "Room",
        props: ['date'],
        components: { Meeting },
        data() {
            return {
                roomSelected: 1,
                rooms: [],
            }
        },
        created() {
            this.getRoom();
        },
        watch: {
            roomSelected: function (newVal, oldVal) {
                store.commit('CHANGE_ROOM', newVal)
            }
        },
        methods: {
            handleRoomSelected(val) {
                this.roomSelected = val;
            },
            getRoom() {
                axios.get(`/api/rooms`)
                    .then(({data}) => {
                        // handle success
                        this.rooms = data['hydra:member'];
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