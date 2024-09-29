<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
    *@OA\Post(
     *     path="/api/upload-local",
     *     summary="Upload into local storage",
     *     tags={"Upload"},
     *    @OA\RequestBody(
    *         @OA\JsonContent(
    *            required={"image"},
    *            @OA\MediaType(
    *                mediaType="multipart/form-data",
    *            ),
    *            @OA\Property(property="image", type="string", format="binary"),
    *          ),
    *    ),
     *     @OA\Response(response="201", description="Image uploaded sucessfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function upload_local(Request $request){
        $validator = \Validator::make($request->all(), [
            'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
         ]);
         if ($validator->fails()) {
            return response()->json([
                "message" => $validator->messages()->first(),
            ],500);
         }
         $uploadFolder = $request->upload_folder ?? 'products';
         $image = $request->file('image');
         $image_uploaded_path = $image->store($uploadFolder, 'public');
         return response()->json([
            "message" => "File Uploaded Successfully",
            "image_name" => basename($image_uploaded_path),
            "image_url" => \Storage::disk('public')->url($image_uploaded_path),
            "mime" => $image->getClientMimeType()
        ],200);
    }
}
