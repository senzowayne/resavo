$('#payMe').hide();
$('#resa1').hide()

num = $('#number-salle');
if (num.val() == 1  || num.val() == 'bora-bora') {
    $('#reservation_salle').prop('disabled', true);
    $('#reservation_seance options[0]').prop('selected', true);
    valueSalle = 1;
}
if (num.val() == 2 || num.val() == 'miami') {
    $('#reservation_salle option[value="2"]').prop('selected', true);
    $('#reservation_salle').prop('disabled', true);
    $('#reservation_seance options[0]').prop('selected', true);
    valueSalle = 2;

}
if (num.val() == 3 || num.val() == 'phuket') {
    $('#reservation_salle option[value="3"]').prop('selected', true);
    $('#reservation_salle').prop('disabled', true);
    valueSalle = 3;

}

$('#btn-remarque').click(function () {
    $('#resa1').show()
})
    /***************
     INITIALITION
     ****************/

    $('form').attr('id', 'myForm');
    prix = 75;
    valueCheck = 0;
    valueSeance = 1;

    nomSalle = 'Salle Bora-Bora';
    pluriel = '';
    valuePersonnes = 1;
    for (i = 1; i <= 4; i++) {
        $('#reservation_nbPersonne').append($('<option>',
            {
                value: i,
                text: i
            }));
    }


    $('#reservation_nbPersonne').attr('max', 3);
    messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">4</span> personnes';
    $('#messageSalle').append(messageSalle);
    //$('#valide').hide();

//revenir a la reservation
    $('#annuler').on('click', function(){
            $('#payMe').hide("slow");
            $('#reservation_salle, #reservation_seance, #reservation_date_reservation, #reservation_remarques, #reservation_nbPersonne').prop('disabled', false);
            $('#resa, #resa1').show();
    });

// Methode Confirmer la reservation
    $('#buttonValide').click(function () {
        if (confirm('Valider et Procéder au paiement')) {
            $('html, body').animate({scrollTop: 0}, 'slow');
            $('#payMe').show("slow");
            resume()
            $('#reservation_salle, #reservation_seance, #reservation_date_reservation, #reservation_remarques, #reservation_nbPersonne').prop('disabled', true);
            $('#resa, #resa1').hide();
        }

    });
