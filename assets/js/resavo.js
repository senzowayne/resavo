import axios from 'axios';
import Vue from 'vue';

window.axios = axios;
window.Vue = Vue;
import MaResa from './MaResa.vue';

if (document.getElementById('app')) {
    new Vue({
        el: '#app',
        delimiters: ['${', '}'],
        render: function (h) {
            return (
                <MaResa/>
            )
        }
    })
}
