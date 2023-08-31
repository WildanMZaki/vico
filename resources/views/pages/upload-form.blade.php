@extends('layouts.master')

@push('styles')
  <style>
    h1#hero { font-size: 4rem}
    #mainContent { min-height: 100vh}

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

    .icons { pointer-events: none; }
    
    .toast-container {
      top: 50px;
      right: 25px;
      position: absolute;
      z-index: 20;
    }
  </style>
@endpush

@section('content')
<div class="container">
  <div class="row">
    <div class="d-flex flex-column align-items-center col-12 col-lg-8 offset-lg-2 bg-white py-5 shadow-sm px-lg-5" id="mainContent">
      <h1 class="fw-bold mb-3 mt-5" id="hero">Video Compressor</h1>
      <div class="d-flex flex-column w-100 d-none" id="allUploaded">
        <div class="btn-container d-flex w-100">
          <button class="btn btn-outline-primary rounded-top rounded-bottom-0 file-picker">
            Select more videos <i class='bx bxs-file-plus'></i>
          </button>
        </div>
        <div class="d-flex flex-column" id="videos">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="file" id="selectfile" name="video" multiple accept="video/*" max="10">
          
        </div>
        <div class="compress-btn p-3 bg-light d-flex justify-content-between border rounded-bottom">
          <span>Added <span id="fileUploaded">1</span> file</span>
          <button type="button" class="btn btn-primary" id="compress_btn">
            Compress now! <i class='bx bx-right-arrow-alt'></i>
          </button>
        </div>
      </div>

      <p class="d-none" id="url">{{ route('upload-video') }}</p>

      <div id="drop_zone" class="bg-light my-3">
        <p>
          <button type="button" id="btn_file_pick" class="btn btn-outline-primary file-picker">
            <i class='bx bxs-file-plus'></i> <span id="btnPicker">Select videos</span>
          </button>
        </p>
        <p><small>Max: 10 files, 500 MB</small></p>
        <p id="message_info"></p>
     </div>
    </div>
  </div>

</div>

@includeIf('components.drop-area')

<div class="toast-container" id="toastContainer">
  {{-- Toasts goes here --}}
</div>

@endsection

@push('scripts')
<script>
  const maxFile = 10;
  const maxSizeTotal = 500;
  const fileList = new DataTransfer();
  let fileNumber = 0;
  const fileNumbers = [];

  function middleElipsis(str) {
    if (str.length > 28) {
      return str.substr(0, 17) + '...' + str.substr(str.length-8, str.length);
    }
    return str;
  }

  function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    let num = (bytes / Math.pow(1024, i)).toFixed(2)
    //  num = num[num.length-1] === '0' ? num.slice(0, num.length-1) : num;
    return num + ' ' + sizes[i];
  }

  // const ext = str => (str.split('.')).pop();

  function addToast(title, body, time = 'just now') {
    const t = (new Date()).getTime();
    $('#toastContainer').append(`
      <div id="theToast${t}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <i class="bx bx-x text-danger fs-4"></i>
          <strong class="me-auto" id="toastTitle">${title}</strong>
          <small id="toastTime">${time}</small>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody">
          ${body}
        </div>
      </div>
    `);
    $(`#theToast${t}`).toast('show');
  }

  $(document).ready(function(){
    $(".container").on("dragover", event => {
      event.preventDefault();  
      event.stopPropagation();
      $('#dropArea').removeClass('d-none');
      return false;
    });
    $('.container').on('dragend', () => $('#dropArea').addClass('d-none'));

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
      const files = event.originalEvent.dataTransfer.files;
      const allowedExts = ['video/mp4', 'video/mkv'];
      const filteredFiles = [...files].filter(file => allowedExts.includes(file.type));
      const invalid = [...files].filter(file => !(allowedExts.includes(file.type)));
      addFiles(filteredFiles);
      if (invalid.length) {
        let listInvalidFile = '';
        invalid.forEach(file => {
          listInvalidFile += `<li>${middleElipsis(file.name)}</li>`;
        });
        setTimeout(() => {
          addToast('Format file tidak didukung', `
            <span>${invalid.length} file gagal diupload :</span>
            <ul>${listInvalidFile}</ul>
            <small class="text-muted">Saran: Tolong upload file video</small>
          `);
        }, 500);
      }
    });

    function addFiles(files) {
      let maxFileReached = false;
      let maxSizeReached = false;
      files.forEach(file => {
        const sizeAll = [...fileList.files].reduce((x, {size}) => x + size, 0) + file.size;
        maxFileReached = fileList.files.length +1 > maxFile;
        maxSizeReached = sizeAll > maxSizeTotal * 1000000;
        console.log(maxFileReached, maxSizeReached)
        console.log(Math.round(sizeAll/1000000), fileList.files.length+1)
        if (!maxFileReached && !maxSizeReached) {
          fileNumbers.push(fileNumber);
          const fname = middleElipsis(file.name);
          const fsize = file.size;
          $('#videos').append(`
            <div id="${`video${fileNumber}`}" class="video d-flex border py-2 px-3 justify-content-between align-items-center">
              <p class="m-0 p-0 video-title w-50">${fname}</p>
              <p class="m-0 p-0 text-muted">${ bytesToSize(fsize) }</p>
              <button class="btn p-0 m-0 file-remove" data-number="${fileNumber}" onclick="${event => removeFile(event)}">
                <i class='bx bx-x-circle fs-3 m-0 p-0 icons'></i>
              </button>
            </div>
          `);
          fileList.items.add(file);
          fileNumber += 1;
        }
      });

      if (maxFileReached) {
        setTimeout(() => {
          addToast('Jumlah maksimum file tercapai', `
            <span>Beberapa file tidak dapat diupload</span>
          `);
        }, 400);
      }
      if (maxSizeReached) {
        setTimeout(() => {
          addToast('Ukuran maksimum file tercapai', `
            <span>Beberapa file tidak dapat diupload</span>
          `);
        }, 500);
      }

      document.getElementById('selectfile').files = fileList.files;
      $('#allUploaded').removeClass('d-none');
      $('#drop_zone').addClass('d-none');
      $('#btnPicker').html('Select more videos');
      $('#fileUploaded').html(document.getElementById('selectfile').files.length);

      $('.file-remove').click(event => {
        const number = event.target.dataset.number;
        $(`#video${number}`).remove();
        removeFile(parseInt(number));
      });
    }

    function removeFile(number) {
      const i = fileNumbers.indexOf(number);
      fileList.items.remove(i);
      fileNumbers.splice(i, 1);
      document.getElementById('selectfile').files = fileList.files;
      const fileCount = fileList.files.length;
      if (fileCount) {
        $('#fileUploaded').html(fileCount);
      } else {
        $('#allUploaded').addClass('d-none');
        $('#drop_zone').removeClass('d-none');
        $('#btnPicker').html('Select videos');
      }
    }

    $('.file-picker').click(() => {
      /*normal file pick*/
      document.getElementById('selectfile').click();
      document.getElementById('selectfile').onchange = () => {
        const files = document.getElementById('selectfile').files;
        addFiles([...files]);
      };
    });

    $('#compress_btn').click(function(){
      if(!fileList.files.length){
        alert("Please select a file");
        return false;
      }else{
        ajax_file_upload(fileList.files);
      }
    });
  });
  
  function ajax_file_upload(files) {
    const form_data = new FormData();
    [...files].forEach(file => {
      form_data.append('videos[]', file);
    });
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
        // console.log(response);
        // $('#message_info').html(response.message);
        $('#selectfile').val('');
        location.href = response.url
      }
    });
  }

</script>
@endpush