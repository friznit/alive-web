

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

$choice = strval(rand(1,4));
$image = imagecreatefrompng(dirname(__FILE__).'/'.$choice.'.png'); 
$logo = imagecreatefrompng(dirname(__FILE__).'/logo.png');
imagealphablending($image, true);
imagesavealpha($image, true);
imagecopy($image, $logo, 0, 0, 0, 0, 620, 104); 

$color = imagecolorallocate($image, 252, 175, 23);
$white = imagecolorallocate($image, 255, 255, 255);

$font = dirname(__FILE__).'/font_0.otf';

if (count ($playerWeapon) < 2) {
	$playerWeap = "Rifle";
} else {
	$playerWeap = $playerWeapon[2];
}

ImageTTFText ($image, 28, 0, 170, 28, $color, $font, $playerDetails->PlayerName); 
ImageTTFText ($image, 10, 0, 170, 40, $white, $font, $playerDetails->PlayerClass); 
ImageTTFText ($image, 10, 0, 170, 52, $white, $font, '['.$clan->tag.'] '.$clan->name); 
ImageTTFText ($image, 10, 0, 170, 64, $color, $font, 'OPS: '.$playerTotals->Operations); 
ImageTTFText ($image, 10, 0, 228, 64, $color, $font, 'EXP: '.$playerTotals->CombatHours.' mins');
ImageTTFText ($image, 10, 0, 170, 76, $color, $font, 'KILLS: '.$playerTotals->Kills); 
ImageTTFText ($image, 10, 0, 228, 76, $color, $font, 'AMMO: '.$playerTotals->ShotsFired);
ImageTTFText ($image, 10, 0, 170, 88, $color, $font, 'LAST ACTIVE: '.$playerDetails->date); 
ImageTTFText ($image, 10, 0, 170, 100, $color, $font, 'LAST OP: '.$playerDetails->Operation); 

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

$width = imagesx($source);
$height = imagesy($source);

if ($width > 150) {
	imagescale ($source, 150);
	$nwidth = 150;
} else {
	$nwidth = $width;
}

$xmid = (150 - $width) / 2;
	
if ($height > 100) {
	imagescale ($source, 100);
	$nheight = 100;
} else {
	$nheight = $height;
}

$ymid = 2 + (100 - $height) /2;
	
imagecopy($image, $source, $xmid, $ymid, 0, 0, $nwidth, $nheight); 

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
$width = imagesx($source);
$height = imagesy($source);

if ($width > 150) {
	imagescale ($source, 150);
	$nwidth = 150;
} else {
	$nwidth = $width;
}

$xmid = 380+(150 - $width) / 2;
	
if ($height > 100) {
	imagescale ($source, 100);
	$nheight = 100;
} else {
	$nheight = $height;
}

$ymid = 2 + (100 - $height) /2;

imagecopy($image, $source, $xmid, $ymid, 0, 0, $nwidth, $nheight);  

imagepng($image);
imagedestroy($image); 
?>