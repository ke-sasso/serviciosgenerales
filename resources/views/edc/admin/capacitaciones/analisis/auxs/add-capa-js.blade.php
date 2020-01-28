<script>
$(document).ready(function() {
	$("#collapse-filter").collapse("show");

	$("#unidad").chosen({width: "inherit"});
	$("#plazaFuncional").chosen({width: "inherit"});

	$('#selectAll').change(function(){
		var cells = $('#dt-capacitaciones-items').DataTable().cells( ).nodes();
		$(cells).find(':checkbox').prop('checked', $(this).is(':checked'));
	});


  	$('#form').submit(function(event) {
        $.ajax({
            data:  $('#form').serialize(),
            url:   "{{ route('rh.capacitaciones.items.guardar') }}",
            type:  'post',
            beforeSend: function() {
              //$('body').modalmanager('loading');
            },
            success:  function (r){
                //$('body').modalmanager('loading');
                if(r.status == 200){
                    alertify.alert("Mensaje de sistema",r.message, function(){
                        window.location.href = r.redirect;
                    });
                }else if (r.status == 400){
                    alertify.alert("Mensaje de sistema - Error",r.message);
                }else if(r.status == 401){
                    alertify.alert("Mensaje de sistema",r.message, function(){
                        window.location.href = r.redirect;
                    });
                }else{//Unknown
                    alertify.alert("Mensaje de sistema - Error", "Oops!. Algo ha salido mal, contactar con el adminsitrador del sistema para poder continuar!");
                    console.log(r);
                }
            },
            error: function(data){
                // Error...
                var errors = $.parseJSON(data.responseText);
                console.log(errors);
                $.each(errors, function(index, value) {
                    $.gritter.add({
                        title: 'Error',
                        text: value
                    });
                });
            }
        });
        return false;
    });
});
</script>