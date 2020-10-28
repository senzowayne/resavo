import axios from 'axios';
import Vue from 'vue';
import Vuex from 'vuex'
import 'es6-promise/auto'

window.axios = axios;
window.Vue = Vue;
import Reservation from 'Pages/reservation'
import Notification from "Components/Notification";
import Vuetify from "vuetify";

Vue.use(Vuetify);
Vue.use(Vuex);

document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById('app')) {
        new Vue({
            el: '#app',
            vuetify: new Vuetify(),
            render: function (h) {
                return (
                    <div>
                        <Notification />
                        <Reservation />
                    </div>
                )
            }
        })
    }
})

