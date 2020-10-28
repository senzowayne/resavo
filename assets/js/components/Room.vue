<template>
    <div class="col">
        <div class="col pl-2">
        <span class="pb-2">
            <strong>
                Vous avez sélectionné la salle :
            </strong>
        </span>
            <select :disabled="maintenance" ref="reservation_room" id="reservation_room" name="reservation[room]"
                    class="form-control"
                    v-model="roomSelected">
                <option :value=null disabled>{{ maintenance ? 'Maintenance' : 'Sélectionnez votre salle'}}</option>
                <option :disabled="maintenance" v-for="room in rooms" :value="room.id"
                        @input="handleRoomSelected($event)">
                    {{ room.name }}
                </option>
            </select>
        </div>
        <Meeting v-bind:room="roomSelected" :date="this.date" :maintenance="maintenance"/>
    </div>
</template>

<script>
    import Meeting from "Components/Meeting";
    import store from "App/resavoStore";

    export default {
        name: "Room",
        props: ['date', 'maintenance'],
        components: {Meeting},
        data() {
            return {
                roomSelected: null,
                rooms: [],
            }
        },
        created() {
            this.getRoom();
        },
        watch: {
            roomSelected: function (newVal, oldVal) {
                store.commit('CHANGE_ROOM', newVal)
                store.commit('CHANGE_ROOM_TEXT', this.$refs.reservation_room.options[document.getElementById('reservation_room').selectedIndex].text)
            }
        },
        methods: {
            handleRoomSelected(val) {
                this.roomSelected = val;
            },
            getRoom() {
                axios.get(`/api/rooms`)
                    .then(({data}) => {
                        this.rooms = data['hydra:member'];
                    }).catch(function (error) {
                    console.log(error);
                })
            }
        }
    }
</script>