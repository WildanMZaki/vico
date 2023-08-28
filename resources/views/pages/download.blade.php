@extends('layouts.master')

@push('styles')
  <style>
    h1#hero { font-size: 4rem}
    #mainContent { min-height: 100vh}

    @media (max-width: 660px) {
      h1#hero { font-size: 2.5rem}
    }

    /* .icons { pointer-events: none; } */
    
    #forbiddenFilesToast {
      top: 50px;
      left: 25px;
      position: absolute;
      z-index: 20;
    }
  </style>
@endpush

@section('content')
<div class="container">
  <div class="row">
    <div class="d-flex flex-column align-items-center col-12 col-lg-8 offset-lg-2 bg-white py-5 shadow-sm px-lg-5" id="mainContent">
      <h1 class="fw-bold mb-3 mt-5" id="hero">Compression Result</h1>
      <div class="d-flex flex-column w-100" id="allCompressed">
        <div class="d-flex flex-column rounded-top" id="videos">
          @if (count($videos))
            @foreach ($videos as $i => $video)
              <div id="video{{$i}}" class="video d-flex border py-2 px-3 justify-content-between align-items-center">
                <p class="m-0 p-0 video-title w-50">{{$video->video_name}}</p>
                <p class="m-0 p-0 text-muted"></p>
                <button id="downloadBtn{{$video->id}}" disabled class="btn btn-outline-primary m-0 downloadBtn" data-url="{{route('download.video', $video->video_name)}}">
                  <span id="downloadBtnLabel{{$video->id}}">0 %</span> <i class='bx bxs-download m-0 icons'></i>
                </button>
              </div>
            @endforeach
          @else
              
          @endif
        </div>
        <div class="compress-btn p-3 bg-light d-flex justify-content-end border rounded-bottom">
          {{-- <span>Added <span id="fileUploaded">1</span> file</span> --}}
          <button type="button" class="btn btn-primary" id="downloadAllBtn" disabled>
            Download All <i class='bx bx-download'></i>
          </button>
        </div>
      </div>
      
    </div>
  </div>

</div>


    
  </div>
</div>

<p class="d-none" id="urlStatus">{{route('progress.compression', $id)}}</p>
@endsection

@push('scripts')
{{-- <script src="{{asset('js/utils/ajax-sender.js')}}"></script> --}}
<script>
  const download = url => {
    location.href = url;
  }
  $('.downloadBtn').click(e => {
    download(e.target.dataset.url);
  })

  function checkCompressionStatus() {
    let stop = false;
    fetch($('#urlStatus').html())
      .then(response => response.json())
      .then(data => {
        data.forEach(video => {
          const { id, progress, status } = video;
          $(`#downloadBtn${id}`).attr('disabled', !status);
          $(`#downloadBtnLabel${id}`).html(progress !== 100 ? `${progress} %` : 'Download');
        });
        const compressed = data.filter(video => video.status === 1);
        stop = compressed.length === data.length;
        // Poll again after a certain interval
        if (!stop) {
          setTimeout(checkCompressionStatus, 7500); // Poll every 5 seconds
        } else {
          $('#downloadAllBtn').attr('disabled', false);
        }
      })
      .catch(error => {
          console.error('Error fetching compression progress:', error);
      });
  }

  // Start polling when the page loads and wait 4 seconds
  setTimeout(checkCompressionStatus, 4000);

  $('#downloadAllBtn').click(() => {
    
  });

</script>
@endpush