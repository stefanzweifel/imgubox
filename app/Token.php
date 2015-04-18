<?php namespace ImguBox;

use Illuminate\Database\Eloquent\Model;

class Token extends Model {

	protected $fillable = ['token', 'provider_id', 'user_id'];

    protected $with = ['provider'];

    /**
     * @return    Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('ImguBox\User');
    }

    /**
     * @return    Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo('ImguBox\Provider');
    }

}
