$(document).ready(function(){
				//initialize the javascript
				App.init();
				App.menuActive(1,2);
				// $("#spark1").sparkline([0,5,3,7,5,10,3,6,5,10], {
				// 	type: 'line',
				// 	width: '85px',
				// 	height: '35px',
				// 	lineColor: '#2bc185',
				// 	fillColor: false,
				// 	lineWidth: 1.5,
				// 	spotColor: false,
				// 	minSpotColor: false,
				// 	maxSpotColor: false,
				// 	highlightSpotColor: '#2bc185',
				// 	highlightLineColor: '#2bc185'
				// });
				// $("#spark2").sparkline([5,8,7,10,9,10,8,6,4,6,8,7,6,8], {
				// 	type: 'bar',
				// 	width: '85',
				// 	height: '35',
				// 	barWidth: 3,
				// 	barSpacing: 3,
				// 	chartRangeMin: 0,
				// 	barColor: '#FFDC42'
				// });
				// $("#spark3").sparkline([2,3,4,5,4,3,2,3,4,5,6,5,4,3,4,5,6,5,4,4,5], {
				// 	type: 'discrete',
				// 	width: '85',
				// 	height: '35',
				// 	lineHeight: 20,
				// 	lineColor: '#2bc185',
				// 	xwidth: 18
				// });
				
				// $("#spark4").sparkline([0,5,3,7,5,10,3,6,5,10], {
				// 	type: 'line',
				// 	width: '85px',
				// 	height: '35px',
				// 	lineColor: '#F45846',
				// 	fillColor: false,
				// 	lineWidth: 1.5,
				// 	spotColor: false,
				// 	minSpotColor: false,
				// 	maxSpotColor: false,
				// 	highlightSpotColor: '#F45846',
				// 	highlightLineColor: '#F45846'
				// });
			});
			
			function widget_barchart2(){
				
				var color1 = '#2cc185';
				
				var plot_statistics = $.plot($("#bar-chart2"), [
				{
					data: [
					[1, 60], [2, 25], [3, 50], [4, 58], [5, 58], [6, 83]
					],
					label: "Page Views"
				}
				], {
					series: {
						bars: {
							order: 2,
							align: 'center',
							show: true,
							lineWidth: 1,
							barWidth: 0.60,
							fill: true,
							fillColor: {
								colors: [{
									opacity: 1
									}, {
									opacity: 1
								}
								]
							}
						},
						shadowSize: 2
					},
					legend:{
						show: false
					},
					grid: {
						margin: {
							left: 23,
							right: 30,
							top: 20,
							botttom: 40
						},
						labelMargin: 10,
						axisMargin: 200,
						hoverable: true,
						clickable: true,
						tickColor: "rgba(0,0,0,0.05)",
						borderWidth: 1,
						borderColor: "rgba(0,0,0,0.0)"
					},
					colors: [color1],
					xaxis: {
						ticks: 11,
						tickSize: 1,
						tickDecimals: 0,
						tickColor: "rgba(0,0,0,0.0)"
					},
					yaxis: {
						ticks: 4,
						tickDecimals: 0
					}
				});
			}
			widget_barchart2()
			
			// function widget_linechart1(){
				
			// 	var color1 = '#b5b5b5';
				
			// 	var plot_statistics = $.plot($("#line-chart1"), [{
			// 		data: [
			// 		[0, 20], [1, 30], [2, 25], [3, 39], [4, 35], [5, 40], [6, 30], [7, 45]
			// 		],
			// 		label: "Page Views"
			// 	}
			// 	], {
			// 		series: {
			// 			lines: {
			// 				show: true,
			// 				lineWidth: 0,
			// 				fill: true,
			// 				fillColor: {
			// 					colors: [{
			// 						opacity: 0.35
			// 						}, {
			// 						opacity: 0.35
			// 					}
			// 					]
			// 				}
			// 			},
			// 			points: {
			// 				show: false
			// 			},
			// 			shadowSize: 0
			// 		},
			// 		legend:{
			// 			show: false
			// 		},
			// 		grid: {
			// 			margin: {
			// 				left: -8,
			// 				right: -8,
			// 				top: 0,
			// 				bottom: 0
			// 			},
			// 			show: false,
			// 			labelMargin: 15,
			// 			axisMargin: 500,
			// 			hoverable: true,
			// 			clickable: true,
			// 			tickColor: "rgba(0,0,0,0.15)",
			// 			borderWidth: 0
			// 		},
			// 		colors: [color1],
			// 		xaxis: {
			// 			ticks: 11,
			// 			tickDecimals: 0
			// 		},
			// 		yaxis: {
			// 			autoscaleMargin: 0.5,
			// 			ticks: 4,
			// 			tickDecimals: 0
			// 		}
			// 	});
			// }
			// widget_linechart1()
			
			// function widget_chartpie4(){
			// 	var data = [
			// 	{ label: "Perú", data: 45},
			// 	{ label: "Panamá", data: 25},
			// 	{ label: "México", data: 20},
			// 	{ label: "Chile", data: 10}
			// 	];
				
			// 	var color1 = '#bfd72f'
			// 	var color2 = '#8cc63e';
			// 	var color3 = '#3fb549';
			// 	var color4 = '#018949';
				
			// 	$.plot('#pie-chart4', data, {
			// 		series: {
			// 			pie: {
			// 				show: true,
			// 				innerRadius: 0.30,
			// 				shadow:{
			// 					top: 5,
			// 					left: 15,
			// 					alpha:0.5
			// 				},
			// 				stroke:{
			// 					width:0
			// 				},
			// 				label: {
			// 					show: true,
			// 					formatter: function (label, series) {
			// 						return '<div style="font-size:12px;text-align:center;padding:2px;color:#fff711">' + label + '</div>';
			// 					}
			// 				},
			// 				highlight:{
			// 					opacity: 0.5
			// 				}
			// 			}
			// 		},
			// 		grid: {
			// 			hoverable: true,
			// 			clickable: true
			// 		},
			// 		colors: [color1, color2, color3, color4],
			// 		legend: {
			// 			show: false
			// 		}
			// 	});
			// }
			
			// widget_chartpie4()