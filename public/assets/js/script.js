/* ===== Slider Main ===== */
function mainSlider(){
    var owl = $('#slider-main');
    owl.owlCarousel({
        loop: true,
        nav: false,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:1,
            },
            1000:{
                items:1,
            }
        }
    });

    $('.next').click(function() {
        owl.trigger('next.owl.carousel');
    })

    $('.prev').click(function() {
        owl.trigger('prev.owl.carousel', [300]);
    });
}

/* ===== Slider project ===== */
function projectSlider(){
    var owl = $('.slider-project');
    owl.owlCarousel({

        nav: false,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:1,
            },
            1000:{
                items:1,
            }
        }
    });

}

function pop(){
    const pop = $('.pop-edit');
    const shadow = $('.shadow');
    const button = $('.edit');
    const cancel = $('.cancel')
    $(button).on('click', function(){
        $(pop).fadeIn(300);
        $(shadow).fadeIn(300);
    });
    $(shadow, cancel).on('click', function(){
        $(shadow).fadeOut(300);
        $(pop).fadeOut(300);
    })
}
function register(){

   if( $('form.form-register').length !== 0 ) {

		   //validate
		   $('.form-register').validate({
		   highlight: function(element, errorClass, validClass) {
				$(".alert").hide();
				//console.log('Error');
			  },
			  unhighlight: function(element, errorClass, validClass) {
			   //$(".alert").show();
			   //console.log('Ok');
			  },

            rules: {

                email:{
                    required: true,
                    email: true
                    },
                password: 'required',
				confirmpassword: 'required'
                // confirmpassword: {
                    // required: true,
                    // equalTo: '#password'
                // }
            },
            messages:{
                // username: "",
                email: "",
                password: "",
                confirmpassword: "",
            }
        });
	// end-validate

       $('form.form-register').ajaxForm({
        beforeSend: function() {
			$(".alert").hide();
        },
        uploadProgress: function(event, position, total, percentComplete) {
        },
        success: function() {
        },
        complete: function(xhr) {
           var rp = JSON.parse(xhr.responseText);
           $(".alert").show();

           if( rp.success === 0){
		   // $("form.form-register")[0].reset();
            $("form.form-register span.msg").html( rp.msg + '!');
           }else if( rp.success === 2 ){
             $("form.form-register span.msg").html( rp.msg + '!');
           }else if( rp.success === 3){
		     $("form.form-register")[0].reset();
              $("form.form-register span.msg").html(rp.msg);
			 }else if( rp.success === 4){
		     $("form.form-register")[0].reset();
              $("form.form-register span.msg").html( rp.msg + ' <a href="mailto:alejandro.rozo@hpe.com">Alejandro Rozo</a>');

           }else{
		     $("form.form-register")[0].reset();
            $("form.form-register span.msg").html(rp.msg);
				setTimeout(function() {
					   window.location.href = "../"
					  }, 1300);
           }
        }
        });
    }
}

$(document).ready(function(){
    //mainSlider();
    //projectSlider();
    //pop();
    register();
})