import Vue from 'vue';
import Vuex from "vuex";
Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        date: '',
        meeting: 1,
        room: 1,
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
        }
    },
    getters:{
        date: state => state.date,
        room: state => state.room,
        meeting: state => state.meeting
    },
    actions: {
    }
})