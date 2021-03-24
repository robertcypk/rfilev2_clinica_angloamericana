$(document).ready(function(){
    App.init();
    App.menuActive(1,3);


    var host = window.location.origin;

	//modal -users
	$(".utrash").click(function(event) {
		var id  = $(this).attr('data-idusr');
		$("#idusrchampionsdelete").val( id );
	});

	//newchampions_save
	$(".newchampions_save").ajaxForm({
	    beforeSend: function() {
	    },
	    uploadProgress: function(event, position, total, percentComplete) {
	    },
	    success: function() {
	    },
		complete: function(xhr) {
			var m = JSON.parse( xhr.responseText );
			$(".msg").html( m.msg );
			setTimeout(function(){
				window.location.href = host + '/dashboard/champions';
			},900);
		}
	}); ;

	//editchampions_save
	$(".editchampions_save").ajaxForm({
	    beforeSend: function() {
	    },
	    uploadProgress: function(event, position, total, percentComplete) {
	    },
	    success: function() {
	    },
		complete: function(xhr) {
			var v = JSON.parse( xhr.responseText );
			$(".msg").html( v.msg );
			setTimeout(function(){
				window.location.href = host + '/dashboard/champions';
			},900);
		}
	}); ;



	/******************************/
	
	$("#country").change(function(){
		var codigopais = $(this).val();
		var html = '<option value="">Seleccion un mayorista</option>';
		
		$.post(host+'/dashboard/sellers',{codigopais:codigopais},function(data){

			$.each(data,function(i,o){
				
				html += '<option value="'+ o.id +'">'+ o.nombre +'</option>';
			});

			$("#distribuidor").html( html );

		},'json');

	});

	var distribuidor = $("#distribuidor").attr('data-distribuidor');
	if ( $.trim( distribuidor ) !== '' ) {
		setTimeout(function(){

			//carga automatica de los sellers segun el pais registrado para el usuario
			var codigopais = $("#country option:selected").val();
			var html = '<option value="">Seleccion un mayorista</option>';
			$.post(host+'/dashboard/sellers',{codigopais:codigopais},function(data){
				$.each(data,function(i,o){
					html += '<option value="'+ o.id +'">'+ o.nombre +'</option>';
				});
				$("#distribuidor").html( html );

				//seleccina automaticamente el pais correspondiente al usuario
				$.each( $("#distribuidor option"), function(i,e){
					//console.log( $(this).val() );
					if( $(this).val() === distribuidor){
						$(this).prop('selected', true);
					}
				});

			},'json');

		},900);
	}


	/****************************/
});