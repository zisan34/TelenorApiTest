<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class KeyValueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $keyValueArr = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3' );

    private $redis;
    public function __construct(){
        $this->redis = app()->make('redis');
    }

    public function index(Request $request)
    {
        //

        // return response()->json(['data' => $request]);

        if($request->has('keys'))
        {
            $keys = explode(',', $request->keys);
            foreach ($keys as $key) {
                $data[$key] = $this->keyValueArr[$key];
            }
            return response()->json(['data' => $data], 200);
        }
        else
        {
            return response()->json(['data' => $this->keyValueArr], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        foreach ($request->all() as $key => $value) {
            $this->keyValueArr[$key] = $request[$key];
           if(!$this->redis->exists($key)){
            $this->redis->set($key, $request[$key], 'EX', 300); 
           }

        }
        return response()->json(['data' => $this->redis->keys('*')],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        // return $request;
        foreach ($request->all() as $key => $value) {
            $this->keyValueArr[$key] = $request[$key];
        }
        return response()->json(['data' => $this->keyValueArr], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
