<?php

namespace Crater\Http\Controllers;

use Crater\Models\LogsDev;
use Illuminate\Http\Request;
/* use Auth; */
use Log;

class InternationalRateController extends Controller
{
    //

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InternationalRateController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\ProfileDidTollFree  $profileDidTollFree
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\ProfileDidTollFree  $profileDidTollFree
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\ProfileDidTollFree  $profileDidTollFree
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
    }
}
