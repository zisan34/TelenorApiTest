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

    private $redis;

    public function __construct(){
        $this->redis = app()->make('redis');
    }

    public function index(Request $request)
    {
        //
        $this->reset_ttl();

        $keys_arr = array();
        if($request->has('keys'))
        {
            $keys_arr = explode(',', $request->keys);
        }

        $data = $this->fetch_key_value($keys_arr);

        if(count($data) > 0)
        {
            return response()->json(['data' => $data], 200);
        }
        else
        {
            return response()->json([], 204);
        }
    }


    private function fetch_key_value($keys=null)
    {

        if($keys)
            $keys_arr = $keys;
        else
            $keys_arr = $this->redis->keys('*');

        $response_arr = array();
        foreach($keys_arr as $key)
        {
            if($this->redis->exists($key))
            {
                $response_arr[$key]=$this->redis->get($key);
            }
        }

        return $response_arr;

    }

    private function reset_ttl()
    {
        $keys_arr = $this->redis->keys('*');

        foreach($keys_arr as $key)
        {
            if($this->redis->exists($key))
            {
                $this->redis->setex($key, 300, $this->redis->get($key));
            }
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
        $keys_arr = array();
        foreach ($request->all() as $key => $value)
        {
            array_push($keys_arr, $key);
            if(!$this->redis->exists($key))
            {
                $this->redis->set($key, $request[$key], 'EX', 300); 
            }

        }
        return response()->json(['data' => $this->fetch_key_value($keys_arr)], 201);
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
        $keys_arr = array();
        foreach ($request->all() as $key => $value)
        {
            array_push($keys_arr, $key);
            if($this->redis->exists($key))
            {
                $this->redis->setex($key, 300, $value);
            }
        }

        $data = $this->fetch_key_value($keys_arr);
        if(count($data) > 0)
            return response()->json(['data' => $data], 200);
        else
            return response()->json([], 304);
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
