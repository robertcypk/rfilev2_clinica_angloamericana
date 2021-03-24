$(document).ready(function(){

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
			$(".msg").html(xhr.responseText);
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
			$(".msg").html(xhr.responseText);
		}
	}); ;

	var distribuidor = $("#distribuidoredit").attr('data-distribuidor');
	$("#distribuidoredit optgroup").each(function(index, el) {
		$.each( $(this).children('option'), function(i,e){
			if( $(this).val() == distribuidor){
				$(this).prop('selected', true);
			}
		});
	});

});