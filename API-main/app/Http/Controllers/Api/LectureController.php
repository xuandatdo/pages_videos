<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Http\Resources\LectureResource;
use Illuminate\Support\Facades\Validator;
class LectureController extends Controller
{
    public function index(){
        $lecture = Lecture::get();

        if($lecture){
            return LectureResource::collection($lecture);
        } else {
            return response()->json(['message' => ' No record Available'], 200);
        }
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required | string | max:255',
            'description' => 'required',
            'video_url' => 'required | url',
            'category' => 'required | string | max:255',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }


        $lecture = Lecture::create([
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'category' => $request->category,
        ]);

        return response()->json(['message' => 'Lecture Created Successfully', 'data' => new LectureResource($lecture) ],200);
    }
    public function show(Lecture $lecture){
        return new LectureResource($lecture);
    }
    public function edit($id){
        $lecture = Lecture::find($id);
        if($lecture){
            return new LectureResource($lecture);
        } else {
            return response()->json(['message' => ' No record Available'], 404);
        }
    }

    public function update(Request $request, int $id){
        $validator = Validator::make($request->all(), [
            'title' => 'required | string | max:255',
            'description' => 'required',
            'video_url' => 'required | url',
            'category' => 'required | string | max:255',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }
        else {
            $lecture = Lecture::find($id);
            
            if($lecture){
                $lecture -> update([
                    'title'=> $request->title,
                    'description'=> $request->description,
                    'video_url'=> $request->video_url,
                    'category'=> $request->category
                ]);
                return response()->json(['message' => 'Lecture Updated Successfully', 'data' => new LectureResource($lecture) ],200);
            }
            else {
                return response()->json(['message' => ' No record Available'], 404);
            }
        }

        
    }
    public function destroy($id){
        $lecture = Lecture::find($id);
        if($lecture) {
            $lecture->delete();
            return response()->json(['message'=> 'Delete Lecture Successfully'],200);
        }
        else {
            return response()->json(['message'=> 'Not record Available'],404);
        }
    }
}
