$(document).ready(() => {
    let select = $('#booking_room');
    let horaire = $('#reservation_seance');
    let datepicker = $("#reservation_date_reservation");
    let valueWeekEnd = 0;
    if (jourSelect === "Vendredi") {
      valueWeekEnd = 1;
    }
    horaires(select.val(), valueWeekEnd);

    $('#booking_room, #reservation_date_reservation').on('change', function () {
        if (jourSelect == "Vendredi" || jourSelect == "Samedi" || jourSelect == "Dimanche") {
            valueWeekEnd = 1;
        } else {
            valueWeekEnd = 0;
        }
        horaires(select.val(), valueWeekEnd, horaire)
    });

    datepicker.datepicker({onSelect: horaires(select.val(), valueWeekEnd, horaire)});

});
