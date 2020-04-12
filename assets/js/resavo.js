import axios from 'axios';
import Vue from 'vue';

window.axios = axios;
window.Vue = Vue;

import DatePicker from "./components/DatePicker"
import Payment from "./components/Payment"
import Vuetify from "vuetify";

Vue.use(Vuetify);

document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById('app')) {
        new Vue({
            el: '#app',
            vuetify: new Vuetify(),
            render: function (h) {
                return (
                    <div>
                        <DatePicker/>
                        <Payment/>
                    </div>
                )
            }
        })
    }
})

