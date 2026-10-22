<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <label for="email">Email</label>
        <br>

        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            autocomplete="username"
            required
        >

        <br><br>

        <label for="password">Password</label>
        <br>

        <input
            id="password"
            type="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>