$(document).ready(() => {
    select = $('#reservation_salle');
    horaire = $('#reservation_seance');
    datepicker = $("#reservation_date_reservation")
    if (jourSelect = "Vendredi") {
      valueweekEnd = 1
    }
    horaires($('#reservation_salle').val(), valueweekEnd);

    $('#reservation_salle, #reservation_date_reservation').on('change', function () {
      if (jourSelect == "Vendredi" || jourSelect == "Samedi" || jourSelect == "Dimanche") {
        valueweekEnd = 1;
      } else {
        valueweekEnd = 0;
      }
        horaires(select.val(), valueweekEnd)
    });

    datepicker.datepicker({onSelect: horaires(select.val(), valueweekEnd)});

});
