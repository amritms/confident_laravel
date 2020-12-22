<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WatchesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\Watch::create([
            'user_id' => $request->user()->id,
            'video_id' => $request->get('video_id')
        ]);

         event('video.watched', [-1]);

        return response(null, 204);
    }
}
