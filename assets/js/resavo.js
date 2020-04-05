import axios from 'axios';
import Vue from 'vue';

window.axios = axios;
window.Vue = Vue;
import Metting from './components/Meeting.vue';
import Room from "./components/Room";

if (document.getElementById('app')) {
    new Vue({
        el: '#app',
        delimiters: ['${', '}'],
        render: function (h) {
            return (
                <div>
                < Room />
                </div>)
        }
    })
}
