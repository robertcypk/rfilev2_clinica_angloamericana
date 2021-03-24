$(document).ready(function(){
				//initialize the javascript
				App.init();
				App.menuActive(1,1);
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

			function widget_barchart1(){

				var color1 = '#4a4a4d';

				var plot_statistics = $.plot($("#bar-chart2"), [
				{
					data: [
					[1, 30], [2, 80], [3, 30], [4, 18], [5, 53], [6, 10]
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
			widget_barchart1()

			function widget_barchart2(){
				console.log('bar-chart2');

				var color1 = '#e8c20f'; // Color de barras
				var data = [ ["Enero", 10], ["Febrero", 8], ["Marzo", 4], ["Abril", 13], ["Mayo", 17], ["Junio", 9] ];

				$.plot("#bar-chart1", [ data ], {
					series: {
						/*bars: {
							show: true,
							barWidth: 0.6,
							align: "center"
						}*/
						bars: {
							order: 1,
							align: 'center',
							show: true,
							lineWidth: 1,
							barWidth: 0.6,
							fill: true,
							fillColor: {
								colors: [{
									opacity: 0.3
									}, {
									opacity: 1
								}
								]
							}
						},
						shadowSize: 1
					},
					grid: {
						margin: {
							left: 23,
							right: 30,
							top: 20,
							botttom: 40
						},
						/*padding: {
							left: 23,
							right: 30,
							top: 20,
							botttom: 40
						},*/
						labelMargin: 10,
						axisMargin: 200,
						hoverable: true,
						clickable: true,
						tickColor: "rgba(255,255,255,0.05)",
						borderWidth: 1,
						borderColor: "rgba(255,255,255,0.1)"
					},
					colors: [color1],
					xaxis: {
						mode: "categories",
						tickLength: 0,
						color: "black",
						tickColor: "rgba(0,0,0,0.0)"
					}
					//valueLabels: { show: true, font : "17px Arial", yoffset : 0, xoffset : 14, fontcolor : "#FF0000" }
				});


				/*var plot_statistics = $.plot($("#bar-chart1"), [
				{
					data: [{
					  	//[1, 30], [2, 80], [3, 30], [4, 18], [5, 53], [6, 10]
					  	x: ['giraffes', 'orangutans', 'monkeys'],
						y: [20, 14, 23],
						type:'bar'
					}],
					//label: "Paises"
					labels: ["January", "February", "March", "April", "May", "June"]
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
						show: true
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
						tickColor: "rgba(255,255,255,0.10)",
						borderWidth: 1,
						borderColor: "rgba(255,225,55,0.5"
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
				});*/
			}
			widget_barchart2()
			/*
			function widget_linechart1(){

				var color1 = '#b5b5b5';

				var plot_statistics = $.plot($("#line-chart1"), [{
					data: [
					[0, 20], [1, 30], [2, 25], [3, 39], [4, 35], [5, 40], [6, 30], [7, 45]
					],
					label: "Page Views"
				}
				], {
					series: {
						lines: {
							show: true,
							lineWidth: 0,
							fill: true,
							fillColor: {
								colors: [{
									opacity: 0.35
									}, {
									opacity: 0.35
								}
								]
							}
						},
						points: {
							show: false
						},
						shadowSize: 0
					},
					legend:{
						show: false
					},
					grid: {
						margin: {
							left: -8,
							right: -8,
							top: 0,
							bottom: 0
						},
						show: false,
						labelMargin: 15,
						axisMargin: 500,
						hoverable: true,
						clickable: true,
						tickColor: "rgba(0,0,0,0.15)",
						borderWidth: 0
					},
					colors: [color1],
					xaxis: {
						ticks: 11,
						tickDecimals: 0
					},
					yaxis: {
						autoscaleMargin: 0.5,
						ticks: 4,
						tickDecimals: 0
					}
				});
			}
			widget_linechart1();
			*/
			function widget_chartpie4(){
				var data = [
				{ label: "Perú", data: 50},
				//{ label: "Panamá", data: 25},
				{ label: "México", data: 30},
				{ label: "Chile", data: 20}
				];

				var color1 = '#fff'
				var color2 = '#d5d6d8';
				var color3 = '#e7e7e7';
				var color4 = '#f3f3f3';

				$.plot('#pie-chart4', data, {
					series: {
						pie: {
							show: true,
							innerRadius: 0.30,
							shadow:{
								top: 5,
								left: 15,
								alpha:0.3
							},
							stroke:{
								width:0
							},
							label: {
								show: true,
								formatter: function (label, series) {
									return '<div style="font-size:12px;text-align:center;padding:2px;color:#909090">' + label + '</div>';
								}
							},
							highlight:{
								opacity: 0.5
							}
						}
					},
					grid: {
						hoverable: true,
						clickable: true
					},
					colors: [color1, color2, color3, color4],
					legend: {
						show: false
					}
				});
			}

			widget_chartpie4()