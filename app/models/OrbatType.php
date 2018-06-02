<?php
class OrbatType extends Eloquent
{
    protected $table = 'orbattypes';

    public function clan()
    {
        return $this->belongsTo('Clan', 'type', 'type');
    }
}
