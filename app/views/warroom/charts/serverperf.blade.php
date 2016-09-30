<script type="text/javascript">
$(function() {
    
    Highcharts.setOptions({
        colors: ['#003366','#000000','#FF0000','#339933','#660066', '#660000', '#660066', '#FF66FF','#33CC33','#CC9900','#800000']
    });
    
	var seriesOptions = [],
		yAxisOptions = [],
		seriesCounter = 0,
		names = ['Players','FPS','FPSMIN','CPS','Objects','Agents','Entities','Triggers','AllDead','Vehicles','REMAI','LOCAI','ActiveProf','InactiveProf','UnitsProf','VehProf','Mem','CPU','IORead','Threads','DiskQueue','IOWrite'],
		colors = Highcharts.getOptions().colors;

	$.each(names, function(i, name) {

		$.getJSON('http://alivemod.com/api/serverperf?id={{{$server->ip}}}&servername={{{$server->hostname}}}&type=' + name, function(data) {
            
            var seriesData = [];
            
			if (data.rows) {
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
			}
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
                    plotShadow: false								
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
		        selected: 0,
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
				buttons: [
				{
					type: 'minute',
					count: 240,
					text: '4h'
				},				{
					type: 'day',
					count: 1,
					text: '1d'
				},				{
					type: 'week',
					count: 1,
					text: '1w'
				},				{
					type: 'month',
					count: 1,
					text: '1m'
				}, {
					type: 'month',
					count: 3,
					text: '3m'
				}, {
					type: 'month',
					count: 6,
					text: '6m'
				}, {
					type: 'ytd',
					text: 'YTD'
				}, {
					type: 'year',
					count: 1,
					text: '1y'
				}, {
					type: 'all',
					text: 'All'
				}],				
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

<div class="container">
        <div class="row">
 		    <div class="col-md-9">
                <div class="panel panel-light">
                    <div class="panel-heading">
                        <h3 class="panel-title">About ALiVE Performance Monitoring</h3>
                    </div>

                    {{ Form::token() }}

                    <div class="panel-body">

                        <div class="strip">
                            <p><b>NOTE: Click on the chart keys to turn individual graphs on or off. Roll over graphs with mouse to see specific data points</b></p>
                        </div>

                        <table class="table">
                             <tr>
                                <td width="80">Players</td>
                                <td>Number of players connected to the server</td>
                            </tr>
                            <tr>
                                <td>FPS</td>
                                <td>Frames per second on the server as recorded by diag_FPS. Indicates general load on server.</td>
                            </tr>
                            <tr>
                                <td>FPSMIN</td>
                                <td>Lowest recorded Frames per second on the server as recorded by diag_FPSMIN over the last 16 frames</td>
                            </tr>
                            <tr>
                                <td>CPS</td>
                                <td>Cycles per second (FSM) on average during the default interval (3 minutes). Low values are likely to affect AI.</td>
                            </tr>
                            <tr>
                                <td>Objects</td>
                                <td>Total number of objects in game.</td>
                            </tr>
                            <tr>
                                <td>Agents</td>
                                <td>Total number of AI in game.</td>
                            </tr>
                            <tr>
                                <td>Vehicles</td>
                                <td>Total number of vehicles in game.</td>
                            </tr>                                                                                    
                            <tr>
                                <td>Entities</td>
                                <td>Total number of dead or alive entities in game.</td>
                            </tr> 
                            <tr>
                                <td>AllDead</td>
                                <td>Total number of dead things.</td>
                            </tr>
                             <tr>
                                <td>Triggers</td>
                                <td>Total number of triggers in game.</td>
                            </tr>                           
                             <tr>
                                <td>REMAI</td>
                                <td>Total number of remote AI, for example that are local to a client and not the server.</td>
                            </tr>   
                              <tr>
                                <td>LOCAI</td>
                                <td>Total number of AI that are local to the server.</td>
                            </tr>  
                              <tr>
                                <td>ActiveProf</td>
                                <td>Total number of profiles that are active and spawned.</td>
                            </tr>
                              <tr>
                                <td>InactiveProf</td>
                                <td>Total number of profiles that are cached/virtualized</td>
                            </tr>
                               <tr>
                                <td>UnitsProf</td>
                                <td>Total number of units profiled</td>
                            </tr>          
                              <tr>
                                <td>VehProf</td>
                                <td>Total number of vehicles that are profiled</td>
                            </tr>    
                            <tr>
                                <td>Mem</td>
                                <td>Amount of memory in MB the current arma3server.exe process is using</td>
                            </tr> 
                            <tr>
                                <td>CPU</td>
                                <td>Percentage of CPU use for the arma3server.exe process (taking into account total cores)</td>
                            </tr>                             
                            <tr>
                                <td>Threads</td>
                                <td>Number of threads currently associated with the arma3server.exe process</td>
                            </tr>                              
                            <tr>
                                <td>IORead</td>
                                <td>Amount of read IO in MB since the last sample (network and disk)</td>
                            </tr>                            
                            <tr>
                                <td>IOWrite</td>
                                <td>Amount of write IO in MB since the last sample (network and disk)</td>
                            </tr>                            
                            <tr>
                                <td>DiskQueue</td>
                                <td>Equivalent to Avg. Disk Queue Length which is equal to the (Disk Transfers per second)*( Disk seconds to Transfer)</td>
                            </tr>                                                                                                                                                                                                      
                        </table>
                    </div>

                </div>
            </div>
      </div>
</div>            