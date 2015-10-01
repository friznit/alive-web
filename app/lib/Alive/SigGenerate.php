<?php

namespace Alive;

use Tempo\TempoDebug;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Color;
use Imagine\Gd\Font;
use Gravatar;
use Config;

class SigGenerate
{

    protected $imagine;

    /**
     * Create a new signature image
     *
     * @param array $data Data to use with signature generation
     */
    public function create($data)
    {
        $this->imagine = new Imagine();

        $username = $data['username'];
        $useremail = $data['email'];
        $playerdata = $data['playerdata'];
        if ($data['clan']) {
            $clan = json_decode($data['clan']);
        }
        $avatar = $data['avatar'];
        $clantar = $data['clantar'];
        $a3_id = $data['a3_id'];
        $country = $data['country'];

        $playerDetails = json_decode($playerdata['Details']);
        $playerTotals = json_decode($playerdata['Totals']);
        $playerVehicle = json_decode($playerdata['Vehicle']);
        $playerWeapon = json_decode($playerdata['Weapon']);
        $playerClass = json_decode($playerdata['Class']);

        if (count($playerWeapon) < 2) {
            $playerWeap = "Rifle";
        } else {
            $playerWeap = $playerWeapon[2];
        }

        if ($playerDetails) {
            $opdate = $this->showDate(date(strtotime($playerDetails->date)));
        }

        $public = public_path();

        $avatar = $public . $avatar;
        $clantar = $public . $clantar;

        if (!file_exists($avatar)) {
            $avatar = Gravatar::src($useremail, 100);
            if ($avatar == "https://secure.gravatar.com/avatar/00000000000000000000000000000000?s=100&amp;r=r&amp;d=mm&amp;f=y") {
                $avatar = $public . "/avatars/thumb/clan.png";
            }
        }

        if (!file_exists($clantar)) {
            $clantar = $public . "/avatars/thumb/clan.png";
        }

        if ($country) {
            $flag = $this->imagine->open($public . '/img/flags_iso/24/' . strtolower($country) . '.png');
        } else {
            $flag = $this->imagine->open($public . '/img/flags_iso/24/_United Nations.png');
        }

        $sig = $this->imagine->create(new Box(601, 100));

        $avatar = $this->resizeAuto($avatar, 100, 100);
        $clantar = $this->resizeAuto($clantar, 95, 95);

        $background = $this->imagine->open($public . '/sigs/assets/background.png');

        $sig->paste($background, new Point(0, 0));
        $sig->paste($avatar, new Point(0, 0));
        $sig->paste($clantar, new Point(468, 2));
        $sig->paste($flag, new Point(109, 0));

        $colorWhite = new Color('#ffffff');
        $colorYellow = new Color('#ffa900');
        $colorDarkYellow = new Color('#a6771a');

        $titleFont = new Font($public . '/sigs/assets/font_0.otf', 12, $colorYellow);
        $detailFont = new Font($public . '/sigs/assets/pixelmix.ttf', 6, $colorDarkYellow);
        $boldFont = new Font($public . '/sigs/assets/pixelmix.ttf', 6, $colorYellow);

        if ($playerDetails) {
            $name = $playerDetails->PlayerName;
        } else {
            $name = $username;
        }

        $mainTitle = '[' . $clan->tag . '] ' . $name . ' - ' . $clan->name;

        $sig->draw()->text($mainTitle, $titleFont, new Point(135, 5), 0);

        //$sig->draw()->text($playerDetails->PlayerClass, $titleFont, new Point(110, 26), 0);
        //$sig->draw()->text('['.$clan->tag.'] '.$clan->name, $titleFont, new Point(110, 39), 0);

        $sig->draw()->text('FAV:', $detailFont, new Point(111, 27), 0);
        $sig->draw()->text('OPS:', $detailFont, new Point(111, 39), 0);
        $sig->draw()->text('EXP:', $detailFont, new Point(111, 52), 0);
        $sig->draw()->text('KILLS:', $detailFont, new Point(111, 64), 0);
        $sig->draw()->text('AMMO:', $detailFont, new Point(111, 76), 0);
        $sig->draw()->text('VEH EXP:', $detailFont, new Point(231, 39), 0);
        $sig->draw()->text('GUNNER:', $detailFont, new Point(231, 52), 0);
        $sig->draw()->text('PILOT:', $detailFont, new Point(231, 64), 0);
        $sig->draw()->text('MEDIC:', $detailFont, new Point(231, 76), 0);
        $sig->draw()->text('LAST OP:', $detailFont, new Point(111, 89), 0);

        if ($playerDetails && $playerTotals) {

            $sig->draw()->text($playerDetails->PlayerClass . ', ' . $playerWeap, $boldFont, new Point(165, 27), 0);
            $sig->draw()->text($playerTotals->Operations, $boldFont, new Point(165, 39), 0);
            $sig->draw()->text($playerTotals->CombatHours . ' mins', $boldFont, new Point(165, 52), 0);
            $sig->draw()->text($playerTotals->Kills, $boldFont, new Point(165, 64), 0);
            $sig->draw()->text($playerTotals->ShotsFired, $boldFont, new Point(165, 76), 0);
            $sig->draw()->text($playerTotals->VehicleTime . ' mins', $boldFont, new Point(295, 39), 0);
            $sig->draw()->text($playerTotals->VehicleKills . ' kills', $boldFont, new Point(295, 52), 0);
            $sig->draw()->text($playerTotals->PilotTime . ' mins', $boldFont, new Point(295, 64), 0);
            $sig->draw()->text($playerTotals->Heals, $boldFont, new Point(295, 76), 0);
            $sig->draw()->text($playerDetails->Operation . ', ' . $opdate, $boldFont, new Point(165, 89), 0);
        }

        /*
        $sig->draw()->text('OPS: '.$playerTotals->Operations, $detailFont, new Point(210, 40), 0);
        $sig->draw()->text('EXP: '.$playerTotals->CombatHours.' mins', $detailFont, new Point(210, 50), 0);
        $sig->draw()->text('KILLS: '.$playerTotals->Kills, $detailFont, new Point(210, 60), 0);
        $sig->draw()->text('AMMO: '.$playerTotals->ShotsFired, $detailFont, new Point(210, 70), 0);
        $sig->draw()->text('LAST ACTIVE: '.$playerDetails->date, $detailFont, new Point(210, 80), 0);
        $sig->draw()->text('LAST OP: '.$playerDetails->Operation, $detailFont, new Point(210, 90), 0);
        */

        $options = array(
            'quality' => 100,
        );

        $sig->save($public . '/sigs/' . $a3_id . '.jpg', $options);

    }

