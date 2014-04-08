

<?php
 
$url = URL::to('/');
Log::info(dirname(__FILE__));

$user = $data['user'];
$playerdata = $data['playerdata'];
$clan = json_decode($data['clan']);
$avatar = $data['avatar'];
$clantar = $data['clantar'];

$playerDetails = json_decode($playerdata['Details']);
$playerTotals = json_decode($playerdata['Totals']);
$playerVehicle = json_decode($playerdata['Vehicle']);
$playerWeapon = json_decode($playerdata['Weapon']);
$playerClass = json_decode($playerdata['Class']);

$image = imagecreatefrompng(dirname(__FILE__).'/sig.png'); 

$white = imagecolorallocate($image, 225, 225, 225);

$font = dirname(__FILE__).'/redalert.ttf';

if (count ($playerWeapon) < 2) {
	$playerWeap = "Rifle";
} else {
	$playerWeap = $playerWeapon[2];
}

ImageTTFText ($image, 12, 0, 100, 16, $white, $font, $playerDetails->PlayerName); 
ImageTTFText ($image, 10, 0, 100, 32, $white, $font, $playerDetails->PlayerClass); 
ImageTTFText ($image, 10, 0, 100, 48, $white, $font, '['.$clan->tag.'] '.$clan->name); 
ImageTTFText ($image, 10, 0, 255, 17, $white, $font, $playerTotals->CombatHours.' mins');
ImageTTFText ($image, 10, 0, 255, 31, $white, $font, $playerTotals->Operations); 
ImageTTFText ($image, 10, 0, 255, 45, $white, $font, $playerTotals->Kills); 
ImageTTFText ($image, 10, 0, 267, 60, $white, $font, $playerDetails->Operation); 
ImageTTFText ($image, 8, 0, 355, 17, $white, $font, $playerDetails->date); 
ImageTTFText ($image, 10, 0, 367, 31, $white, $font, $playerWeap); 
ImageTTFText ($image, 10, 0, 385, 45, $white, $font, $playerTotals->ShotsFired);

$public = public_path();

$src = $public.$avatar;
switch (pathinfo($src, PATHINFO_EXTENSION)) {
    case 'gif' :
        $source = imagecreatefromgif($src);
        break;
    case 'jpg' :
        $source = imagecreatefromjpeg($src);
        break;
    default :
        $source = imagecreatefrompng($src);
}
imagecopymerge($image, $source, 2, 2, 0, 0, 92, 92, 80); 

$src = $public.$clantar;
switch (pathinfo($src, PATHINFO_EXTENSION)) {
    case 'gif' :
        $source = imagecreatefromgif($src);
        break;
    case 'jpg' :
        $source = imagecreatefromjpeg($src);
        break;
    default :
        $source = imagecreatefrompng($src);
}
imagecopymerge($image, $source, 467, 2, 0, 0, 92, 92, 80); 

imagepng($image);
imagedestroy($image); 
?>