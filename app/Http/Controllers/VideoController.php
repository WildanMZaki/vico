<?php

namespace App\Http\Controllers;

use App\Jobs\CompressVideosJob;
use App\Models\CompressedVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ZipArchive;

class VideoController extends Controller
{       
    public function form() {
        return view('pages.upload-form');
    }

    public function upload(Request $request)
    {
        $videoFiles = $request->file('videos');
        $downloadId = 'download-'.Str::random(16); // Generate a random string for download_id

        $videoNames = [];

        foreach ($videoFiles as $videoFile) {
            $videoName = $videoFile->getClientOriginalName();

            // Save the video to storage
            $videoFile->storeAs('videos', $videoName);

            // Save video info to database
            CompressedVideo::create([
                'download_id' => $downloadId,
                'video_name' => $videoName,
                'status' => false,
            ]);

            $videoNames[] = $videoName;
        }

        // Dispatch a job for background compression
        CompressVideosJob::dispatch($downloadId, $videoNames);

        // return redirect()->;
        return response()->json([
            'message' => 'Video uploaded, compression later',
            'download_id' => $downloadId,
            'url' => route('download.page', $downloadId)
        ]);
    }

    public function download_page($id) {
        $videos = CompressedVideo::where('download_id', $id)->get(['id', 'video_name']);
        $data['id'] = $id;
        $data['videos'] = $videos;
        return view('pages.download', $data);
    }
    
    public function download($video) {
        $file= storage_path("app/compressed-videos/$video");

        $headers = [
            'Content-Type' => 'application/video',
        ];

        return response()->download($file, 'compressed_'.basename($video), $headers);
    }
    
    public function download_all($id) {
        $videos = CompressedVideo::where('download_id', $id)->get('video_name');
        $zip_file = storage_path("app/compressed-videos/$id.zip");
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($videos as $video) {
            $videoPath = storage_path("app/compressed-videos/$video->video_name");
            $zip->addFile($videoPath, "compressed-videos/$video->video_name");
        }
        $zip->close();

        // Zipper::make('mydir/mytest12.zip')->add(['thumbnail/1461610581.jpg','thumbnail/1461610616.jpg']);

        $headers = [
            'Content-Type' => 'application/zip',
        ];

        return response()->download($zip_file, "compressed-videos.zip", $headers);
    }

    public function compress_progress($id) {
        $status = CompressedVideo::where('download_id', $id)->get(['id', 'progress', 'status']);
        return response()->json($status);
    }

}
