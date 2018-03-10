//<?php echo date('Y.m.d H:i:s', $session->start_time); ?>
$('#path-select').change(function(){
    window.location.href = $(this).find('option:selected').attr('href');
});