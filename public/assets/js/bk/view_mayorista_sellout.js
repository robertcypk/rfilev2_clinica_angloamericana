 $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.menuActive(1,2);
		App.dataTables();
		
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
		
        })