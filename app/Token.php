<?php namespace ImguBox;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Token extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['token', 'refresh_token', 'provider_id', 'user_id'];

    protected $with = ['provider'];

    public function scopeIsImgurToken($query)
    {
        return $query->whereHas('provider', function($q) {

            return $q->whereName('Imgur');

        });
    }

    public function scopeIsDropboxToken($query)
    {
        return $query->whereHas('provider', function($q) {

            return $q->whereName('Dropbox');

        });
    }

    /**
     * @return    Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return    Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }


    /**
     * Retrieve the Token attribute.
     *
     * @param   mixed
     * @return  string
     */
    public function getTokenAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    /**
     * Set the Token attribute.
     *
     * @param   mixed
     * @return  void
     */
    public function setTokenAttribute($value)
    {
        $this->attributes['token'] = Crypt::encrypt($value);
    }

    /**
     * Retrieve the RefreshToken attribute.
     *
     * @param   mixed
     * @return  string
     */
    public function getRefreshTokenAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    /**
     * Set the RefreshToken attribute.
     *
     * @param   mixed
     * @return  void
     */
    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token'] = Crypt::encrypt($value);
    }

}
