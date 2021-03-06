<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\\Models\Trash;
use App\\Models\User;
use JWTAuth;
use DB;
use Carbon\Carbon;
use Auth;

class Clean extends Model
{
    /**
      * NB: A 'Clean' is the event of cleaning garbage, not an actual cleaning event where people gather to clean garbage. A cleaning/gathering event is referred to as a 'Cleaning'.
      */
    protected $fillable = [
          'trash_id'
        , 'user_id'
        , 'litter_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**********************
     * Relationships begins
     */

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function trash()
    {
        return $this->belongsTo('App\Models\Trash', 'trash_id');
    }

    public function litter()
    {
        return $this->belongsTo('App\Models\Litter', 'litter_id');
    }

    /********************
     * Relationships ends
     */
}
