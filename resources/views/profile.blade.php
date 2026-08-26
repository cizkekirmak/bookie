<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookie - Profile</title>

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
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(225, 232, 213, 0.4);
            border-radius: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #8ec46f;
            border-radius: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #4c7237;
        }
    </style>
</head>
<body>
<div style="font-family: 'Unkempt', cursive; height: 100vh; display: flex; flex-direction: column; background-color: #f7faf5; overflow: hidden; margin: 0;">

    {{-- HEADER --}}
    <header style="
        max-width: 100%;
        margin: 0;
        height: 95px;
        padding: 20px 30px;
        background-color: #d3ea76;
        border: 2px solid #2d5a27;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        align-items: center;
        display: flex;
        gap: 20px;
        box-sizing: border-box;
        overflow: visible !important;
        position: relative;
        z-index: 1000;
        justify-content: space-between;
        flex-direction: row;
    ">     
        <div style="font-family: 'Henny Penny', cursive; font-size: 45px; color: #1f5117; flex-shrink: 0; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); margin-top: 5px;">
            Bookie
        </div>
        
        <div style="display: flex; align-items: center; margin-left: auto; flex-shrink: 0;">
            <!-- Bildirim Zarfı -->
            <div style="margin-right: 20px; display: flex; align-items: center;">
                @include('partials.notifications')
            </div>

            <!-- Ayarlar -->
            <a href="{{ route('ayarlar') }}" style="margin-right: 18px; display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0; transition: transform 0.2s ease, filter 0.2s ease;">
                <img src="{{ asset('images/ayarlar.jpg') }}" alt="ayarlar" style="width: 64px; height: 64px; object-fit: contain; border: 1.5px solid #4b813b; display: block; cursor: pointer;"
                     onmouseenter="this.style.transform='scale(1.1)'; this.style.filter='drop-shadow(0px 6px 8px rgba(0, 0, 0, 0.4))';"
                     onmouseleave="this.style.transform='scale(1)'; this.style.filter='none';">
            </a>
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" style="display: inline-block; line-height: 0; text-decoration: none; flex-shrink: 0; transition: transform 0.2s ease, filter 0.2s ease;">
                <img src="{{ asset('images/dash.jpg') }}" alt="dashboard" style="width: 64px; height: 64px; object-fit: contain; border: 1.5px solid #4b813b; display: block; cursor: pointer;"
                     onmouseenter="this.style.transform='scale(1.1)'; this.style.filter='drop-shadow(0px 6px 8px rgba(0, 0, 0, 0.4))';"
                     onmouseleave="this.style.transform='scale(1)'; this.style.filter='none';">
            </a>
        </div>
    </header>

    {{-- GÖVDE --}}
    <div style="display: flex; flex: 1; overflow: hidden;">

        {{-- SOL SABİT PROFİL PANELİ --}}
        <aside style="width: 320px; background: #8ec46f; border-right: 2px solid #4c7237; padding: 30px 20px; display: flex; flex-direction: column; align-items: center; box-sizing: border-box; flex-shrink: 0; height: 100%;">
            
            {{-- Profil Fotoğrafı / Fide --}}
            <div style="width: 120px; height: 120px; border-radius: 50%; border: 2px solid #2d5a27; background: #eaf3e4; display: flex; justify-content: center; align-items: center; margin-bottom: 0; overflow: hidden; flex-shrink: 0;">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->username }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span style="font-size: 45px;">🌱</span>
                @endif
            </div>

            @php
                $title = $user->reading_title;
            @endphp

            {{-- 1. Kullanıcı Adı (Altındaki boşluğu azaltmak için margin-bottom eklendi) --}}
            <div style="font-family: 'Henny Penny', cursive; font-size: 28px; color: #1a3c11; margin-bottom: 2px;">
                {{ $user->username ?? $user->name }}
            </div>

            {{-- 2. Unvan Rozeti (Üstü isme yakın, altı bio'dan 6px uzak) --}}
            <button type="button" onclick="openTitlesModal()" style="background: none; border: none; padding: 0; cursor: pointer;">
            <div style="
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background-color: {{ $title['bg'] }};
                border: 1.5px solid {{ $title['border'] }};
                color: {{ $title['color'] }};
                padding: 3px 12px;
                border-radius: 16px;
                font-family: 'Unkempt', cursive;
                font-size: 13px;
                font-weight: bold;
                margin-top: 2px;
                margin-bottom: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                cursor: pointer;
            ">
                <span>{{ $title['icon'] }}</span>
                <span>{{ $title['name'] }}</span>
                <span style="font-size: 10px; opacity: 0.6;">▼</span>
            </div>
            </button>

            {{-- 3. Bio Metni --}}
            @if(!empty($user->bio))
                <div style="font-family: 'Unkempt', cursive; font-size: 15px; color: #355e28; margin: 0; line-height: 1.2; text-align: center;">
                    "{{ $user->bio }}"
                </div>
            @endif

            @if(!$isOwnProfile)
                <div style="margin-bottom: 20px; width: 100%; display: flex; justify-content: center;">
                    @if(!$friendship)
                        <form action="{{ route('friends.request', $user->id) }}" method="POST" style="margin: 0; width: 100%;">
                            @csrf
                            <button type="submit" style="width: 100%; background-color: #2d5a27; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 15px;">
                                + Arkadaş Ekle
                            </button>
                        </form>
                    @elseif($friendship->status === 'pending')
                        @if($friendship->user_id === auth()->id())
                            <form action="{{ route('friends.request', $user->id) }}" method="POST" style="margin: 0; width: 100%;">
                                @csrf
                                <button type="submit" style="width: 100%; background-color: #6c757d; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14px;">
                                    İstek Gönderildi (İptal Et)
                                </button>
                            </form>
                        @else
                            <div style="display: flex; gap: 8px; width: 100%;">
                                <form action="{{ route('friends.accept', $user->id) }}" method="POST" style="flex: 1; margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; background-color: #2d5a27; color: white; border: none; padding: 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14px;">Kabul Et</button>
                                </form>
                                <form action="{{ route('friends.reject', $user->id) }}" method="POST" style="flex: 1; margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; background-color: #c62828; color: white; border: none; padding: 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 14px;">Reddet</button>
                                </form>
                            </div>
                        @endif
                    @elseif($friendship->status === 'accepted')
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 6px; width: 100%;">
                            <span style="color: #1a3c11; font-weight: bold; font-size: 15px;">✓ Arkadaşsınız</span>
                            <form action="{{ route('friends.remove', $user->id) }}" method="POST" style="margin: 0; width: 100%;">
                                @csrf
                                <button type="submit" style="width: 100%; background-color: transparent; color: #c62828; border: 1px solid #c62828; padding: 4px 8px; border-radius: 6px; cursor: pointer; font-family: 'Unkempt', cursive; font-size: 12px;">
                                    Arkadaşlıktan Çıkar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Friends Butonu --}}
            <div style="width: 100%; border-top: 1.5px solid #deeaa5; padding-top: 20px; display: flex; justify-content: center;">
                <button 
                    type="button" 
                    onclick="openFriendsModal()" 
                    style="
                        width: 100%;
                        display: flex; 
                        align-items: center; 
                        justify-content: space-between; 
                        background: #deeaa5; 
                        border: 1.5px solid #2d5a27; 
                        border-radius: 12px; 
                        padding: 8px 14px; 
                        cursor: pointer; 
                        font-family: 'Unkempt', cursive; 
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
                        transition: all 0.2s ease;
                    "
                    onmouseenter="this.style.background='#c5f58b'; this.style.transform='translateY(-1px)';"
                    onmouseleave="this.style.background='#deeaa5'; this.style.transform='translateY(0)';"
                >
                    <span style="font-size: 15px; font-weight: bold; color: #1a3c11; display: flex; align-items: center; gap: 6px;">
                        🌱 friends
                    </span>

                    <span style="
                        background: #2d5a27; 
                        color: #ffffff; 
                        font-size: 12px; 
                        font-weight: bold; 
                        padding: 2px 8px; 
                        border-radius: 12px;
                    ">
                        {{ $friendsCount }}
                    </span>
                </button>
            </div>

        </aside>

        {{-- SAĞ İÇERİK ALANI --}}
        <main style="flex: 1; padding: 25px 30px; display: flex; flex-direction: column; overflow: hidden; min-width: 0;">
            
            {{-- ÜST PANEL: BAŞLIK & SWITCH --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-shrink: 0;">
                <h3 style="font-family: 'Henny Penny', cursive; font-size: 24px; color: #1a3c11; margin: 0;">
                    Bookshelf & Reviews
                </h3>

                <div style="display: flex; background: #cae28c; border: 2px solid #737e3d; border-radius: 12px; padding: 4px; gap: 6px;">
                    <button type="button" id="btn-list-view" onclick="switchProfileView('list')" style="border: none; background: #255719; color: #ffffff; padding: 6px 14px; border-radius: 8px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.2s ease;">
                        Book List
                    </button>
                    <button type="button" id="btn-shelf-view" onclick="switchProfileView('shelf')" style="border: none; background: transparent; color: #1a3c11; padding: 6px 14px; border-radius: 8px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.2s ease;">
                        Shelf View
                    </button>
                </div>
            </div>

            {{-- PARTIALS ÇAĞRILARI --}}
            @include('profile.list-view')
            @include('profile.shelf-view')

        </main>

    </div>

</div>

{{-- JAVASCRIPT YÖNETİMİ --}}
<script>
function switchProfileView(mode) {
    const listView = document.getElementById('profile-list-view');
    const shelfView = document.getElementById('profile-shelf-view');
    const btnList = document.getElementById('btn-list-view');
    const btnShelf = document.getElementById('btn-shelf-view');

    if (mode === 'list') {
        listView.style.display = 'flex';
        shelfView.style.display = 'none';
        btnList.style.background = '#255719';
        btnList.style.color = '#ffffff';
        btnShelf.style.background = 'transparent';
        btnShelf.style.color = '#1a3c11';
    } else {
        listView.style.display = 'none';
        shelfView.style.display = 'flex';
        btnShelf.style.background = '#255719';
        btnShelf.style.color = '#ffffff';
        btnList.style.background = 'transparent';
        btnList.style.color = '#1a3c11';
    }
}

function filterStatus(status, clickedBtn) {
    document.querySelectorAll('.status-tab').forEach(btn => {
        btn.style.background = '#eaf3e4';
        btn.style.color = '#1a3c11';
        btn.style.border = '1px solid #737e3d';
    });
    clickedBtn.style.background = '#255719';
    clickedBtn.style.color = '#ffffff';
    clickedBtn.style.border = 'none';

    const cards = document.querySelectorAll('.book-card-item');
    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        if (status === 'all' || cardStatus === status) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

{{-- FRIENDS MODAL --}}
<div 
    id="friendsModal" 
    style="
        display: none; 
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        z-index: 999999; 
        background: rgba(0, 0, 0, 0.45); 
        justify-content: center; 
        align-items: center; 
        backdrop-filter: blur(2px);
    "
>
    <div 
        style="
            background: #f7faf5; 
            border: 2px solid #4c7237; 
            border-radius: 12px; 
            width: 380px; 
            max-width: 90%; 
            max-height: 80vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            animation: popInModal 0.2s ease-out; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        "
    >
        {{-- Modal Başlığı --}}
        <div 
            style="
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                padding: 14px 18px; 
                border-bottom: 2px solid #82b564; 
                background: #c5e8ad;
            "
        >
            <span 
                style="
                    font-family: 'Henny Penny', cursive; 
                    font-size: 18px; 
                    color: #1a3c11;
                "
            >
                🌱 Arkadaşlar ({{ $friendsCount ?? ($user->friends ? $user->friends->count() : 0) }})
            </span>

            <button 
                type="button" 
                onclick="closeFriendsModal()" 
                style="
                    background: transparent; 
                    border: none; 
                    font-size: 20px; 
                    font-weight: bold; 
                    color: #1a3c11; 
                    cursor: pointer; 
                    line-height: 1; 
                    padding: 0 4px;
                "
            >
                ✕
            </button>
        </div>

        {{-- Liste Alanı --}}
        <div 
            class="custom-scroll" 
            style="
                padding: 14px; 
                overflow-y: auto; 
                display: flex; 
                flex-direction: column; 
                gap: 10px; 
                max-height: 380px;
            "
        >
            @php
                $friendsList = $user->friends();
            @endphp

            @forelse($friendsList as $friend)
                <div 
                    onclick="window.location.href='/profile/{{ $friend->id }}'" 
                    style="
                        display: flex; 
                        align-items: center; 
                        justify-content: space-between; 
                        background: #ffffff; 
                        border: 1.5px solid #4c7237; 
                        border-radius: 10px; 
                        padding: 10px 12px; 
                        cursor: pointer; 
                        transition: all 0.15s ease;
                    "
                    onmouseenter="this.style.background='#f1f8ed'; this.style.transform='translateY(-1px)';"
                    onmouseleave="this.style.background='#ffffff'; this.style.transform='translateY(0)';"
                >
                    <div 
                        style="
                            display: flex; 
                            align-items: center; 
                            gap: 10px;
                        "
                    >
                        <div 
                            style="
                                width: 34px; 
                                height: 34px; 
                                border-radius: 50%; 
                                background: #badfa0; 
                                border: 1px solid #4c7237; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                font-size: 15px; 
                                color: #1a3c11; 
                                font-weight: bold; 
                                font-family: 'Unkempt', cursive;
                            "
                        >
                            @if(!empty($friend->avatar))
                                <img src="{{ asset('storage/' . $friend->avatar) }}" 
                                alt="{{ $friend->username ?? 'Profile' }}" 
                                style="width: 100%; 
                                height: 100%; 
                                border-radius: 50%;
                                display: block;
                                object-fit: cover;"
                                onerror="this.onerror=null; this.src='{{ asset('images/profile.jpg') }}';">
                            @else
                                <img src="{{ asset('images/profile.jpg') }}" 
                                alt="Profile" 
                                style="width: 100%; 
                                height: 100%; 
                                border-radius: 50%;
                                display: block;
                                object-fit: cover;">
                            @endif
                        </div>

                        <div>
                            <div 
                                style="
                                    font-family: 'Unkempt', cursive; 
                                    font-size: 15px; 
                                    font-weight: bold; 
                                    color: #1f5117;
                                "
                            >
                                {{ $friend->username ?? $friend->name }}
                            </div>

                            <div 
                                style="
                                    font-family: 'Unkempt', cursive; 
                                    font-size: 11px; 
                                    color: #666;
                                "
                            >
                                {{ \App\Models\UserBook::where('user_id', $friend->id)->whereHas('book')->count() }} kitap
                            </div>
                        </div>
                    </div>

                    <span 
                        style="
                            font-family: 'Unkempt', cursive; 
                            font-size: 12px; 
                            color: #4c7237; 
                            font-weight: bold;
                        "
                    >
                        profili gör →
                    </span>
                </div>
            @empty
                <div 
                    style="
                        text-align: center; 
                        color: #355726; 
                        font-family: 'Unkempt', cursive; 
                        padding: 25px 10px; 
                        font-size: 14px;
                    "
                >
                    Henüz eklenmiş bir arkadaş bulunmuyor. 🌱
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
@keyframes popInModal {
    0% { 
        transform: scale(0.9); 
        opacity: 0; 
    }
    100% { 
        transform: scale(1); 
        opacity: 1; 
    }
}
</style>

<script>
function openFriendsModal() {
    const modal = document.getElementById('friendsModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeFriendsModal() {
    const modal = document.getElementById('friendsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('friendsModal');
    if (e.target === modal) {
        closeFriendsModal();
    }
});
</script>

</body>
</html>
@include('partials.title-modal')