<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookie - Ayarlar</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Mystery+Quest&family=Unkempt:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-image: url('{{ asset('images/giris.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            font-family: 'Unkempt', cursive;
        }

        .settings-card {
            background: #badfa0;
            border: 2px solid #82b564;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .settings-input {
            width: 100%;
            border: 1.5px solid #4c7237;
            border-radius: 8px;
            padding: 10px 12px;
            font-family: 'Unkempt', cursive;
            font-size: 15px;
            color: #1a3c11;
            background: #ffffff;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .settings-input:focus {
            border-color: #1f5117;
            box-shadow: 0 0 5px rgba(45, 90, 39, 0.3);
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <header 
        style="
            max-width: 100%;
            margin: 0;
            height: 95px;
            padding: 20px 30px;
            background-color: #d3ea76;
            border-bottom: 2px solid #2d5a27;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            align-items: center;
            display: flex;
            gap: 20px;
            box-sizing: border-box;
            position: relative;
            z-index: 1000;
            justify-content: space-between;
        "
    >     
        <div 
            style="
                font-family: 'Henny Penny', cursive; 
                font-size: 45px; 
                color: #1f5117; 
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); 
                margin-top: 5px;
            "
        >
            Bookie
        </div>
        
        <div 
            style="
                display: flex; 
                align-items: center; 
                margin-left: auto; 
                gap: 16px;
            "
        >
            {{-- Dashboard --}}
            <a 
                href="{{ route('dashboard') }}" 
                style="
                    display: inline-block; 
                    line-height: 0; 
                    text-decoration: none; 
                    transition: transform 0.2s ease;
                "
                onmouseenter="this.style.transform='scale(1.1)'"
                onmouseleave="this.style.transform='scale(1)'"
            >
                <img 
                    src="{{ asset('images/dash.jpg') }}" 
                    alt="dashboard" 
                    style="
                        width: 60px; 
                        height: 60px; 
                        object-fit: contain; 
                        border: 1.5px solid #4b813b; 
                        display: block; 
                        cursor: pointer;
                    "
                >
            </a>

            {{-- Profil --}}
            <a 
                href="{{ route('profile') }}" 
                style="
                    display: inline-block; 
                    line-height: 0; 
                    text-decoration: none; 
                    transition: transform 0.2s ease;
                "
                onmouseenter="this.style.transform='scale(1.1)'"
                onmouseleave="this.style.transform='scale(1)'"
            >
                <img 
                    src="{{ asset('images/profile.jpg') }}" 
                    alt="profile" 
                    style="
                        width: 60px; 
                        height: 60px; 
                        object-fit: contain; 
                        border: 1.5px solid #4b813b; 
                        display: block; 
                        cursor: pointer;
                    "
                >
            </a>
        </div>
    </header>

    {{-- BAŞARI BİLDİRİMİ --}}
    @if(session('success'))
        <div 
            id="success-toast" 
            style="
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 9999;
                background-color: #d4edda;
                color: #155724;
                border: 1.5px solid #c3e6cb;
                padding: 10px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                font-size: 15px;
                transition: opacity 0.5s ease;
            "
        >
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById("success-toast");
                if (toast) {
                    toast.style.opacity = "0";
                    setTimeout(() => toast.remove(), 500);
                }
            }, 2500);
        </script>
    @endif

    {{-- ANA İÇERİK FORMU --}}
    <main 
        style="
            max-width: 550px; 
            margin: 40px auto; 
            padding: 0 20px;
        "
    >
        <div 
            class="settings-card" 
            style="padding: 30px;"
        >
            <h2 
                style="
                    font-family: 'Henny Penny', cursive; 
                    font-size: 26px; 
                    color: #1a3c11; 
                    margin: 0 0 24px 0; 
                    text-align: center;
                "
            >
                Profile settings
            </h2>

            <form 
                action="{{ route('profile.update') }}" 
                method="POST" 
                enctype="multipart/form-data" 
                style="
                    display: flex; 
                    flex-direction: column; 
                    gap: 20px;
                "
            >
                @csrf

                {{-- AVATAR ALANI & CANLI ÖNİZLEME --}}
                <div 
                    style="
                        display: flex; 
                        flex-direction: column; 
                        align-items: center; 
                        gap: 12px;
                    "
                >
                    <div 
                        id="avatar-preview-box" 
                        style="
                            width: 110px; 
                            height: 110px; 
                            border-radius: 50%; 
                            border: 2px solid #2d5a27; 
                            background: #eaf3e4; 
                            display: flex; 
                            justify-content: center; 
                            align-items: center; 
                            overflow: hidden;
                        "
                    >
                        @if(auth()->user()->avatar)
                            <img 
                                id="avatar-preview-img" 
                                src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                alt="Avatar" 
                                style="width: 100%; height: 100%; object-fit: cover;"
                            >
                        @else
                            <span id="avatar-default-icon" style="font-size: 42px;">🌱</span>
                            <img 
                                id="avatar-preview-img" 
                                src="" 
                                alt="Avatar" 
                                style="width: 100%; height: 100%; object-fit: cover; display: none;"
                            >
                        @endif
                    </div>

                    <label 
                        for="avatarInput" 
                        style="
                            background: #f1f8ed; 
                            border: 1.5px solid #4c7237; 
                            color: #1f5117; 
                            padding: 6px 14px; 
                            border-radius: 18px; 
                            cursor: pointer; 
                            font-size: 13px; 
                            font-weight: bold; 
                            transition: all 0.2s ease;
                        "
                        onmouseenter="this.style.background='#ffffff'"
                        onmouseleave="this.style.background='#f1f8ed'"
                    >
                        choose your best pic !
                    </label>
                    <input 
                        type="file" 
                        id="avatarInput" 
                        name="avatar" 
                        accept="image/*" 
                        style="display: none;" 
                        onchange="previewSelectedImage(this)"
                    >
                    @error('avatar')
                        <span style="color: #c62828; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- BİYOGRAFİ (BIO) ALANI --}}
                <div 
                    style="
                        display: flex; 
                        flex-direction: column; 
                        gap: 6px;
                    "
                >
                    <div 
                        style="
                            display: flex; 
                            justify-content: space-between; 
                            align-items: center;
                        "
                    >
                        <label 
                            for="bioText" 
                            style="
                                font-size: 15px; 
                                font-weight: bold; 
                                color: #1a3c11;
                            "
                        >
                            About you (bio)
                        </label>
                        <span 
                            id="bioCharCount" 
                            style="font-size: 12px; color: #527943;"
                        >
                            {{ strlen(old('bio', auth()->user()->bio ?? '')) }}/160
                        </span>
                    </div>

                    <textarea 
                        id="bioText" 
                        name="bio" 
                        rows="3" 
                        maxlength="160" 
                        class="settings-input" 
                        placeholder="Tell us about yourself..." 
                        style="resize: none;"
                        oninput="updateCharCount(this)"
                    >{{ old('bio', auth()->user()->bio) }}</textarea>
                    @error('bio')
                        <span style="color: #c62828; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- KAYDET BUTONU --}}
                <button 
                    type="submit" 
                    style="
                        background: #2d5a27; 
                        color: #ffffff; 
                        border: none; 
                        padding: 12px 20px; 
                        border-radius: 10px; 
                        font-family: 'Unkempt', cursive; 
                        font-size: 16px; 
                        font-weight: bold; 
                        cursor: pointer; 
                        transition: background-color 0.2s ease, transform 0.1s ease; 
                        margin-top: 8px;
                    "
                    onmouseenter="this.style.background='#1f4219'; this.style.transform='translateY(-1px)';"
                    onmouseleave="this.style.background='#2d5a27'; this.style.transform='translateY(0)';"
                >
                    save 
                </button>
            </form>
        </div>
    </main>

    {{-- JAVASCRIPT: ÖNİZLEME & SAYAÇ --}}
    <script>
        function previewSelectedImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('avatar-preview-img');
                    const defaultIcon = document.getElementById('avatar-default-icon');

                    img.src = e.target.result;
                    img.style.display = 'block';
                    if (defaultIcon) {
                        defaultIcon.style.display = 'none';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updateCharCount(textarea) {
            const count = textarea.value.length;
            document.getElementById('bioCharCount').innerText = `${count}/160`;
        }
    </script>
</body>
</html>