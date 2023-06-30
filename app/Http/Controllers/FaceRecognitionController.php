<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use File;

class FaceRecognitionController extends Controller
{
    // ==============================================================
    // @remind ON PROGRESS - being used as reference for now
    // ==============================================================
    public function index() {
        return view('user.face-recognition.clock');
    }

    public function register() {
        return view('user.face-recognition.register');
    }
    // ==============================================================

    public function saveUserImage(Request $request) {
        $file_name = null;
        $base64encodedString = $request->image;
        $generated_name = uniqid() . '_' . time() . date('Ymd') . '_IMG';
        $fileBin = file_get_contents($base64encodedString);
        $mimeType = mime_content_type($base64encodedString);   
        if('image/png' == $mimeType) {
            $file_name = $generated_name . '.png';
        } else if ('image/jpg' == $mimeType) {
            $file_name = $generated_name . '.jpg';
        } else if ('image/jpeg' == $mimeType) {
            $file_name = $generated_name . '.jpeg';
        } else {
            return response()->json([
                'success'=>false,
                'message'=>'invalid file type only png, jpeg and jpg files are allowed',
            ],400);
        }
        // image directory
        $dir_label = Auth::user()->id;
        $directory = base_path() . '/uploads/face-recognition/' . $dir_label . '/';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $this->clearUserDir($directory); // clear so that only 1 image file in dir
        file_put_contents( $directory . $file_name, $fileBin);
        return response()->json([
            'success' => true,
        ]);
    }

    public function clearUserDir($path) {
        $files = glob($path . '*');
        foreach($files as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

    public function getLabeledImages() {
        $path = base_path() . '/uploads/face-recognition/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $directories = array_map('basename', File::directories($path));
        $labeled_images = [];
        foreach ($directories as $key => $user_id) {
            $image_path = glob($path . '/' . $user_id . "/*")[0];
            $type = pathinfo($image_path, PATHINFO_EXTENSION);
            $data = file_get_contents($image_path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $user_name = User::find($user_id)->employee->getFullName();
            array_push($labeled_images, [
                'user_id' => $user_id,
                'user_name' => $user_name,
                'image' => $base64,
            ]);
        }
        return response()->json($labeled_images);
    }

}