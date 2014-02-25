$(document).ready(function(){

    setInterval(ajaxPatient, (1000));

    function ajaxPatient() {
        $.ajax({
            url: patientUpdate,
            type: 'POST',
            success: function(data)
            {
                var ulPatient = $('.patient-js');
                var patientArray = $.parseJSON(data);

                for (var i = 0; i < ulPatient.length; i++ )
                {
                    ulPatient.eq(i).html(patientArray[i]);
                }
            }
        });
    }
});