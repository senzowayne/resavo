<template>
    <section>
        <div v-show="$store.state.isAvailable && !this.loading" class="row mt-4" id="payMe">
            <div class="card text-center col">
                <div class="card-header">
                    Pour valider votre reservation
                </div>
                <div class="card-body">

                    <p class="card-text" id="validatePay">
                    <div id="pay"></div>
                    <span><a id="annuler" href="#">Revenir à la réservation</a></span>
                </div>
                <div class="card-footer text-muted">
                    Version beta
                </div>
            </div>
        </div>
        <div v-show="$store.state.isAvailable && this.loading" class="row mt-4">
            <div class="card text-center col">
                <div class="card-header">
                    Veuillez patienter
                </div>
                <div class="card-body">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <br>
                    {{ message }}
                </div>
                <div class="card-footer text-muted">
                    Version beta
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import store from "App/resavoStore";

    export default {
        store: store,
        mounted: function () {
            const script = document.createElement("script");
            script.src =
                "https://www.paypal.com/sdk/js?client-id=Afbh-Bgw6uzC1YvdeuMSIOenKPYHNrFL3EAGqS2MpGIA2ts3TuXxKLvNpUVZNcDQCN6sVIlVfFQdkKr3&currency=EUR&debug=false&disable-card=amex&intent=authorize";
            script.addEventListener("load", this.setLoaded);
            document.body.appendChild(script);
        },
        methods: {
            setLoaded: function () {
                paypal.Buttons({
                    createOrder: (data, actions) => {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '45'
                                }
                            }]
                        });
                    },
                    onApprove: (data, actions) => {
                        actions.order.authorize().then((authorization) => {
                            var authorizationID = authorization.purchase_units[0]
                                .payments.authorizations[0].id
                            const data = {
                                id: authorization.id,
                                authorization: authorization,
                                authorizationID: authorizationID
                            };
                            this.loading = true;
                            this.message = 'Vérification du paiement ..'
                            axios.post('/reservation/paypal-transaction-complete?id=' + authorizationID, data)
                                .then((reponse) => {
                                    this.message = 'Enregistrement de votre réservation..'
                                    axios({
                                        method: 'post',
                                        url: '/reservation/api-reserve',
                                        data: {
                                            'date': store.state.date,
                                            'meeting': store.state.meeting,
                                            'room': store.state.room,
                                            'nbPerson': 2,
                                            'notices': '',
                                            'total': 90,
                                        }
                                    }).then(({data}) => {
                                        if (data.error !== '') {
                                            this.message = 'Il semble y avoir une erreur, veuillez nous contacter'
                                            store.commit('CHANGE_NOTIF_DISPLAY', true)
                                            store.commit('CHANGE_NOTIF_MSG', data.msg)
                                            setTimeout(() =>
                                                    store.commit('CHANGE_NOTIF_DISPLAY', false)
                                                , 10000)
                                        } else {
                                            window.location.href = "/reservation/resume";
                                        }
                                    }).catch((erreur) => {
                                        this.message = 'Il semble y avoir une erreur, veuillez nous contacter'
                                        console.log(erreur);
                                    });
                                }).catch(function (erreur) {
                                //On traite ici les erreurs éventuellement survenues
                                console.log(erreur);
                            });
                        })
                    },
                    onError: err => {
                        console.log(err);
                    }
                }).render('#pay');
            }
        },
        data() {
            return {
                loading: false,
                message: ''
            }
        },
    }
</script>