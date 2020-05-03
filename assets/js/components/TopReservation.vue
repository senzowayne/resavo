<template>
    <section>
        <h1 class="col text-center text-capitalize">{{ this.name }} Réservation</h1>
        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link btn btn-sm blue-grey lighten-3" href="/user/historique">Mes Réservations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn btn-sm  blue-grey darken-4" href="/deconnexion">Déconnexion</a>
            </li>
        </ul>
        <transition name="custom-classes-transition"
                    enter-active-class="animated fadeIn"
                    appear mode="out-in">
            <div v-show="this.maintenance" class="alert deep-purple lighten-5 text-center m-3" role="alert">
                <i class="fas fa-exclamation-triangle"></i> &nbsp; Maintenance
            </div>
        </transition>
        <hr>
    </section>
</template>

<script>
    export default {
        name: "Top",
        props: ['maintenance', 'name']
    }
</script>