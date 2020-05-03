<template>
    <div>
        <Top :maintenance="maintenance" :name="name"/>
        <DatePicker :maintenance="maintenance"/>
        <Payment/>
    </div>
</template>

<script>
    import DatePicker from "../components/DatePicker";
    import Payment from "../components/Payment";
    import axios from "axios";
    import store from "../resavoStore";
    import Top from "../components/TopReservation";

    export default {
        name: "Reservation",
        components: {Top, Payment, DatePicker},
        created() {
           this.isMaintenance()
        },
        methods: {
            isMaintenance() {
                axios.get('/api/config_merchants').then(({data}) => {
                    let config = data['hydra:member'][0];
                    this.maintenance = config.maintenance
                    this.name = config.nameMerchant
                    this.description = config.description
                    store.commit('IS_MAINTENANCE', this.maintenance)
                })
            },
        },
        data() {
            return {
                maintenance: false,
                name: '',
                description: ''
            }
        },
    }
</script>