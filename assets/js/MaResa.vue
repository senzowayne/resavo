<template>
    <h1>{{this.tesst('Bienvenue')}} {{message}}</h1>
</template>

<script>
    export default {
        name: "MaResa",
        data() {
            return {
                message: 'Hello Vue !'
            }
        },
        methods: {
            tesst(text) {
                return text.toUpperCase()
            }
        }

    }
</script>

<style scoped>

</style>