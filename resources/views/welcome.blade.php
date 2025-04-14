<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <!-- Custom Styles (Optional) -->
            <style>
            /* Minimal custom styles */
            body {
                font-family: 'Instrument Sans', sans-serif;
            }
            </style>
    </head>
    <body class="bg-light text-dark">
        <div class="container py-4 py-lg-5">
            <header class="mb-4">
            @if (Route::has('login'))
                    <nav class="d-flex justify-content-end gap-2">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                                class="btn btn-outline-secondary btn-sm"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                                class="btn btn-outline-secondary btn-sm"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                    class="btn btn-primary btn-sm"> <!-- Use primary button style -->
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
            {{-- ... Rest of the welcome page content (needs manual refactoring for Bootstrap) ... --}}
            <main class="row align-items-center">
                 {{-- Content needs refactoring from Tailwind to Bootstrap --}}
                 <div class="col-lg-7 mb-4 mb-lg-0">
                </div>
                 <div class="col-lg-5">
                </div>
            </main>
        </div>

        <!-- Bootstrap JS Bundle (includes Popper) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
