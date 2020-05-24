import Vue from 'vue';
import Vuex from "vuex";
Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        date: new Date().toISOString().substr(0, 10),
        meeting: 1,
        meetingText: '',
        room: 1,
        roomText: '',
        isAvailable: false,
        isMaintenance: false,
        notifDisplay: false,
        notifMsg: ''
    },
    mutations: {
        CHANGE_NOTIF_DISPLAY: (state, bool) => {
            state.notifDisplay = bool
        },
        CHANGE_NOTIF_MSG: (state, msg) => {
            state.notifMsg = msg
        },
        CHANGE_DATE: (state, newDate) => {
            state.date = newDate
        },
        CHANGE_MEETING: (state, newMeeting) => {
            state.meeting = newMeeting
        },
        CHANGE_MEETING_TEXT: (state, newMeeting) => {
            state.meetingText = newMeeting
        },
        CHANGE_ROOM: (state, newRoom) => {
            state.room = newRoom
        },
        CHANGE_ROOM_TEXT: (state, newRoom) => {
            state.roomText = newRoom
        },
        IS_AVAILABLE: (state, bool) => {
            state.isAvailable = bool
        },
        IS_MAINTENANCE: (state, bool) => {
            state.isMaintenance = bool
        }
    },
    getters:{
        notifDisplay: state => state.notifDisplay,
        notifMsg: state => state.notifMsg,
        date: state => state.date,
        room: state => state.room,
        roomText: state => state.roomText,
        meeting: state => state.meeting,
        meetingText: state => state.meetingText,
        isAvailable: state => state.isAvailable,
        isMaintenance: state => state.isMaintenance
    },
    actions: {
    }
})