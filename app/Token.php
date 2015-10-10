<?php namespace ImguBox;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Token extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['token', 'refresh_token', 'provider_id', 'user_id'];

    protected $with = ['provider'];

    public function scopeIsImgurToken($query)
    {
        return $query->where('provider_id', 1);
    }

    public function scopeIsDropboxToken($query)
    {
        return $query->where('provider_id', 2);
    }

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
