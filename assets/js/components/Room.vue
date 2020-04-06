<template>
    <div class="col">
        <div class="col mt-3 pl-2">
        <span class="pb-2">
            <strong>
                Vous avez selectionner la salle :
            </strong>
        </span>
            <select id="reservation_room" name="reservation[room]" class="form-control" v-model="roomSelected">
                <option v-for="room in rooms" :value="room.id" @input="handleRoomSelected($event)">
                    {{ room.name }}
                </option>
            </select>
        </div>
        <Meeting v-bind:room="roomSelected" />
    </div>
</template>

<script>
    import Meeting from "./Meeting";

    export default {
        name: "Room",
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
        methods: {
            handleRoomSelected(val) {
                this.roomSelected = val;
            },
            getRoom() {
                axios.get(`/api/rooms`)
                    .then(({data}) => {
                        // handle success
                        this.rooms = data['hydra:member'];
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