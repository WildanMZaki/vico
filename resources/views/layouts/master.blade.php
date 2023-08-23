<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>Compress</title>
    
    @stack('styles')
</head>
<body class="relative">

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow">
            <div class="container">
              <a class="navbar-brand fw-bold" href="#">Vico</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <ul class="navbar-nav">
                  <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Tools
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                      <li><a class="dropdown-item active" href="#">Compress Video</a></li>
                    </ul>
                  </li>
                  
                </ul>
              </div>
            </div>
        </nav>
    </header>

    <main class="bg-light">
        @yield('content')

    </main>
    
    <script src="{{asset('js/jquery-3.6.4.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    @stack('scripts')
</body>
</html>