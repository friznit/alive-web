<html>
<head>
<title>A3 ALiVE</title>
</head>
<body>
<h1>ALiVE</h1>
<p>
<?php

date_default_timezone_set('Europe/London');

$map = '';
if(isset($_GET['map'])){
	$map = $_GET['map'];
}

$mission = '';
if(isset($_GET['mission'])){
	$mission = $_GET['mission'];
}

$player = '';
if(isset($_GET['player'])){
	$player = $_GET['player'];
}

$date = date('d/m/y');
echo $date
?>
<br />
<br />
<?php
echo "Welcome ". $player. " to ALiVE Operations on ". $map. "<br />";
echo '<br>';
echo 'Latest ALiVE Version:<br> 0.9.0.1410251<br />';
echo 'Compatible with:<br> Arma 3 Stable 1.32.127785<br />';
echo '<br />-<a href="http://www.alivemod.com/#Download"> Download ALiVE here!</a>';
echo '<br />-<a href="http://www.alivemod.com/war-room"> Access War Room here!</a>';
echo '<br />-<a href="http://makearmanotwar.com/entry/0MI2rqQ5aQ#.VCmCgfmSwnI"> Help us WIN here!</a>';
echo '<br />-<a href="http:\\alivemod.com"> Get ALiVE updates here!</a>';
echo '<br />-<a href="http:\\alivemod.com/#Donate"> Donate to the cause here!</a>';
echo '<br>';
echo '<br />Coming soon, Player and Operation stats in game.';
echo '</p>';
  ?>

</body>
</html>