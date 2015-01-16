<script type="text/javascript">
$(function() {
    
    Highcharts.setOptions({
        colors: ['#660066', '#E60000', '#008A2E', '#0000CC']
    });
    
	var seriesOptions = [],
		yAxisOptions = [],
		seriesCounter = 0,
		names = ['CPS', 'Objects', 'FPS', 'Players','REMAI','LOCAI','ActiveProf','InactiveProf','UnitsProf','VehProf'],
		colors = Highcharts.getOptions().colors;

	$.each(names, function(i, name) {

		$.getJSON('{{ URL::to('/') }}/api/serverperf?id={{{$server->ip}}}&servername={{{$server->hostname}}}&type=' + name, function(data) {
            
            var seriesData = [];
            
            for (var r = 0; r < data.rows.length; r++) {
                if (data.rows[r].value.value > 0) {
                    if (data.rows[r].key != null) {
                        seriesData.push([data.rows[r].value.time, data.rows[r].value.value]);
                    }
                }
            }
			
			seriesData.sort( function(a,b) {
					return a[0]-b[0]
			});

			seriesOptions[i] = {
				name: name,
				data: seriesData
			};

			// As we're loading the data asynchronously, we don't know what order it will arrive. So
			// we keep a counter and create the chart when all the data is loaded.
			seriesCounter++;

			if (seriesCounter == names.length) {
				createChart();
			}
		});
	});



	// create the chart when all data is loaded
	function createChart() {

		$('#container').highcharts('StockChart', {
		    chart: {
                renderTo: 'container',
                    height: 480,
                    width: 900,
                    backgroundColor: null,
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
					events: {
						load: function(){
								this.showLoading();
						}
					}										
		    },
            exporting: {
                enabled: false
            },
            legend: {
                enabled: true,
                itemStyle: {
                    color: 'silver'
                }
            },
		    rangeSelector: {
		        selected: 4,
                inputEnabled: false,
                buttonTheme: { // styles for the buttons
                    fill: 'none',
                    stroke: 'none',
                    'stroke-width': 0,
                    r: 3,
                    style: {
                        color: 'silver',
                        fontWeight: 'bold'
                    },
                    states: {
                        hover: {
                            fill: '#CC9',
                            style: {
                                color: '#EEE'
                            }
                        },
                        select: {
                            fill: '#AA6',
                            style: {
                                color: 'white'
                            }
                        }
                    }
                },
                labelStyle: {
                    color: 'silver',
                    fontWeight: 'bold'
                }
		    },
            xAxis: {
                title: {
                    text: 'Date',
                    style: {
                        color: 'silver'
                    }
                }
            },
		    yAxis: {
                type: 'logarithmic',
                title: {
                    text: 'Count',
                    style: {
                        color: 'silver'
                    }
                },
		    	plotLines: [{
		    		value: 0,
		    		width: 2,
		    		color: 'silver'
		    	}]
		    },
		    
		    plotOptions: {
		    	series: {
		    	}
		    },
		    
		    tooltip: {
		    	pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}<br/>'
		    },
		    
		    series: seriesOptions
		});
	}

});
		
</script>

<div id="container"><div class="alert alert-info" style="text-align:center">Loading...</div></div>