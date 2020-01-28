@extends('master')

@section('css')

@endsection

@section('contenido')
<div class="panel panel-success">
	<div class="panel-heading">		
	</div>
	<div class="panel-body">
		<div class="col-md-2 col-lg-2">
			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<form id="frmPasswd">
				<div class="input-group form-group">
					<div class="input-group-addon">Contraseña Anterior&nbsp;&nbsp;&nbsp;</div>
					<input type="password" class="form-control" id="pwdold" name="pwdold">										
					<div id="checkpwdold" class="input-group-addon"></i></div>				
				</div>				
				<div class="input-group form-group">
					<div class="input-group-addon">Contraseña Nueva&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
					<input type="password" class="form-control" id="pwdnew1" readonly="readonly" name="pwdnew1">
					<div id="checkpwdnew1" class="input-group-addon"></div>
				</div>
				<div class="input-group form-group">
					<div class="input-group-addon">Confirmar Contraseña</div>
					<input type="password" class="form-control" id="pwdnew2" readonly="readonly" name="pwdnew2">
					<div id="checkpwdnew2" class="input-group-addon"></div>
				</div>
				<div id="tooltip" class="panel-footer text-center">
					
				</div>
				<div class="text-center">
					<button type="button" id="btnsend" class="btn btn-md btn-success">Cambiar Contraseña</button>
				</div>	
				<input type="hidden" name="_token" value="{{csrf_token()}}">							
			</form>
		</div>
		<div class="col-lg-2">
			
		</div>
	</div>
</div>
@endsection
@section('js')
<script type="text/javascript">
	
	$('#checkpwdold').hide();
	$('#checkpwdnew1').hide();
	$('#checkpwdnew2').hide();
	$('#btnsend').hide();

	$(document).ready(function() {
		
		function validatePwdStrength(string) { 
		    return /[A-Z]+/.test(string) && /[a-z]+/.test(string) &&
		    /[\d\W]/.test(string) && /\S{7,}/.test(string)
		}

		$('#pwdold').on('blur', function(event) {
			$(this).parent().removeClass('has-error');
			event.preventDefault();
			$('#checkpwdold').html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
			$('#checkpwdold').show();
			setTimeout(function(){
				$.post('{{route('validate.passwd')}}', {pwdold: $('#pwdold').val(),_token:'{{csrf_token()}}'}, function(data, textStatus, xhr) {
					if(data.status == 200)
					{

						$('#pwdold').parent().addClass('has-success');
						$('#checkpwdold').html('<i class="fa fa-check"></i>');
						$('#pwdnew1').removeAttr('readonly');						
						$('#tooltip').html('');
					}
					else
					{
						$('#tooltip').html('<p>'+data.message+'</p>');
						$('#pwdold').parent().addClass('has-error');
						$('#checkpwdold').html('<i class="fa fa-times"></i>');	
						$('#pwdnew1').attr('readonly', 'readonly');
						$('#pwdnew2').attr('readonly', 'readonly');
						$('#btnsend').hide();
					}
				});
			},2000);
					
		});

		$('#pwdnew1').keyup(function(event) {
			$(this).parent().removeClass('has-error');
			var test = validatePwdStrength($(this).val());

			var old = $('#pwdold').val();
			var nova = $('#pwdnew1').val();			

			if(!test)
			{
				$('#tooltip').html('<p class="text-left">La nueva contraseña debe complir las siguientes condiciones: </p><ul><li>Debe contener por lo menos 8 caracteres</li><li>Una Letra Mayúscula</li><li>Una Letra Minúscula</li><li>Un valor numérico (0-9)</li><li>Debe ser diferente a la contraseña anterior</li></ul>');
				$('#pwdnew1').parent().addClass('has-error');
				$('#checkpwdnew1').html('<i class="fa fa-times"></i>');
				$('#pwdnew2').attr('readonly', 'readonly');
				$('#btnsend').hide();
				return;
			}
			if(old == nova)
			{
				$('#tooltip').html('<p class="text-left">La nueva contraseña debe complir las siguientes condiciones: </p><ul><li>Debe ser diferente a la contraseña anterior</li></ul>');
				$('#pwdnew1').parent().addClass('has-error');
				$('#checkpwdnew1').html('<i class="fa fa-times"></i>');
				$('#pwdnew2').attr('readonly', 'readonly');	
				$('#btnsend').hide();
				return;
			}
			if(test && (old != nova))
			{
				$('checkpwdnew1').show();
				$('#pwdnew1').parent().addClass('has-success');
				$('#checkpwdnew1').html('<i class="fa fa-check"></i>');	
				$('#pwdnew2').removeAttr('readonly');
				$('#tooltip').html('');
				$('#btnsend').hide();
			}
		});

		$('#pwdnew2').keyup(function(event) {
			$(this).parent().removeClass('has-error');
			var nova = $('#pwdnew1').val();
			var conf = $(this).val();

			if(nova != conf)
			{
				$('#tooltip').html('<p class="text-left">La nueva contraseña debe complir las siguientes condiciones: </p><ul><li>La confirmación de la contraseña no coincide</li></ul>');
				$('#pwdnew2').parent().addClass('has-error');
				$('#checkpwdnew2').html('<i class="fa fa-times"></i>');				
				$('#btnsend').hide();
				return;	
			}
			else
			{
				$('checkpwdnew2').show();
				$('#pwdnew2').parent().addClass('has-success');
				$('#checkpwdnew2').html('<i class="fa fa-check"></i>');					
				$('#tooltip').html('');	

				$('#btnsend').show('slow');
			}
		});

		$('#btnsend').on('click',function(event) {
			event.preventDefault();
			$('#btnsend').hide();
			$.post('{{route('cambiar.passwd')}}', $('#frmPasswd').serialize(), function(data, textStatus, xhr) {
				if(data.status == 200)
				{

					alertify.alert(data.message,function(){
						window.location.href = "{{route('doInicio')}}";	
					}).set('title','Confirmación de Contraseña');
					
				}
				else
				{
					alertify.alert(data.message).set('title','Confirmación de Contraseña');
				}
			});
		});
	});
</script>
@endsection