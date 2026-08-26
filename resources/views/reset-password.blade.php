<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Bookie</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Mystery+Quest&family=Unkempt:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: "Mystery Quest" , system-ui;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #D1FFBD;
        margin: 0; 
        
        background-image: url("{{ asset('images/arkaplan.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        }

        .dis-kapsayici {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0px;
        }
        label {
            display: block;
            margin-bottom: -2px;
            color: #333;
            font-size: 17px;
        }
        
        .site-basligi {
            font-family: "Henny Penny", cursive;
            font-size: 100px;
            color: #1a562b;
            margin: 0 0 -20px 0;
            letter-spacing: 2px;
        }

        .kutucuk { 
            background: #ebf8e2; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(44, 159, 76, 0.64); 
            width: 280px;
            font-family: "Henny Penny", cursive; 
        }

        .alt-linkler {
            margin-top: 15px;
            text-align: center;
            font-size: 15px;
        }

        .alt-linkler p {
            margin: 6px 0;
        }

        .alt-linkler a {
            color: #2e6f40;
            text-decoration: none;
            font-family: "Henny Penny", cursive;
            font-weight: none;
        }
        .alt-linkler a:hover {
            text-decoration: underline;
        }

        input {     
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #5f9852;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: Arial, sans-serif; }

        button { 
            width: 250px; 
            padding: 10px; 
            display: block;
            margin: 0 auto;
            background-color: #2e6f40; 
            color: #D1ffbd; 
            border: none; 
            font-size: 16px;
            border-radius: 5px; 
            cursor: pointer;
            font-weight: bold;
            font-family: "Henny Penny", cursive; }

        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <div class="dis-kapsayici">
        <h1 class="site-basligi">Bookie</h1>
        <div class="kutucuk">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">

                <label for="email">email:</label>
                <input type="email" id="email" name="email" value="{{ request('email') }}" required>

                <label for="password">new password:</label>
                <input type="password" id="password" name="password" required>

                <label for="password_confirmation">Confirm New Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                
                @error('email')
                    <small style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; display: block; margin-top: -5px;">
                        {{ $message }}
                    </small>
                @enderror 

                @error('password')
                    <small style="color: #2e6433; font-size: 15px; font-family: 'Unkempt', cursive; display: block; margin-top: -5px;">
                        {{ $message }}
                    </small>
                @enderror

                <button type="submit">Update Password</button>
            </form>
        </div>
    </div>
    
</body>
</html>