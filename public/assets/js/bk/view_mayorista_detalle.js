 $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.menuActive(1,2);
		    App.dataTables();
        // $('form').parsley();
/*------------------------
//certificaciones
------------------------*/
//chek si esta activado
		function checkInput(cont){
      if ($(cont).is(':checked')){
        console.log('press' + true)
            return true;
        } else {
          console.log('press' + false)
          return false;
        }
    }

/* activar certificaciones */
    function activeCert(){
      $(".txt-special").text("30%")
      $(".chk-special").val("30")
	  $('.chk-special-training').prop('checked', false);
	  $('.chk-special-status').prop('checked', true);
    }

/* desactivar certificaciones */
    function resetCert(){
      $(".txt-special").text("25%")
      $(".chk-special").val("25")
	  //$('.chk-special-training').prop('checked', false);
	  $('.chk-special-status').prop('checked', false);
    }

  /* click para activar certificaciones */
$('.chk-special-status').change(function() {
  if (checkInput($(this))){
    activeCert();
  }else {
    resetCert();
  }
})
  /* click para activar certificaciones */
$('.chk-special-training').change(function() {
  if (checkInput($(this))){
     resetCert();
  }
})
/* verificar si esta activado */
// if (checkInput($('.chk-special-status')) && $('.chk-special-status').length){

	$(".chk-special").each(function() {
	console.log ("Si estan con : " + $(this).val());
	if ($(this).val() == 30){
		 activeCert();
		};
});
	/*
	var certificado = $('.chk-special').val();
	console.log(" se encontro " + certificado)
	if (certificado == 30 || $('.chk-special:eq(1)') == 30 ){
		 activeCert();
		 console.log ("Si estan con 30");
  } else {
resetCert();
	}*/
/*------------------------
//generaci[o]n de demanda
------------------------*/
/* activar demanda */
    function activeDem(){
      $(".txt-special").text("70%")
      $(".chk-demanda-special").val("70")
	  $('.chk-demanda-sp').prop('checked', false);
	  $('.chk-demanda-status').prop('checked', true);
    }

/* desactivar demanda */
    function resetDem(){
      $(".txt-special").text("35%")
      $(".chk-demanda-special").val("35")
	  $('.chk-demanda-status').prop('checked', false);
    }


  /* click para activar certificaciones */
$('.chk-demanda-status').change(function() {
  if (checkInput($(this))){
    activeDem();
  }else {
    resetDem();
  }
})
$('.chk-demanda-sp').change(function() {
	if (checkInput($(this))){
    resetDem();
	}
})


/* verificar si esta activado */
var cantdemanda = $('.chk-demanda-special').val();
if ( cantdemanda  == 70){
	console.log("sim cpumplee")
		activeDem();
  }/*else {
resetDem();
}*/
/*------------------------
//reclutamiento
------------------------*/
function calReclutamiento(){

var range = $('.sel-range-special option:selected').val();
var stat = $('.val-special').val();
var stotal = (stat/range)*100 ;
// var ptotal =  (stat*100)/stotal;
  if ($.isNumeric(stotal)){
  $('.total-special').val(Math.floor(stotal));
   }else{
      $('.total-special').val(0);
   }
   // $("[data-mask='percent']").mask("99%");
}
// $("[data-mask='percent']").mask("99%");
$( ".sel-range-special" ).change(function () {
    calReclutamiento();
});
$(".val-special").on("change paste keyup keypress blur", function(event) {
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    calReclutamiento();
});
/*------------------------
//inventario
------------------------*/
function calWoi(){

var range = $('.woi-range option:selected').val();
var stat = $('.woi-dist').val();
var stotal = (stat/range)*100 ;
// var ptotal =  (stat*100)/stotal;
  if ($.isNumeric(stotal)){
  $('.woi-total').val(Math.floor(stotal));
   }else{
      $('.woi-total').val(0);
   }
}
$( ".woi-range" ).change(function () {
    calWoi();
});
$(".woi-dist").on("change paste keyup keypress blur", function(event) {
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    calWoi();
});
/////////////////////////////

		 // New Users
    function widget_users_chart(){
      var mob = $('#users-chart').attr( "mob" );
      var swc = $('#users-chart').attr( "swc" );


      var data = [
        //{ data: 45},
        //{ data: 65},
        { data: mob},
        { data: swc}
      ];
      console.log (mob + " ===== "+ swc)
      var color1 = tinycolor( App.color.primary ).brighten( 10 ).toString();
      var color2 = tinycolor( App.color.primary ).lighten( 40 ).toString();
      var color3 = tinycolor( App.color.info ).lighten( 20 ).toString();
      var color4 = tinycolor( App.color.warning ).brighten( 0 ).toString();
      var color5 = tinycolor( App.color.dark ).brighten( 20 ).toString();

      $.plot('#users-chart', data, {
        series: {
          pie: {
            show: true,
            innerRadius: 0.7,
            shadow:{
              top: 5,
              left: 15,
              alpha:0.3
            },
            stroke:{
              width:0
            },
            label: {
                show: false,
                formatter: function (label, series) {
                    return '<div style="font-size:12px;text-align:center;padding:2px;color:#333;">' + label + '</div>';
                }
            },
            highlight:{
              opacity: 0.08
            }
          }
        },
        grid: {
          hoverable: true,
          clickable: true
        },
        // colors: [color1, color2, color3, color4],
		 colors: [color1, color2],
        legend: {
          show: false
        }
      });
    }

    widget_users_chart();

        });

    function widget_users_chart2(){
          var mob = $('#users-chart-3').attr( "mob" );
          var swc = $('#users-chart-3').attr( "swc" );


          var data = [
          //{ data: 45},
          //{ data: 65},
          { data: mob},
          { data: swc}
          ];
          console.log (mob + " ===== "+ swc)
          var color1 = tinycolor( App.color.primary ).brighten( 10 ).toString();
          var color2 = tinycolor( App.color.primary ).lighten( 40 ).toString();
          var color3 = tinycolor( App.color.info ).lighten( 20 ).toString();
          var color4 = tinycolor( App.color.warning ).brighten( 0 ).toString();
          var color5 = tinycolor( App.color.dark ).brighten( 20 ).toString();

          $.plot('#users-chart-3', data, {
          series: {
            pie: {
              show: true,
              innerRadius: 0.7,
              shadow:{
                top: 5,
                left: 15,
                alpha:0.3
              },
              stroke:{
                width:0
              },
              label: {
                  show: false,
                  formatter: function (label, series) {
                      return '<div style="font-size:12px;text-align:center;padding:2px;color:#333;">' + label + '</div>';
                  }
              },
              highlight:{
                opacity: 0.08
              }
            }
          },
          grid: {
            hoverable: true,
            clickable: true
          },
          // colors: [color1, color2, color3, color4],
           colors: ['#45db9d', '#caf3e3'],
          legend: {
            show: false
          }
          });
          }
        widget_users_chart2();