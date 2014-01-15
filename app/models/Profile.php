<?php

class Profile extends Eloquent {

    use Codesleeve\Stapler\Stapler;

	protected $guarded = array();
	public static $rules = array();

    public function __construct(array $attributes = array()) {
        $this->hasAttachedFile('avatar', [
            'styles' => [
            'medium' => 'x300',
            'thumb' => 'x100',
            'tiny' => 'x40'
            ]
        ]);

        parent::__construct($attributes);
    }

    public function user()
    {
       return $this->belongsTo('User');
    }

    public function clan() {
        return $this->belongsTo('Clan');
    }

    public function applications()
    {
        return $this->hasMany('Application');
    }

}
