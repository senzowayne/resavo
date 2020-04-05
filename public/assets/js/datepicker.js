    /***************
     INITIALISATION
     ****************/
jourSelect = "";
valueweekEnd= '';
tarifNuit = false;
jour = new Date().getDate();
lmois = (new Date().getMonth() + 1);
mois = '';
if (mois < 9) {
    mois = 0 + '' + lmois
}
annee = new Date().getFullYear();
// valueDate = jour + '-' + mois + '-' + annee

//Fonction qui charge les séances depuis la bdd
function horaires(select, valueweekEnd, horaire){
$.ajax({
        url: "/reservation/seance/horaire",
        method: 'post',
        data: {
            'room': select,
            'weekEnd': valueweekEnd
        },
        success: function (result) {
            /*  let seance = result;
            // let horaire;
            horaire.empty();
            horaire.append(
                $('<option>',
                    {
                        value: 0,
                        text: 'Sélectionnez une séance'
                    })
            );
            for (const key in result) {
                //console.log("key " + key + " has value " + result[key]);

              horaire.append($('<option>',
                    {
                        value: key,
                        text: result[key]
                    }))
            }*/

            $('this option:first').prop('selected', true);
            resume()
        }
    }).always(data => {
        //console.log(data);
    });
}

var dateBlocked = $('#dateBlocked').data('blocked');
    var unavailableDates = dateBlocked.split(',');
function unavailable(date) {
    dmy = ((date.getDate() < 10 ? '0' : '') + date.getDate()) + "-" + (((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1)) + "-" +date.getFullYear();
    if ($.inArray(dmy, unavailableDates) < 0) {
        return [true,"",""];
    } else {
        return [false,"","Indisponible"];
    }
}

    /***************
     INITIALITION DATEPICKER
     ****************/
jQuery(function($){
    $.datepicker.regional['fr'] = {
        showOptions: { direction: "up" },
        closeText: 'Fermer',
        prevText: '&#x3c;Préc',
        nextText: 'Suiv&#x3e;',
        currentText: 'Aujourd\'hui',
        monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
            'Juillet','Aout','Septembre','Octobre','Novembre','Décembre'],
        monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
            'Jul','Aou','Sep','Oct','Nov','Dec'],
        dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
        dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
        dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
        weekHeader: 'Sm',
        // defaultDate: new Date(),
        dateFormat: 'dd-mm-yy',
        setDate: "-0d",
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        beforeShowDay: unavailable,
        //minDate: new Date() + '0M + 2D',
        minDate: '+2d',
        maxDate: '+12M +0D',
        showButtonPanel: true,
        showAnim: "fadeIn",
        onSelect: (function(dateText){
            //valueDate = $(this).val(dateText);
            let seldate = $(this).datepicker('getDate');
            seldate = seldate.toDateString();
            seldate = seldate.split(' ');
            const weekday = [];
            weekday['Mon']="Lundi";
            weekday['Tue']="Mardi";
            weekday['Wed']="Mercredi";
            weekday['Thu']="Jeudi";
            weekday['Fri']="Vendredi";
            weekday['Sat']="Samedi";
            weekday['Sun']="Dimanche";
        var weekOfDay = weekday[seldate[0]];
        jourSelect = weekday[seldate[0]];
        if (weekOfDay !== "Vendredi" && weekOfDay !== "Samedi" && weekOfDay !== "Dimanche"){
            valueweekEnd = 0;
            horaires($("#booking_room").val(), valueweekEnd)
        } else {
            valueweekEnd = 1;
            horaires($("#booking_room").val(), valueweekEnd)
        }

        //valueDate = dateText;

        $(this).change();
        //  console.log(weekOfDay + ' ' + valueweekEnd );

    })
    };

    /* French initialisation for the jQuery UI date picker plugin. */

        $.datepicker.setDefaults($.datepicker.regional['fr']);
     $( "#reservation_date_reservation" ).datepicker();

});
