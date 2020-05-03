<template>
    <div v-show="$store.state.isAvailable" class="row mt-4" id="payMe">
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
</template>

<script>
    import store from "../resavoStore";

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
                this.loaded = true;
                paypal.Buttons({
                    createOrder: function (data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '45'
                                }
                            }]
                        });
                    },
                    onApprove: function (data, actions) {
                        actions.order.authorize().then(function (authorization) {
                            var authorizationID = authorization.purchase_units[0]
                                .payments.authorizations[0].id
                            const data = {
                                id: authorization.id,
                                authorization: authorization,
                                authorizationID: authorizationID
                            };
                            axios.post('/reservation/paypal-transaction-complete?id=' + authorizationID, data)
                                .then(function (reponse) {
                                    const resume = document.getElementById('resume');
                                    axios({
                                        method: 'post',
                                        url: '/reservation/api-reserve',
                                        data: {
                                            'date': resume.dataset.date,
                                            'meeting': resume.dataset.meeting,
                                            'room': resume.dataset.room,
                                            'nbPerson': 2,
                                            'notices': '',
                                            'total': 90,
                                        }
                                    }).then(function (reponse) {
                                       window.location.href = "/reservation/resume";
                                    }).catch(function (erreur) {
                                        //On traite ici les erreurs éventuellement survenues
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
                loaded: false,
            }
        },
    }
</script>