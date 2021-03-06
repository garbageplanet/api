<?php

namespace App\Http\Controllers\Cleanings;

use Log;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Cleaning;
use App\Models\User;
use JWTAuth;
use DB;
use Carbon\Carbon;
use Auth;

class CleaningsController extends Controller
{
    public function __construct()
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['only' => ['store', 'update', 'destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $cleanings = Cleaning::all();

        //long route to do this
        $cleaningsArray= [];

        foreach ($cleanings as $cleaning) {
            $array = $cleaning->toArray();
            $cleaningsArray[] = $array;
        }

        $cleanings = collect($cleaningsArray);

        return $cleanings;
        // return response()->json($cleaningsArray, 200)->header('Access-Control-Allow-Origin', '*');

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function withinBounds(Request $request)
    {
        // parse bounds
        $bounds = str_replace(",", ", ", $request->bounds);

        $query = "SELECT * FROM cleanings WHERE cleanings.geom && ST_MakeEnvelope($bounds)";

        $cleanings = DB::select($query);

        //get id's of the cleanings
        $cleaning_ids = [];

        foreach ($cleanings as $cleaning) {
            $cleaning_ids[] = $cleaning->id;
        }

        $cleanings = Cleaning::whereIn('id', $cleaning_ids)->get();

        return $cleanings;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        Log::debug(print_r($data, true));

        if (!Auth::check()) {
            $glome = Glome::createGlomeAccount();
            $user = User::create(['email' => $glome, 'password' => '12345678', 'name' => $glome]);
            Auth::attempt(['email' => $glome, 'password' => '12345678']);
        }

        $validator = $this->validate($request, [
              'recurrence'  => 'alpha|nullable'
            , 'joins'       => 'num|nullable'
            , 'datetime'    => 'date|required'
            , 'tweetonsave' => 'boolean|nullable'
            , 'latlng'      => array(
                    'required'
                  , 'max:60'
                  , 'regex:/^([-+]?\d{1,2}[.]\d+)\s*,\s*([-+]?\d{1,3}[.]\d+)$/u'
            )
            , 'tags' => array(
                    'max:60'
                  , 'nullable'
                  , 'regex:/^[\p{L}\p{N}\040,.-]+$/'
            )
            , 'note' => array(
                    'max:140'
                  , 'nullable'
                  , 'min:10'
                  , 'regex:/^[\p{L}\p{N}\040,.-]+$/'
            )
        ]);

        $cleaning = Auth::user()->createdCleanings()->create($data);

        $cleaning->makePoint();

        return $cleaning;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $cleaning = Cleaning::findOrFail($id);
        //long route to do this
        $array = $cleaning->toArray();
        $cleaning = collect($array);
        return $cleaning;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //currently anyone authenticated user can update anything
        //find id
        $cleaning = Cleaning::findOrFail($id);

        //update request
        $cleaning->update($request->all());

        $array = $cleaning->toArray();

        $cleaning = collect($array);
        return $cleaning;
    }

    public function attend(Request $request, $id)
    {
        $cleaning = Cleaning::findOrFail($id);

        $cleaning->attend($id);

        if($cleaning->save()) {
            $returnData = $cleaning->find($cleaning->id)->toArray();
            $data = array ("message" => "event updated","data" => $returnData );
            return response()->json(["data" => $data], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //find id
        $cleaning = Cleaning::findOrFail($id);
        //delete
        $cleaning->delete();
        //delete types

        return response()->json("{}", 200);

    }
}
