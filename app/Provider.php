<?php namespace ImguBox;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

	protected $fillable = [
        'name', 'short_name', 'is_storage'
    ];

    public function scopeIsImgur($query)
    {
        return $query->where('id', 1);
    }

    public function scopeIsDropbox($query)
    {
        return $query->where('id', 2);
    }

    /**
     * @return    Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens()
    {
        return $this->hasMany('ImguBox\Token');
    }

}
