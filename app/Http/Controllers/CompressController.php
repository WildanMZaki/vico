<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FFMpeg;
use FFMpeg\Format\Video\X264;

class CompressController extends Controller
{
    function index() {
      $data['param'] = 'Ini data';
      return view('pages.compress', $data);
    }

    // public function compress(Request $request) {
    //     $video = $request->file('video');
    //     if ($video->getType() === 'video') {
            
    //     }
    //     // // $filename = $video->getClientOriginalName();
    //     // // $save_path = public_path('img/berita/');
    //     // // if (!file_exists($save_path)) {
    //     // //     mkdir($save_path, 775, true);
    //     // // }
    //     // // $command = "ffmpeg -i $filename -b:v 360p -bufsize 360p result.mp4";
    //     // // system($command);
    //     // $path = public_path('/storage');
    //     // echo "Video Stored";
    //     // if (!file_exists($path)) {
    //     //     mkdir($path, 775, true);
    //     // }
    //     // $video->move($path, 'uploaded2.mp4');

    //     // $lowBitrateFormat = (new X264('libmp3lame', 'libx264'))->setKiloBitrate(500);
    //     // FFMpeg::fromDisk('storage')
    //     // ->open('uploaded2.mp4')
    //     // ->export()
    //     // ->inFormat($lowBitrateFormat)
    //     // ->save('resized2.mp4');

    //     // return "Video resized";

    //     $
        
    // }

    public function compress(Request $request)
    {
        $request->validate([
            'video' => 'required|mimetypes:video/avi,video/mpeg,video/mp4|max:50000', // Adjust max size as needed
        ]);

        $videoPath = $request->file('video')->store('videos');

        $compressedPath = $this->compressProcess($videoPath);

        echo 'Compresion Success: <br />';
        echo $compressedPath . '<br />';

        // echo "Video compressed </br>";
        // echo $compressedPath;
    }

    protected function compressProcess($videoPath)
    {
        $videoName = pathinfo($videoPath, PATHINFO_FILENAME) .'.mp4';

        $lowBitrateFormat = (new X264('libmp3lame', 'libx264'))
            ->setKiloBitrate(500)
            ->setAdditionalParameters(['-preset', 'fast', '-crf', '23']);
        FFMpeg::fromDisk('videos')
            ->open($videoPath)
            ->export()
            ->toDisk('videos')
            ->inFormat(new X264())
            ->save('compressed-videos/'.$videoName);

        $outputPath = storage_path('app/compressed-videos/'.$videoName);
        return $outputPath;
    }
}
