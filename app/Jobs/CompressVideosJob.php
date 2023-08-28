<?php

namespace App\Jobs;

use App\Models\CompressedVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Session;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use PHPUnit\Event\Code\Throwable;

class CompressVideosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;

    protected $downloadId;
    protected $videoNames;
    protected $videoInCompressing;

    public function __construct($downloadId, $videoNames)
    {
        $this->downloadId = $downloadId;
        $this->videoNames = $videoNames;
        $this->videoInCompressing = $videoNames[0];
    }

    public function handle()
    {
        $lowBitrateFormat = (new X264('aac', 'libx264'))
            ->setKiloBitrate(500)
            ->setAdditionalParameters(['-preset', 'fast', '-crf', '20']);
        foreach ($this->videoNames as $videoName) {
            $this->videoInCompressing = $videoName;
            $compressedVideo = CompressedVideo::where('download_id', $this->downloadId)
                                               ->where('video_name', $this->videoInCompressing);
            FFMpeg::fromDisk('videos')
                ->open($videoName)
                ->export()
                ->onProgress(function ($percentage) use ($compressedVideo) {
                    $compressedVideo->update(['progress' => $percentage]);
                })
                ->toDisk('local')
                ->inFormat($lowBitrateFormat)
                ->save("compressed-videos/$videoName");

            // Update the status in the database
            CompressedVideo::where('download_id', $this->downloadId)
                ->where('video_name', $videoName)
                ->update(['status' => true]);
        }
    }

    public function failed(Throwable $exception): void
    {
        dd($exception);
    }
}
