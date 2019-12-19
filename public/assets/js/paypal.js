$('#payMe').hide();
$('#resa1').hide();
$('#buttonValide').prop('disabled', true);
valueDate = null;
jour = new Date().getDate();
lmois = (new Date().getMonth() + 1);
mois = '';
if (mois < 9) {
  mois = 0 + '' + lmois
}
annee = new Date().getFullYear();
valueSalle = 1;
console.log('ready');

/***************
 VERIFIE INPUT HIDDEN POUR SAVOIR DANS QUEL SALLE ON SE SITUE
 VARIABLE ENVOYER DEPUIS LE CONTROLLER
 ****************/

num = $('#number-salle');
if (num.val() == 1 || num.val() == 'bora-bora') {
  $('#reservation_salle').prop('disabled', true);
  $('#reservation_seance options[0]').prop('selected', true);
  valueSalle = 1;
  acc = 38
}
if (num.val() == 2 || num.val() == 'miami') {
  $('#reservation_salle option[value="2"]').prop('selected', true);
  $('#reservation_salle').prop('disabled', true);
  $('#reservation_seance options[0]').prop('selected', true);
  valueSalle = 2;
  acc = 45
}
if (num.val() == 3 || num.val() == 'phuket') {
  $('#reservation_salle option[value="3"]').prop('selected', true);
  $('#reservation_salle').prop('disabled', true);
  valueSalle = 3;
  acc = 45
}

