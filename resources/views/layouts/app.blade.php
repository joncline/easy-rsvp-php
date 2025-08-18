<!DOCTYPE html>
<html>
<head>
    <title>
        {{ isset($event) && $event->title ? $event->title : 'Easy RSVP' }}
    </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Trix Editor -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>

    <!-- Custom styles -->
    <style>
        #logo {
            height: 30px;
            margin-right: 10px;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container" id="main">
        <nav class="navbar navbar-light mb-2">
            <a href="{{ route('events.new') }}" class="navbar-brand">
                <img src="{{ asset('images/logo-small.png') }}" id="logo" alt="Easy RSVP">
                Easy RSVP
            </a>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('alert'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('alert') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')

        <div class="text-end text-muted mt-5">
            <hr>
            <p class="small">
                Easy RSVP PHP port by
                <a href="https://www.linkedin.com/in/joncline" target="_blank">Jon Cline</a>.
                Originally an
                <a href="https://github.com/KevinBongart/easy-rsvp">open-source</a>
                app made with care by
                <a href="http://kevinbongart.net">Kevin Bongart</a>.
                <a href="https://www.kevinbongart.net/projects/easy-rsvp.html">Learn more</a>.
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Clipboard JS -->
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
    <script>
        new ClipboardJS('.clipboard');
    </script>
</body>
</html>