if (valueSalle == '1' || valueSalle == 'bora-bora') {
    valueSalle = 1;
    prix = 75;
    $('#reservation_nbPersonne').empty();
    $('#messageSalle').empty();

    for (i = 1; i <= 4; i++) {
        $('#reservation_nbPersonne').append($('<option>',
            {
                value: i,
                text: i
            }));
    }

    messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">4</span> personnes';

} else if (valueSalle == '2' || valueSalle == 'miami') {
    valueSalle = 2;
    prix = 90;
    $('#messageSalle').empty();
    $('#reservation_nbPersonne').empty();
    for (i = 1; i <= 8; i++) {
        $('#reservation_nbPersonne').append($('<option>',
            {
                value: i,
                text: i
            }));
    }

    messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">8</span> personnes';
} else {
    valueSalle = 3;
    prix = 90;
    $('#messageSalle').empty();
    $('#reservation_nbPersonne').empty();

    for (i = 1; i <= 8; i++) {
        $('#reservation_nbPersonne').append($('<option>',
            {
                value: i,
                text: i
            }));
    }

    messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">8</span> personnes';
}
//console.log(prix)
$('#messageSalle').append(messageSalle);
/*
* ***********************************************
 NOMBRE DE PERSONNES EN FONCTION DE LA SALLE ****
 */
    salle = $('#reservation_salle').on('change', function () {
        if (this.value == '1' || valueSalle == 'bora-bora') {
            valueSalle = 1;
            prix = 75;
            $('#reservation_nbPersonne').empty();
            $('#messageSalle').empty();

            for (i = 1; i <= 4; i++) {
                $('#reservation_nbPersonne').append($('<option>',
                    {
                        value: i,
                        text: i
                    }));
            }

            messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">4</span> personnes';

        } else if (this.value == '2' || valueSalle == 'miami') {
            valueSalle = 2;
            prix = 90;
            $('#messageSalle').empty();
            $('#reservation_nbPersonne').empty();
            for (i = 1; i <= 8; i++) {
                $('#reservation_nbPersonne').append($('<option>',
                    {
                        value: i,
                        text: i
                    }));
            }

            messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">8</span> personnes';
        } else {
            this.value = 3;
            prix = 90;
            $('#messageSalle').empty();
            $('#reservation_nbPersonne').empty();

            for (i = 1; i <= 8; i++) {
                $('#reservation_nbPersonne').append($('<option>',
                    {
                        value: i,
                        text: i
                    }));
            }

            messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">8</span> personnes';
        }
        //console.log(prix)
        $('#messageSalle').append(messageSalle);

    });






    nbPersonne = $('#reservation_nbPersonne').on('change', function () {
        valuePersonnes = $('#reservation_nbPersonne').val();

    });

    date = $('#reservation_date_reservation').on('change', function () {
        valueDate = $('#reservation_date_reservation').val();
     //console.log(valueDate);
    });

     $('#reservation_seance').on('change', function () {
        valueSeance = $('#reservation_seance').val();
        valueTextSeance = $('#reservation_seance').find(":selected").text()
        console.log(valueSeance);
    });

    $('#reservation_date_reservation, #reservation_nbPersonne, #reservation_salle, #reservation_seance').on('change', function () {
        if (valueSalle === 1) {
            nomSalle = 'Salle Bora-Bora';
        } else if (valueSalle === 2) {
            nomSalle = 'Salle Miami';
        } else {
            nomSalle = 'Salle Phuket';
        }
// SALLE 1 nbPersonne moins ou egal a 2
        if (valueSalle === 1 && valuePersonnes <= 2) {
            prix = 75;
            acc = (76 / 2)
            //console.log('SALLE 1 MOINS DE 2 PERSONNE : prix : ' + prix)
        } else if (valueSalle === 1 && valuePersonnes > 2) {
            prix = 75;
            acc = (76 / 2);
            prix += ((valuePersonnes - 2) * 30);
            //console.log('SALLE 1. PLUS DE 2 PERSONNE : prix : ' + prix)

        }
// SALLE 2 & 3 nbPersonne moins ou egal a 2

        if (valueSalle === 2 || valueSalle === 3 && valuePersonnes <= 2) {
            prix = 90;
            acc = (90 / 2);
        }


//Si Salle egal 2 et personne superieur ou egale a 3 à 5 alors ajoute 35euros et de 5 & ajoute 20euro
        if (valueSalle === 2 && valuePersonnes >= 3 && valuePersonnes <= 5) {
            prix = 90;
            prix += ((valuePersonnes - 2) * 35);
            acc = (90 / 2);
        } else if (valueSalle === 2 && valuePersonnes > 5 && valuePersonnes <= 8) {

            prix = 90;
            prix += ((valuePersonnes - 2) * 20);
            acc = (90 / 2);
        }

        //Si Salle egal 3 et personne superieur ou egale a 3 à 5 alors ajoute 35euros et de 5 & ajoute 20euro
        if (valueSalle === 3 && valuePersonnes >= 3 && valuePersonnes <= 5) {
            prix = 90;
            prix += ((valuePersonnes - 2) * 35);
            acc = (90 / 2);
        } else if (valueSalle === 3 && valuePersonnes > 5 && valuePersonnes <= 8) {
            prix = 90;
            prix += ((valuePersonnes - 2) * 20);
            acc = (90 / 2);
        }
checkWeek()
        $('#tarif').empty();

        //Si Seance du soir selectionner un jour de semaine
         if (valueSeance > 2 && valueweekEnd == 0) {
            valueweekEnd = 1;
                prix += 30; //tarif nuit
                $('#tarif').empty()
                $('#tarif').append('<span class="badge badge-pill badge-warning">Tarif Soir +30€</span>')
                console.log('seance > 2 tarif nuit')
                // Si séance 
            }  else if (valueSeance > 2 && valueweekEnd == 1) { 
                prix += 30;
                $('#tarif').empty()
                $('#tarif').append('<span class="badge badge-pill blue-gradient">Tarif Week End +30€</span>')
                console.log('seance < 2 tarif weekEnd')

            } else if (valueSeance < 2 && valueweekEnd == 0) { 
                valueweekEnd = 0

            } else if (valueweekEnd == 1) {
                prix += 30;
                 $('#tarif').empty()
                $('#tarif').append('<span class="badge badge-pill badge-success blue-gradient">Tarif Week End +30€</span>')
                console.log('value week = 1')
            } else {
                $('#tarif').empty();
                valueweekEnd = 0
            }
            if (valuePersonnes > 1) {
                pluriel = 's';
            } else {
                pluriel = '';
            }
            valueRemarque = $('#reservation_remarques').val();
            console.log(valueSeance);



        setTimeout(() => { verifDispo() }, 900);

        resume();
    });

 function checkWeek(){
    if (jourSelect != "Vendredi" && jourSelect != "Samedi" && jourSelect != "Dimanche"){
        valueweekEnd = 0
    }
    else {
        valueweekEnd = 1
    }
 }
    // Au changement dans la date
    $('#reservation_date_reservation').on('change', function (){
        checkWeek();
         valueSeance = $('#reservation_seance').find(":selected").val();
         valueTextSeance =  $('#reservation_seance').find(":selected").text()
    })

    // Augmente le prix si les séance de nuit sont selectionner

    

    $('#reservation_remarques').on('change', function() {
valueRemarque = $('#reservation_remarques').val();
    })




    /* FONCTION BUTTON PAYPAL */
    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: acc
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                // alert('Transaction completed by ' + details.payer.name.given_name);
                // Call your server to save the transaction
                $('#pay').empty().append('<h2><i class="fas fa-check"></i><span class="text-success"> PAIMENT VALIDER </span></h2><br>' +
                    '<strong>Patientez un instant vous aller être rediriger sur la page de recapitilatif</strong>');
                $('#validatePay').empty();
                //$('#valide').show();
                //$('#valide').prop("disabled", false);

                return fetch('/reservation/paypal-transaction-complete', {
                    method: 'post',
                    body: JSON.stringify({
                        orderID: data.orderID,
                    }),
                    beforeSend: function() {
                        $('#pay').empty().append('<div class="loader"></div> Nous vérifions votre paiement');
                    },
                    success:
                        setTimeout(function () {
                            $.ajax({
                                url: '/reservation/api-reserve',
                                method: 'post',
                                data: {
                                    'date': valueDate,
                                    'seance': valueTextSeance,
                                    'salle': valueSalle,
                                    'nbPersonne': valuePersonnes,
                                    'remarques': valueRemarque,
                                    'weekEnd': valueweekEnd,
                                    'total': prix,
                                },
                                beforeSend: function() {
                                    $('#pay').empty().append('<div class="loader"></div> Enregistrement de votre réservation');
                                },
                                success: function () {
                                    window.location.pathname = "/reservation/resume";

                                }
                            }).always(data => {
                                //console.log(data);
                            })
                        }, 1500)
                })
                //ajax reservation:

            });
        }
    }).render('#pay');

    function resume() {
        $('#resume').empty().append('<hr>Vous avez selectionné la date du : <strong>' + valueDate + '</strong> de <strong>' + $('#reservation_seance').find(":selected").text() + '</strong> pour <strong>' + valuePersonnes + ' personne'+pluriel+' </strong> dans la <strong>' + nomSalle + '<br>Total de la commande : ' + prix + '€ <br> Seul un acompte de <span style="color:green"> ' + acc + '€ </span> vous sera prélevé lors du paiement en ligne </strong>');
    }


    /* FONCTION DE DISPONIBILITE */
   // $('#buttonDispo').on('click', verifDispo);

    function verifDispo() {

       // $('#dispo').empty().append('Nous vérifions les disponibilitées');
        const PATH_SAVE_RECIT_CONTENT = '/reservation/verif/dispo';
        compare = {"message": "Cette réservation est deja prise"};
        $.ajax({
            url: window.location.protocol + '//' + window.location.hostname + PATH_SAVE_RECIT_CONTENT,
            method: 'post',
            dataType: 'json',
            data: {
                'date': valueDate,
                'seance':  $('#reservation_seance').find(":selected").text(),
                'salle': valueSalle
            },
            beforeSend: function () {
                $('#dispo').empty().append('<div class="loader"></div>');
            },
            success: function (result) {
                if (JSON.stringify(result) == JSON.stringify(compare)) {
                    $('#dispo').empty().append('<div class="alert alert-danger" role="alert"><i class="fas fa-times"></i> Cette séance est déjà prise</div>');
                    $('#buttonValide').prop("disabled", true);
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve('resolved');
                        }, 1300);
                    });

                } else {
                    $('#dispo').empty().append('<div class="alert alert-success" role="alert"><i class="fas fa-check"></i> Cette séance est disponible </div>');
                    $('#buttonValide').prop("disabled", false);
                }
            }
        }).always(data => {
            //console.log(data);
        });

    }




