<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrackResource;
use App\Http\Traits\ListenAudio;
use App\Http\Traits\UploadFile;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrackController extends Controller
{
    use UploadFile, ListenAudio;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): AnonymousResourceCollection
    {
        return TrackResource::collection(Track::paginate(4));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Track $track): TrackResource
    {
        $request->validate(
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required',
                  'audio'=>'required',
                'is_favourite' => 'nullable',
            ]
        );
        $testIfImageBase64 = $track->is_base64($request->image); //check New image if new image it must be type base64
        $testIfAudioBase64 = $track->is_base64($request->audio); //check New audio if new audio it must be type base64

        if ($request->image && $testIfImageBase64) {
            $imageName = $this->uploadFile($request['image']);
        }
        if ($request->audio && $testIfAudioBase64) {
            $audioName = $this->uploadFile($request['audio']);
        }
        $track = Track::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => isset($imageName) ? '/storage/tracks/' . $imageName : "",
            'audio' => isset($audioName) ? '/storage/tracks/audios/' . $audioName : "",
            'is_favourite' => $request->is_favourite ?? 0,
        ]);
        return new TrackResource($track);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function show(Track $track, $id): TrackResource
    {
        $track = Track::find($id);
        $response=$this->listen($track->audio);
        // info($response);
    //     $t=$track->getDuration();
    //     $e=$track->getDurationEstimate();
    //    $k=$track->formatTime($t);
    //    $a=$track->formatTime($e);
    //    info($k);
    //    info($e);
    //    info($a);


        return TrackResource::make($track);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Track $track, $id): TrackResource
    {
        $testIfImageBase64 = $track->is_base64($request->image); //check New image if new image it must be type base64
        $testIfAudioBase64 = $track->is_base64($request->audio); //check New audio if new audio it must be type base64


        $request->validate(
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required',

                 'audio'=>'required',
                'is_favourite' => 'nullable',
            ]
        );
        if ($request->image && $testIfImageBase64) {
            $imageName = $this->uploadFile($request['image']);
        }
        if ($request->audio && $testIfAudioBase64) {
            $audioName = $this->uploadFile($request['audio']);
        }

        $track = Track::find($id);
        $track->update(['title' => $request->title, 'description' => $request->description, 'is_favourite' => $request->is_favourite, 'image' => $testIfImageBase64 == true ? '/storage/tracks/' . $imageName : $request->image, 'audio' => $testIfAudioBase64  == true ? '/storage/tracks/audios/' . $audioName : $request->audio]);
        return new TrackResource($track);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $track, $id): Response
    {
        $track->find($id)->delete();
        return response()->noContent();
    }
}
