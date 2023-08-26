<?php

namespace App\Http\Controllers;

use App\Jobs\CompressVideosJob;
use App\Models\CompressedVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class VideoController extends Controller
{
    public function form() {
        return view('pages.upload-form');
    }

    public function upload(Request $request)
    {
        $videoFiles = $request->file('videos');
        $downloadId = 'download-'.Str::random(16); // Generate a random string for download_id

        $compressedVideos = [];

        foreach ($videoFiles as $videoFile) {
            $videoName = $videoFile->getClientOriginalName();

            // Save the video to storage
            $path = $videoFile->storeAs('videos', $videoName);

            // Save video info to database
            CompressedVideo::create([
                'download_id' => $downloadId,
                'video_name' => $videoName,
                'status' => false,
            ]);

            $compressedVideos[] = $path;
        }

        // Dispatch a job for background compression
        // CompressVideosJob::dispatch($downloadId, $compressedVideos);

        // return redirect()->route('download-video', ['download_id' => $downloadId]);
        return response()->json([
            'message' => 'Video uploaded, compression later',
            'download_id' => $downloadId
        ]);
    }
    
    public function download($path) {
        
    }
}
