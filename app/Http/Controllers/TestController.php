<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Currency;


class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->input('id');
        if (strlen(trim($id)) > 0) {
            if (is_numeric($id)) {
                $response = Currency::find($id, $columns = array('id', 'title'));
                if (is_array($response) && sizeof($response) > 0) {
                    return $response;
                }
                else {
                    return response()->json(['code' => 400, 'message' => 'No data found'], 400);
                }
            }
            else {
                return response()->json(['code' => 400, 'message' => 'invalid id. value should be numeric'], 400);
            }
        }
        else {
            return Currency::all('id', 'title');
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $id)
    {
        //if (strlen(trim($id)) > 0) {
        //    return Currency::findorFail($id);
        //}
        //else {
        //    return 'failed badly';
        //}
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
    public function update(Request $request, $id)
    {
        //
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
