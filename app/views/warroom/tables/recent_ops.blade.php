<script>
  $(document).ready(function() {
	  updateOps()
  });
	
  function updateOps(){
		
	$('#recent_ops').empty();

	$.getJSON('{{ URL::to('/') }}/api/recentoperations', function(opsdata) {

            $.each(opsdata.rows, function (index, row) {
				
                $('#recent_ops')
				.append('<hr>' + (row.value[1].charAt(0).toUpperCase() + row.value[1].slice(1) )+ '<br><a href={{ URL::to("war-room") }}/showoperation?name=' + encodeURIComponent(row.value[2]) + '&map=' + encodeURIComponent(row.value[1]) + '&clan=' + encodeURIComponent(row.value[3]) +'><span class="highlight">Operation ' + row.value[2] + '</a> (')
                  .append(row.value[3] + ')<br>' + parseArmaDate(row.key))

             });

            $("#recent_ops_container").mCustomScrollbar("update");
     });
	 setTimeout(updateOps,240000);
 }
</script>

<div id="recent_ops_container">
	<div id="recent_ops">
    </div>
</div>
