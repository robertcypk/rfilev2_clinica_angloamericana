$(document).ready(function(){
	//initialize the javascript
	App.init();
	App.menuActive(1,1);
	// RENDIMIENTO
    function widget_users_chart(){
		
		var mob = $('#performance-chart').attr( "mob" );
		var swc = $('#performance-chart').attr( "swc" );
		
		var data = [
			//{ data: 45},
				//{ data: 25},
					{ data: mob},
					{ data: swc}
				];
				
				var color1 = tinycolor( App.color.primary ).brighten( 10 ).toString();
				var color2 = tinycolor( App.color.primary ).lighten( 40 ).toString();
				var color3 = tinycolor( App.color.primary ).lighten( 20 ).toString();
				var color4 = tinycolor( App.color.primary ).lighten( 27 ).toString();
				
				$.plot('#performance-chart', data, {
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
					//colors: [color1, color2, color3, color4],
					colors: [color1, color2],
					legend: {
						show: false
					}
				});
			}
			widget_users_chart();
			
			//EVALUACIÓN
			function widget_evaluation_chart(){
				console.log(" chart de evaluación")
				var mob = $('#metrica-chart').attr( "mob" );
				var swc = $('#metrica-chart').attr( "swc" );
				
				var data = [
					//{ data: 45},
						//{ data: 25},
							{ data: mob},
							{ data: (100 - swc) }
						];
						
						var color1 = tinycolor( App.color.warning ).brighten( 5 ).toString();
						var color2 = tinycolor( App.color.warning ).brighten( 50 ).toString();
						var color3 = tinycolor( App.color.warning ).lighten( 20 ).toString();
						var color4 = tinycolor( App.color.warning ).lighten( 27 ).toString();
						
						$.plot('#metrica-chart', data, {
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
							//colors: [color1, color2, color3, color4],
							colors: [color1, color2],
							legend: {
								show: false
							}
						});
					}
					widget_evaluation_chart();
					
				});
				
						