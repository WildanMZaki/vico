<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompressedVideosTable extends Migration
{
    public function up()
    {
        Schema::create('compressed_videos', function (Blueprint $table) {
            $table->id();
            $table->string('download_id', 25);
            $table->string('video_name', 100);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('compressed_videos');
    }
}