// Fonction pour ajouter une remarque
$('#btn-remarque').click(function() {
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
  $('#reservation_nbPersonne').append($('<option>', {
    value: i,
    text: i
  }));
}

$('#reservation_nbPersonne').attr('max', 3);
messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">4</span> personnes';
$('#messageSalle').append(messageSalle);
//$('#valide').hide();

//revenir a la reservation
$('#annuler').on('click', function() {
  $('#payMe').hide("slow");
  $('#reservation_salle, #reservation_seance, #reservation_date_reservation, #reservation_remarques, #reservation_nbPersonne').prop('disabled', false);
  $('#resa, #resa1').show();
});

// Methode Confirmer la reservation
$('#buttonValide').click(function() {
  if (confirm('Valider et Procéder au paiement')) {
    $('html, body').animate({
      scrollTop: 0
    }, 'slow');
    $('#payMe').show("slow");
    resume()
    $('#reservation_salle, #reservation_seance, #reservation_date_reservation, #reservation_remarques, #reservation_nbPersonne').prop('disabled', true);
    $('#resa, #resa1').hide();
  }
});

//Initialise le nombre de personnes et le prix selon la salle
if (valueSalle == '1' || valueSalle == 'bora-bora') {
  valueSalle = 1;
  prix = 75;
  $('#reservation_nbPersonne').empty();
  $('#messageSalle').empty();

  for (i = 1; i <= 4; i++) {
    $('#reservation_nbPersonne').append($('<option>', {
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
    $('#reservation_nbPersonne').append($('<option>', {
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
    $('#reservation_nbPersonne').append($('<option>', {
      value: i,
      text: i
    }));
  }

  messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">8</span> personnes';
}

$('#messageSalle').append(messageSalle);

/*
* ***********************************************
 NOMBRE DE PERSONNES EN FONCTION DE LA SALLE ****
 */
salle = $('#reservation_salle').on('change', function() {
  if (this.value == '1' || valueSalle == 'bora-bora') {
    valueSalle = 1;
    prix = 75;
    $('#reservation_nbPersonne').empty();
    $('#messageSalle').empty();

    for (i = 1; i <= 4; i++) {
      $('#reservation_nbPersonne').append($('<option>', {
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
      $('#reservation_nbPersonne').append($('<option>', {
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
      $('#reservation_nbPersonne').append($('<option>', {
        value: i,
        text: i
      }));
    }

    messageSalle = 'Cette salle ne peut comporter maximum <span class="text-danger">8</span> personnes';
  }
  $('#messageSalle').append(messageSalle);
});

// Si quelconque changement sur nombre de personne ré-iniatilise la variable avec la nouvelle valeur
nbPersonne = $('#reservation_nbPersonne').on('change', function() {
  valuePersonnes = $('#reservation_nbPersonne').val();

});

// Si quelconque changement sur la date ré-iniatilise la variable avec la nouvelle valeur
date = $('#reservation_date_reservation').on('change', function() {
  valueDate = $('#reservation_date_reservation').val();

  //console.log(valueDate);
});

// Si quelconque changement sur séance ré-iniatilise la variable avec la nouvelle valeur
$('#reservation_seance').on('change', function() {
  valueSeance = $('#reservation_seance').val();
  valueTextSeance = $('#reservation_seance').find(":selected").text()
  //console.log(valueSeance);
});

// Si quelconque changement sur tout les champs ré-iniatilise la variable avec la nouvelle valeur
$('#reservation_date_reservation, #reservation_nbPersonne, #reservation_salle, #reservation_seance').on('change', function() {
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

  //Si Salle egal 2 et personne superieur ou egale a 3 à 6 alors ajoute 35euros et de 7 & ajoute 20euro
  if (valueSalle === 2 && valuePersonnes >= 3 && valuePersonnes <= 6) {
    prix = 90;
    prix += ((valuePersonnes - 2) * 35);
    acc = (90 / 2);
  } else if (valueSalle === 2 && valuePersonnes >= 7 && valuePersonnes <= 8) {
    prix = 90;
    if (valuePersonnes == 7) {
      prix = (230 + 20);
    }
    if (valuePersonnes == 8) {
      prix = (230 + 40);
    }
    acc = (90 / 2);
  }

  //Si Salle egal 3 et personne superieur ou egale a 3 à 6 alors ajoute 35euros et de 7 & ajoute 20euro
  if (valueSalle === 3 && valuePersonnes >= 3 && valuePersonnes <= 6) {
    prix = 90;
    prix += ((valuePersonnes - 2) * 35);
    acc = (90 / 2);
  } else if (valueSalle === 3 && valuePersonnes >= 7 && valuePersonnes <= 8) {
    prix = 90;
    if (valuePersonnes == 7) {
      prix = (230 + 20);
    }
    if (valuePersonnes == 8) {
      prix = (230 + 40);
    }
    acc = (90 / 2);
  }
  checkWeek()
  $('#tarif').empty();

  //Si Seance du soir selectionner un jour de semaine
  if ($("#reservation_seance option:selected").index() > 3 && valueweekEnd == 0) {
    valueweekEnd = 1;
    prix += 30; //tarif nuit
    $('#tarif').empty()
    $('#tarif').append('<span class="badge badge-pill badge-warning">Tarif Soir +30€</span>')
    //  console.log('seance > 2 tarif nuit --- index ' + $("#reservation_seance option:selected").index() )
    // Si séance
  } else if ($("#reservation_seance option:selected").index() > 3 && valueweekEnd == 1) {
    prix += 30;
    $('#tarif').empty()
    $('#tarif').append('<span class="badge badge-pill red accent-4">Tarif Week End +30€</span>')
    //  console.log('seance < 2 tarif weekEnd')

  } else if ($("#reservation_seance option:selected").index() < 3 && valueweekEnd == 0) {
    valueweekEnd = 0

  } else if (valueweekEnd == 1) {
    prix += 30;
    $('#tarif').empty()
    $('#tarif').append('<span class="badge badge-pill badge-success red accent-4">Tarif Week End +30€</span>')
    //console.log('value week = 1')
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
  //console.log(valueSeance);
  if (valueSeance !== "0") {
    $('#buttonValide').prop("disabled", true);
  }
  setTimeout(() => {
    if (valueSeance !== "0") {
      verifDispo()
    }
  }, 900);

  if (jourSelect === "Vendredi" && ((valueSeance >= 6 && valueSeance <= 8) || (valueSeance >= 17 && valueSeance <= 19) || (valueSeance >= 28 && valueSeance <= 30))) {
    prix - 30;
    valueweekEnd = 0
  }
  resume();
});

// verifie qu'on est bien en week-end
function checkWeek() {
  if (jourSelect != "Vendredi" && jourSelect != "Samedi" && jourSelect != "Dimanche") {
    valueweekEnd = 0
  } else if (jourSelect === "Vendredi" && ((valueSeance >= 6 && valueSeance <= 8) || (valueSeance >= 17 && valueSeance <= 19) || (valueSeance >= 28 && valueSeance <= 30))) {
    valueweekEnd = 0
  } else {
    valueweekEnd = 1
  }
}

// Au changement dans la date
$('#reservation_date_reservation').on('change', function() {
  checkWeek();
  valueSeance = "0";
  valueTextSeance = $('#reservation_seance').find(":selected").text()
})

// Si quelconque changement sur remarque ré-iniatilise la variable avec la nouvelle valeur
$('#reservation_remarques').on('change', function() {
  valueRemarque = $('#reservation_remarques').val();
})

/* FONCTION BUTTON PAYPAL */
paypal.Buttons({
  createOrder: function(data, actions) {
    return actions.order.create({
      purchase_units: [{
        amount: {
          value: acc
        }
      }]
    });
  },
  onApprove: function(data, actions) {

        // Authorize the transaction
    return actions.order.authorize().then(function(authorization) {

          // Get the authorization id
          var authorizationID = authorization.purchase_units[0]
            .payments.authorizations[0].id
      $('#pay').empty().append('<h2><i class="fas fa-check"></i><span class="text-success"> PAIMENT VALIDER </span></h2><br>' +
        '<strong>Patientez un instant vous aller être rediriger sur la page de recapitilatif</strong>');
      $('#validatePay').empty();
      sendOrder(authorization, authorizationID);
      setTimeout(function(){
        createReservation();
      }, 2600);
    });
  }
}).render('#pay');

function sendOrder(authorization, authorizationID) {
    $.ajax({
    url: '/reservation/paypal-transaction-complete',
    method: 'post',
      data: {
          authorization: authorization,
          authorizationID: authorizationID,
      },
    beforeSend: function() {
      $('#annuler').empty();
      $('#pay').empty().append(`<div class="d-flex align-items-center">
  <strong>Nous vérifions votre paiement...</strong>
  <div class="spinner-border ml-auto text-success" role="status" aria-hidden="true"></div>
</div>`);
    }
  })
}

function createReservation() {
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
        $('#pay').empty().append(`<div class="d-flex align-items-center">
    <strong>Enregistrement de votre réservation...</strong>
    <div class="spinner-border ml-auto text-success" role="status" aria-hidden="true"></div>
  </div>`);
      },
      success: function() {
        window.location.pathname = "/reservation/resume";
      }
    }).always(data => {
      //console.log(data);
    })
}


//Fonction qui genere le texte resumant la commande
function resume() {
  if (valueDate !== null && $("#reservation_seance option:selected").index() !== 0) {
    $('#resume').empty().append('<hr>Vous avez selectionné la date du : <strong>' + valueDate + '</strong> de <strong>' + $('#reservation_seance').find(":selected").text() + '</strong> pour <strong>' + valuePersonnes + ' personne' + pluriel + ' </strong> dans la <strong>' + nomSalle + '<br>Total de la commande : ' + prix + '€ <br> Seul un acompte de <span style="color:green"> ' + acc + '€ </span> vous sera prélevé lors du paiement en ligne </strong>');
  } else {
    $("#resume").empty();
    $("#dispo").empty();
  }
}


/* FONCTION DE DISPONIBILITE */
function verifDispo() {
  if (valueDate !== null && $("#reservation_seance option:selected").index() !== 0) {
    const PATH_SAVE_RECIT_CONTENT = '/reservation/verif/dispo';
    compare = {
      "message": "Cette réservation est deja prise"
    };
    $.ajax({
      url: PATH_SAVE_RECIT_CONTENT,
      method: 'post',
      dataType: 'json',
      data: {
        'date': valueDate,
        'seance': $('#reservation_seance').find(":selected").text(),
        'salle': valueSalle
      },
      beforeSend: function() {
        $('#dispo').empty().append(`<div class="text-center"><div class="spinner-grow text-success" role="status">
  <span class="sr-only">Loading...</span>
</div></div>`);
      },
      success: function(result) {
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
}
