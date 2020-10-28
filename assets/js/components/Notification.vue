<template>
    <v-snackbar
            v-model="this.$store.getters.notifDisplay"
            :bottom="y === 'bottom'"
            :color="color"
            :left="x === 'left'"
            :multi-line="mode === 'multi-line'"
            :right="x === 'right'"
            :timeout="timeout"
            :top="y === 'top'"
            :vertical="mode === 'vertical'"
    >
        {{ this.$store.getters.notifMsg }}
        <v-btn
                dark
                text
                @click="handleDisplayNotif()"
        >
            Close
        </v-btn>
    </v-snackbar>
</template>

<script>
    import store from "App/resavoStore";

    export default {
        store: store,
        methods: {
            handleDisplayNotif() {
                store.commit('CHANGE_NOTIF_DISPLAY', false)
            }
        },
        data () {
            return {
                color: 'info',
                mode: '',
                timeout: 20000,
                x: 'right',
                y: 'top',
            }
        },
    }
</script>