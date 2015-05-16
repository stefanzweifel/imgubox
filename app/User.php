<?php namespace ImguBox;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['email', 'password', 'imgur_username'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


	protected $with = ['tokens', 'logs'];

	/**
	 * @return    Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function tokens()
	{
	    return $this->hasMany('ImguBox\Token');
	}

	/**
	 * @return    Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function logs()
	{
	    return $this->hasMany('ImguBox\Log');
	}

	/**
	 * @return    Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function imgurTokens()
	{
	    return $this->hasMany('ImguBox\Token')->where('provider_id', 1);
	}

	/**
	 * @return    Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function dropboxTokens()
	{
	    return $this->hasMany('ImguBox\Token')->where('provider_id', 2);
	}


	public function dropboxToken()
	{
		return $this->hasOne('ImguBox\Token')->where('provider_id', 2);
	}

	public function imgurToken()
	{
		return $this->hasOne('ImguBox\Token')->where('provider_id', 1);
	}

	public function scopeHasDropboxToken($query)
	{
		return $query->whereHas('tokens', function($q) {

			return $q->where('provider_id', 2);

		});
	}

	public function scopeHasImgurToken($query)
	{
		return $query->whereHas('tokens', function($q) {

			return $q->where('provider_id', 1);

		});
	}

}
