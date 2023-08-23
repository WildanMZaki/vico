@extends('layouts.master')

@push('styles')
  <style>
    h1#hero { font-size: 4rem}

    #drop_zone {
      border-style: dashed;
      border: 5px #0d6efd dashed;
      width: 100%;
      padding : 60px 0;
    }
    #drop_zone p {
      font-size: 20px;
      text-align: center;
    }
    #btn_upload, #selectfile {
      display: none;
    }

    @media (max-width: 660px) {
      h1#hero { font-size: 2.5rem}
    }
  </style>
@endpush

@section('content')
<div class="container">
  <div class="row">
    <div class="d-flex flex-column align-items-center col-12 col-lg-8 offset-lg-2 bg-white py-5 shadow-sm px-lg-5">
      <h1 class="fw-bold my-3" id="hero">Video Compressor</h1>
      <div class="d-flex flex-column w-100 d-none" id="allUploaded">
        <div class="btn-container d-flex w-100">
          <button class="btn btn-outline-primary rounded-top rounded-bottom-0 file-picker">
            Select more videos <i class='bx bxs-file-plus'></i>
          </button>
        </div>
        <div class="d-flex flex-column" id="videos">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="file" id="selectfile" name="video" multiple>
          {{-- <div class="video d-flex border py-2 px-3 justify-content-between align-items-center">
            <p class="m-0 p-0 video-title">Lorem ipsum dolor sit amet Lorem, ipsum.</p>
            <p class="m-0 p-0 text-muted">38.33 Mb</p>
            <button class="btn p-0 m-0">
              <i class='bx bx-x-circle fs-3 m-0 p-0'></i>
            </button>
          </div> --}}
        </div>
        <div class="compress-btn p-3 bg-light d-flex justify-content-between border rounded-bottom">
          <span>Added <span id="fileUploaded">1</span> file</span>
          <button type="button" class="btn btn-primary" id="compress_btn">
            Compress now! <i class='bx bx-right-arrow-alt'></i>
          </button>
        </div>
      </div>

      <p class="d-none">{{ route('compress') }}</p>

      <div id="drop_zone" class="bg-light my-3">
        <p>
          <button type="button" id="btn_file_pick" class="btn btn-outline-primary file-picker">
            <i class='bx bxs-file-plus'></i> Select File
          </button>
        </p>
        <p><small>Max uploaded: 500 MB</small></p>
        <p id="message_info"></p>
     </div>
    </div>
  </div>
</div>

@includeIf('components.drop-area')
@endsection

@push('scripts')
<script>
  var fileobj;
  function middleElipsis(str) {
    if (str.length > 28) {
      return str.substr(0, 17) + '...' + str.substr(str.length-8, str.length);
    }
    return str;
  }

  $(document).ready(function(){
    $(".container").on("dragover", event => {
      event.preventDefault();  
      event.stopPropagation();
      $('#dropArea').removeClass('d-none');
      return false;
    });

    $("#dropArea").on("dragover", event => {
      event.preventDefault();  
      event.stopPropagation();
      return false;
    });
    $('#dropArea').on('dragend', () => $('#dropArea').addClass('d-none'));
    $('#dropArea').on('dragleave', () => $('#dropArea').addClass('d-none'));
    $("#dropArea").on("drop", event => {
      event.preventDefault();  
      event.stopPropagation();
      $('#dropArea').addClass('d-none');
      fileobj = event.originalEvent.dataTransfer.files[0];
      const fname = middleElipsis(fileobj.name);
      const fsize = fileobj.size;
      $('#videos').append(`
        <div class="video d-flex border py-2 px-3 justify-content-between align-items-center">
           <p class="m-0 p-0 video-title w-50">${fname}</p>
           <p class="m-0 p-0 text-muted">${ bytesToSize(fsize) }</p>
           <button class="btn p-0 m-0">
             <i class='bx bx-x-circle fs-3 m-0 p-0'></i>
           </button>
         </div>
      `);
      const inputFile = document.getElementById('selectfile');
      inputFile.files[inputFile.files.length] = fileobj;
      $('#allUploaded').removeClass('d-none');
      $('#fileUploaded').html(document.getElementById('selectfile').files.length);
    });

    $('.file-picker').click(() => {
      /*normal file pick*/
      document.getElementById('selectfile').click();
      document.getElementById('selectfile').onchange = () => {
        fileobj = document.getElementById('selectfile').files[0];
        const fname  = middleElipsis(fileobj.name);
        const fsize = fileobj.size;
        $('#videos').append(`
          <div class="video d-flex border py-2 px-3 justify-content-between align-items-center">
            <p class="m-0 p-0 video-title w-50">${fname}</p>
            <p class="m-0 p-0 text-muted">${ bytesToSize(fsize) }</p>
            <button class="btn p-0 m-0">
              <i class='bx bx-x-circle fs-3 m-0 p-0'></i>
            </button>
          </div>
        `);
        $('#allUploaded').removeClass('d-none');
        $('#fileUploaded').html(document.getElementById('selectfile').files.length);
      };
    });
    $('#compress_btn').click(function(){
      if(fileobj=="" || fileobj==null){
        alert("Please select a file");
        return false;
      }else{
        ajax_file_upload(fileobj);
      }
    });
  });
  
  function ajax_file_upload(file_obj) {
    if(file_obj != undefined) {
      const form_data = new FormData(); 
      form_data.append('video', file_obj);
      $.ajax({
        type: 'POST',
        url: $('#url').html(),
        headers: {'X-CSRF-TOKEN': $('#token').val()},
        contentType: false,
        processData: false,
        data: form_data,
        beforeSend:function(response) {
          $('#message_info').html("Compressing your video, please wait...");
        },
        success:function(response) {
          console.log(response);
          $('#message_info').html(response.message);
          $('#selectfile').val('');
        }
      });
    }
  }

  function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    let num = (bytes / Math.pow(1024, i)).toFixed(2)
    //  num = num[num.length-1] === '0' ? num.slice(0, num.length-1) : num;
    return num + ' ' + sizes[i];
  }

</script>
@endpush