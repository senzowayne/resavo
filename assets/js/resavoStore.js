import Vue from 'vue';
import Vuex from "vuex";
Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        date: new Date().toISOString().substr(0, 10),
        meeting: 1,
        room: 1,
        isAvailable: false,
        isMaintenance: false
    },
    mutations: {
        CHANGE_DATE: (state, newDate) => {
            state.date = newDate
        },
        CHANGE_MEETING: (state, newMeeting) => {
            state.meeting = newMeeting
        },
        CHANGE_ROOM: (state, newRoom) => {
            state.room = newRoom
        },
        IS_AVAILABLE: (state, bool) => {
            state.isAvailable = bool
        },
        IS_MAINTENANCE: (state, bool) => {
            state.isMaintenance = bool
        }
    },
    getters:{
        date: state => state.date,
        room: state => state.room,
        meeting: state => state.meeting,
        isAvailable: state => state.isAvailable,
        isMaintenance: state => state.isMaintenance
    },
    actions: {
    }
})