    /**
     * Resize/crop a file
     *
     * @param $file
     * @param $width
     * @param $height
     * @return mixed
     */
    protected function resizeCrop($file, $width, $height)
    {
        $image = $this->imagine->open($file);
        list($optimalWidth, $optimalHeight) = $this->getOptimalCrop($image->getSize(), $width, $height);

        // Find center - this will be used for the crop
        $centerX = ($optimalWidth / 2) - ($width / 2);
        $centerY = ($optimalHeight / 2) - ($height / 2);

        return $image->resize(new Box($optimalWidth, $optimalHeight))
            ->crop(new Point($centerX, $centerY), new Box($width, $height));
    }

    /**
     * Resize specifically in landscape?
     *
     * @param $file
     * @param $width
     * @param $height
     * @return mixed
     */
    protected function resizeLandscape($file, $width, $height)
    {
        $image = $this->imagine
            ->open($file);

        $dimensions = $image->getSize()
            ->widen($width);

        $image = $image->resize($dimensions);

        return $image;
    }

    /**
     * Resize specifically in portrait?
     *
     * @param $file
     * @param $width
     * @param $height
     * @return mixed
     */
    protected function resizePortrait($file, $width, $height)
    {
        $image = $this->imagine
            ->open($file);

        $dimensions = $image->getSize()
            ->heighten($height);

        $image = $image->resize($dimensions);

        return $image;
    }

    /**
     * Resize to exact dimensions
     *
     * @param $file
     * @param $width
     * @param $height
     * @return mixed
     */
    protected function resizeExact($file, $width, $height)
    {
        return $this->imagine
            ->open($file)
            ->resize(new Box($width, $height));
    }

    /**
     * Automatically decide how to resize an image
     *
     * @param $file
     * @param $width
     * @param $height
     * @return mixed
     */
    protected function resizeAuto($file, $width, $height)
    {
        // Image to be resized is wider (landscape)
        if ($height < $width) {
            return $this->resizeLandscape($file, $width, $height);

        }

        // Image to be resized is taller (portrait)
        if ($height > $width) {
            return $this->resizePortrait($file, $width, $height);
        }

        // Image to be resizerd is a square
        return $this->resizeExact($file, $width, $height);
    }

    /**
     * Get the optimal crop dimensions
     *
     * @param $size
     * @param $width
     * @param $height
     * @return array
     */
    protected function getOptimalCrop($size, $width, $height)
    {
        $heightRatio = $size->getHeight() / $height;
        $widthRatio = $size->getWidth() / $width;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = round($size->getHeight() / $optimalRatio, 2);
        $optimalWidth = round($size->getWidth() / $optimalRatio, 2);

        return [$optimalWidth, $optimalHeight];
    }

    /**
     * Generate timestamp to display
     *
     * TODO: This looks full of errors
     *
     * @param $timestamp
     * @return string
     */
    protected function showDate($timestamp)
    {
        $stf = 0;
        $cur_time = time();
        $diff = $cur_time - $timestamp;
        $phrase = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
        $length = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
        for ($i = sizeof($length) - 1; ($i >= 0) && (($no = $diff / $length[$i]) <= 1); $i--) {
            ;
        }
        if ($i < 0) {
            $i = 0;
        }
        $_time = $cur_time - ($diff % $length[$i]);
        $no = floor($no);
        if ($no <> 1) {
            $phrase[$i] .= 's';
        }
        $value = sprintf("%d %s ", $no, $phrase[$i]);
        if (($stf == 1) && ($i >= 1) && (($cur_tm - $_time) > 0)) {
            $value .= time_ago($_time);
        }
        return $value . 'ago';
    }

}