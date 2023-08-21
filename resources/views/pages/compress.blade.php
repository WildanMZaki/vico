<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>Compress</title>
</head>
<body>

    <main class="container">
      <div>
        <form action="{{route('compress')}}" enctype="multipart/form-data" method="POST">
          @csrf
          <div class="mb-3">
            <label for="video" class="form-label">Select your video</label>
            <input type="file" name="video" id="video" class="form-control">
          </div>
          <div class="mb-3">
            <button type="submit" class="btn btn-primary">Compress</button>
          </div>
        </form>
      </div>
    </main>


    <script src="{{asset('js/app.js')}}"></script>
</body>
</html>