<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Lte' }} | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
</head>

<body style="background: rgb(229, 229, 229)">

    <div class="container mt-5" style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%)">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6 col-sm-8">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h2 class="text-center mb-4">{{ $title ?? 'Login' }}</h2>
                        <form action="{{ url('/login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="form2Example1" placeholder="john@gmail.com"
                                    class="form-control" value="{{ old('email') }}" />
                                @if (session()->has('msg-error-username'))
                                    <span class="text-danger">{{ session('msg-error-username') }}</span>
                                @endif

                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="form2Example2" name="password" class="form-control" />
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @if (session()->has('msg-error-password'))
                                    <span class="text-danger">{{ session('msg-error-password') }}</span>
                                @endif

                            </div>
                            <div class="">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.min.js"
        integrity="sha384-heAjqF+bCxXpCWLa6Zhcp4fu20XoNIA98ecBC1YkdXhszjoejr5y9Q77hIrv8R9i" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous">
    </script>
</body>

</html>
