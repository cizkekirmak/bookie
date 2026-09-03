<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookie - {{ $user->username ?? 'User' }}'s Board</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mystery+Quest&display=swap" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Unkempt';
            src: url('{{ asset('fonts/Unkempt-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-display: swap;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #badfa0;
            background-image: url('{{ asset('images/giris.png') }}');
            background-size: cover;
            background-position: center top;
            background-attachment: fixed;
            font-family: 'Unkempt', cursive;
            color: #1b3711;
        }

        .board-page-container {
            width: 100%;
            max-width: 1180px;
            margin: 15px auto;
            padding: 0 16px 80px 16px;
            position: relative;
        }

        /* PANO VE YANINDA ŞEFFAF 3x3 ASKI ALANI */
        .corkboard-main-wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 28px;
            width: 100%;
        }

        /* YATAY PANO (16:10) */
        .corkboard-frame {
            flex: 1;
            max-width: 780px;
            width: 100%;
            aspect-ratio: 16 / 10;
            background-color: transparent;
            background-image: url('{{ asset('images/pano.jpg') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            box-shadow: 0 10px 24px rgba(0,0,0,0.18);
            border-radius: 14px;
            overflow: hidden;
        }

        /* PANO ALTINDAKİ MERKEZİ BUTON ALANI */
        .board-bottom-bar {
            display: flex;
            justify-content: center;
            margin-top: 16px;
            width: 100%;
        }

        .btn-action {
            background: #fdf5a6;
            border: 2px solid #5a7d3b;
            border-radius: 14px;
            padding: 8px 26px;
            font-family: 'Unkempt', cursive;
            font-size: 16px;
            font-weight: bold;
            color: #2c441b;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.15s, background-color 0.2s;
        }
        .btn-action:hover {
            transform: scale(1.05);
        }
        .btn-action.saving {
            background: #badfa0;
        }

        /* POST-IT KUTUCUKLARI */
        .cork-postit {
            position: absolute;
            box-shadow: 2px 5px 12px rgba(0,0,0,0.22);
            padding: 8px;
            cursor: grab;
            user-select: none;
            overflow: hidden;
            transition: box-shadow 0.15s;
        }
        .cork-postit:active {
            cursor: grabbing;
            box-shadow: 4px 8px 18px rgba(0,0,0,0.35);
        }
        .cork-postit.square { width: 125px; height: 125px; }
        .cork-postit.rect { width: 125px; height: 165px; }

        .postit-pin {
            position: absolute;
            top: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 11px;
            height: 11px;
            background: #e74c3c;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            border: 1px solid #c0392b;
            z-index: 20;
            pointer-events: none;
        }

        .postit-text-content {
            font-size: 13px;
            line-height: 1.2;
            color: #1a3c11;
            word-wrap: break-word;
            position: absolute;
            user-select: none;
        }

        .postit-sticker-img {
            position: absolute;
            object-fit: contain;
            user-select: none;
        }

        .postit-author {
            position: absolute;
            bottom: 4px;
            left: 6px;
            font-size: 10px;
            color: rgba(0,0,0,0.55);
            pointer-events: none;
            z-index: 10;
        }

        .postit-delete-btn {
            display: none;
            position: absolute;
            top: 3px;
            right: 3px;
            background: #ff5e57;
            color: white;
            border: none;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            z-index: 25;
        }
        .is-editing .postit-delete-btn {
            display: flex;
        }

        /* SAĞDAKİ 3x3 ASKI ALANI */
        .keychain-area-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            background: transparent;
            z-index: 10;
        }

        .keychain-grid-9 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 12px 10px;
            padding: 4px;
            background: transparent;
        }

        .keychain-hook-unit {
            width: 66px;
            height: 92px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .hook-nail {
            width: 8px;
            height: 8px;
            background: radial-gradient(circle, #ffffff 25%, #888888 75%, #333333 100%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.4);
            z-index: 2;
            margin-bottom: -5px;
        }

        .keychain-plush-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform-origin: top center;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.18));
        }
        .keychain-plush-img:hover {
            transform: rotate(8deg) scale(1.08);
        }

        .empty-hook-slot {
            width: 42px;
            height: 42px;
            border: 1.5px dashed rgba(44, 68, 27, 0.22);
            border-radius: 12px;
            margin-top: 14px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .empty-hook-slot:hover {
            background: rgba(255,255,255,0.2);
        }

        /* DOSYA (FOLDER) ALANI */
        .folder-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            margin-top: 2px;
            transition: transform 0.2s;
        }
        .folder-container:hover {
            transform: scale(1.06);
        }
        .folder-img {
            width: 64px;
            height: 52px;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));
        }
        .folder-label {
            font-size: 11px;
            color: #1f5117;
            font-weight: bold;
            text-align: center;
            line-height: 1.1;
            margin-top: 2px;
            text-shadow: 0 1px 2px rgba(255,255,255,0.7);
        }

        /* AKILLI KOLEKSİYON ÇEKMECESİ (Kancaları kapatmayan sol alt popup) */
        .keychain-collection-drawer {
            position: absolute;
            bottom: 40px;
            left: 20px;
            width: 360px;
            max-height: 440px;
            background: #fdfaf3;
            border: 2.5px solid #5a7d3b;
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.25);
            display: none;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
            animation: slideInDrawer 0.25s ease-out;
        }
        .keychain-collection-drawer.active {
            display: flex;
        }

        @keyframes slideInDrawer {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: #eef7e6;
            border-bottom: 2px solid #9dc384;
        }
        .drawer-header h4 {
            margin: 0;
            font-size: 15px;
            color: #1a3c11;
        }
        .drawer-close-btn {
            background: none;
            border: none;
            font-size: 18px;
            font-weight: bold;
            color: #2d5a27;
            cursor: pointer;
            line-height: 1;
        }

        .drawer-body {
            padding: 12px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        /* Çanta İçindeki Tekil Kart */
        .bag-badge-card {
            background: #ffffff;
            border: 1.5px solid #bddbb0;
            border-radius: 12px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 4px;
            position: relative;
            transition: all 0.15s ease;
        }
        .bag-badge-card.unlocked {
            cursor: pointer;
        }
        .bag-badge-card.unlocked:hover {
            border-color: #4b813b;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .bag-badge-card.locked {
            opacity: 0.55;
            filter: grayscale(85%);
            background: #f3f3f3;
            cursor: not-allowed;
        }

        .bag-badge-img {
            width: 46px;
            height: 46px;
            object-fit: contain;
        }
        .bag-badge-title {
            font-size: 12px;
            font-weight: bold;
            color: #1a3c11;
            margin: 0;
            line-height: 1.1;
        }
        .bag-badge-desc {
            font-size: 10px;
            color: #555;
            margin: 0;
            line-height: 1.2;
        }
        .bag-badge-status {
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 8px;
            margin-top: 2px;
        }
        .status-unlocked {
            background: #e1f5d6;
            color: #235c15;
        }
        .status-locked {
            background: #e0e0e0;
            color: #666;
        }

        /* MODAL: POST-IT OLUŞTURUCU MİNİ STÜDYO */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
            z-index: 100000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }

        .studio-modal-box {
            background: #fdfaf3;
            border: 3px solid #5a7d3b;
            border-radius: 20px;
            padding: 20px;
            width: 95%;
            max-width: 560px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        .studio-columns {
            display: flex;
            gap: 18px;
        }
        .studio-tools {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .studio-preview {
            width: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .color-selector {
            display: flex;
            gap: 6px;
        }
        .color-ball {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid rgba(0,0,0,0.15);
            cursor: pointer;
        }
        .color-ball.selected {
            border: 2px solid #1a3c11;
            transform: scale(1.15);
        }

        .studio-interactive-elem {
            cursor: move;
            border: 1px dashed transparent;
        }
        .studio-interactive-elem.is-selected {
            border: 1.5px dashed #2d5a27 !important;
            background-color: rgba(255,255,255,0.2);
        }

        /* MOBİL UYARLAMA */
        @media (max-width: 1024px) {
            .corkboard-main-wrapper {
                flex-direction: column;
                align-items: center;
                gap: 16px;
            }
            .corkboard-frame {
                max-width: 100%;
                border-radius: 10px;
            }
            .keychain-area-wrapper {
                width: 100%;
                margin-top: 6px;
            }
            .keychain-grid-9 {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                width: 100%;
                justify-content: flex-start;
                padding: 10px 6px;
                gap: 12px;
            }
            .keychain-hook-unit {
                width: 56px;
                height: 78px;
                flex-shrink: 0;
            }
            .keychain-collection-drawer {
                position: fixed;
                bottom: 10px;
                left: 10px;
                right: 10px;
                width: auto;
                max-height: 60vh;
            }
            .desktop-only-action {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="board-page-container">

    <!-- 1. ORTADA PANO & SAĞDA ŞEFFAF 3x3 ASKI ALANI -->
    <div class="corkboard-main-wrapper">
        
        <!-- MANTAR PANO (pano.jpg) -->
        <div class="corkboard-frame" id="corkboardArea">
            <div class="cork-postit square" style="top: 20%; left: 12%; background: #fdf5a6; transform: rotate(-2deg);">
                <span class="postit-pin"></span>
                <button class="postit-delete-btn" onclick="this.parentElement.remove()">✕</button>
                <div class="postit-text-content" style="top: 30px; left: 12px; font-size: 14px; transform: rotate(0deg);">
                    Kitaplar harika gidiyor! 📖
                </div>
                <div class="postit-author">@catlover</div>
            </div>
        </div>

        <!-- SAĞDAKİ 9'LU ASKI VE DOSYA (FOLDER) -->
        <div class="keychain-area-wrapper">
            <div class="keychain-grid-9" id="keychainHooksGrid">
                @for($slot = 1; $slot <= 9; $slot++)
                    <div class="keychain-hook-unit" data-slot="{{ $slot }}" onclick="handleHookSlotClick({{ $slot }})">
                        <div class="hook-nail"></div>
                        
                        @if($slot === 1)
                            {{-- Örnek başlangıçta asılı olan maymun --}}
                            <img src="{{ asset('images/badges/maymun.png') }}" 
                                 alt="Monkey Keychain" 
                                 class="keychain-plush-img" 
                                 data-key="maymun"
                                 title="Tıkla ve kaldır/değiştir"
                                 onerror="this.onerror=null; this.src='{{ asset('images/badges/tavsan.png') }}';">
                        @else
                            <div class="empty-hook-slot" title="Boş kanca - Çantadan as"></div>
                        @endif
                    </div>
                @endfor
            </div>

            <!-- DOSYA PNG VE ALT YAZISI (images/dosya.png) -->
            <div class="folder-container" onclick="toggleCollectionDrawer()" title="Open Keychain Bag">
                <img src="{{ asset('images/dosya.png') }}" 
                     alt="All Keychains" 
                     class="folder-img"
                     onerror="this.onerror=null; this.src='{{ asset('images/folder.png') }}';">
                <span class="folder-label">all keychains u own</span>
            </div>
        </div>

    </div>

    <!-- 2. PANONUN TAM ALTINDAKİ MERKEZİ BUTON -->
    <div class="board-bottom-bar">
        @if(auth()->check() && auth()->id() === ($user->id ?? 0))
            <button id="toggleEditBtn" class="btn-action desktop-only-action">✏️ Edit Board</button>
        @elseif(auth()->check())
            <button id="openPostitModalBtn" class="btn-action">📌 ADD POST-IT</button>
        @endif
    </div>

    <!-- 3. KANCALARI GİZLEMEYEN AKILLI KOLEKSİYON ÇEKMECESİ -->
    <div class="keychain-collection-drawer" id="collectionDrawer">
        <div class="drawer-header">
            <h4>🎒 Keychain Bag & Badges</h4>
            <button type="button" class="drawer-close-btn" onclick="toggleCollectionDrawer()">✕</button>
        </div>

        <div class="drawer-body" id="drawerBadgesList">
            {{-- JavaScript dinamik olarak açık ve kilitli 15 başarımı buraya basar --}}
        </div>
    </div>

</div>

<!-- 4. POST-IT MİNİ STÜDYO MODAL -->
<div class="modal-overlay" id="postitStudioModal">
    <div class="studio-modal-box">
        <h3 style="margin: 0; font-size: 17px;">✨ Create Your Note</h3>
        
        <div class="studio-columns">
            <div class="studio-tools">
                <div>
                    <label style="font-size: 13px;">Color:</label>
                    <div class="color-selector">
                        <div class="color-ball selected" style="background:#fdf5a6;" data-c="#fdf5a6"></div>
                        <div class="color-ball" style="background:#ffd1dc;" data-c="#ffd1dc"></div>
                        <div class="color-ball" style="background:#c6e085;" data-c="#c6e085"></div>
                        <div class="color-ball" style="background:#b5e2fa;" data-c="#b5e2fa"></div>
                        <div class="color-ball" style="background:#dfccf1;" data-c="#dfccf1"></div>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px;">Shape:</label>
                    <select id="studioShapeSelect" style="width: 100%; padding: 4px; font-family: 'Unkempt';">
                        <option value="square">Square</option>
                        <option value="rect">Rectangle</option>
                    </select>
                </div>

                <textarea id="studioTextInput" placeholder="Write something cozy..." maxlength="120" style="width:100%; height:50px; font-family:'Unkempt'; padding:6px; resize:none;"></textarea>

                <label style="background:#eef6ea; border:1px solid #5a7d3b; padding:5px; text-align:center; border-radius:6px; font-size:12px; cursor:pointer;">
                    PNG Sticker?
                    <input type="file" id="studioFileInput" accept="image/png" style="display:none;">
                </label>

                <div style="background: #edf5e6; border: 1px solid #7ea863; border-radius: 8px; padding: 6px 10px; display:flex; flex-direction:column; gap:4px;">
                    <div style="font-size: 11px; font-weight: bold; color: #2d5a27;" id="activeControlLabel">Düzenlenen: Metin</div>
                    <label style="font-size: 12px; display:flex; justify-content:space-between;">
                        Boyut: <input type="range" id="studioSizeRange" min="10" max="60" value="14" style="width: 140px;">
                    </label>
                    <label style="font-size: 12px; display:flex; justify-content:space-between;">
                        Döndür: <input type="range" id="studioRotateRange" min="-45" max="45" value="0" style="width: 140px;">
                    </label>
                </div>
            </div>

            <div class="studio-preview">
                <div class="cork-postit square" id="previewPostitBox" style="position:relative; background:#fdf5a6; width:135px; height:135px; overflow:hidden;">
                    <span class="postit-pin"></span>
                    
                    <div id="previewTextLayer" class="postit-text-content studio-interactive-elem is-selected" style="top:25px; left:10px; font-size:14px; transform:rotate(0deg);">
                        Hello!
                    </div>

                    <img id="previewStickerLayer" class="postit-sticker-img studio-interactive-elem" style="display:none; width:45px; height:45px; top:50px; left:40px; transform:rotate(0deg);">

                    <div class="postit-author" id="previewAuthorLabel"></div>
                </div>
                <span style="font-size:10px; color:#666;">*Yazıyı ve sticker'ı sürükleyebilirsin</span>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn-action" style="background:#ddd; padding:6px 16px; font-size:14px;" onclick="closePostitModal()">Cancel</button>
            <button type="button" class="btn-action" style="padding:6px 18px; font-size:14px;" onclick="pinNoteToBoard()">OK!</button>
        </div>
    </div>
</div>

@php
// 15 BAŞARIM VE KAZANILMA KOŞULLARI
function getAllAchievementsList() {
    return [
        'maymun' => ['title' => 'Monkey Reader', 'req' => 'İlk kitap incelemeni yazdığında açılır.', 'icon' => 'badges/maymun.png', 'unlocked' => true],
        'tavsan' => ['title' => '10+ Friends!', 'req' => '10 arkadaş edindiğinde açılır.', 'icon' => 'badges/tavsan.png', 'unlocked' => true],
        'kalp'   => ['title' => 'Top Reviewer', 'req' => 'Yorumların 20+ beğeni aldığında açılır.', 'icon' => 'badges/kalp.png', 'unlocked' => false],
        'kaset'  => ['title' => 'Sci-Fi Explorer', 'req' => '5 bilim kurgu kitabı bitirdiğinde açılır.', 'icon' => 'badges/kaset.png', 'unlocked' => false],
        'kahve'  => ['title' => 'Classic Reader', 'req' => '3 klasik roman bitirdiğinde açılır.', 'icon' => 'badges/kahve.png', 'unlocked' => true],
        'kedi'   => ['title' => 'Night Owl', 'req' => 'Gece 00:00 - 05:00 arası kitap kaydettiğinde açılır.', 'icon' => 'badges/kedi.png', 'unlocked' => false],
        'yildiz' => ['title' => 'Book Worm', 'req' => 'Tek oturuşta 100 sayfa okuduğunda açılır.', 'icon' => 'badges/yildiz.png', 'unlocked' => false],
        'kupa'   => ['title' => 'Speed Reader', 'req' => 'Aynı haftada 2 kitap bitirdiğinde açılır.', 'icon' => 'badges/kupa.png', 'unlocked' => false],
        'kitap'  => ['title' => 'First Step', 'req' => 'İlk kitabını panoya eklediğinde açılır.', 'icon' => 'badges/kitap.png', 'unlocked' => true],
        'mektup' => ['title' => 'Postman', 'req' => 'Arkadaşlarının panolarına 5 post-it astığında açılır.', 'icon' => 'badges/mektup.png', 'unlocked' => false],
        'ayicik' => ['title' => 'Warm Home', 'req' => 'Panona 10 post-it iliştirildiğinde açılır.', 'icon' => 'badges/ayicik.png', 'unlocked' => false],
        'cicek'  => ['title' => 'Spring Blossom', 'req' => 'Profil bilgilerini ve bionu eksiksiz doldur.', 'icon' => 'badges/cicek.png', 'unlocked' => true],
        'mantar' => ['title' => 'Fantasy Lover', 'req' => '5 fantastik kurgu eseri bitir.', 'icon' => 'badges/mantar.png', 'unlocked' => false],
        'kamera' => ['title' => 'Aesthetic Soul', 'req' => 'Özel profil avatarı yüklediğinde açılır.', 'icon' => 'badges/kamera.png', 'unlocked' => false],
        'pasta'  => ['title' => 'Book Birthday', 'req' => 'Bookie üyeliğinde 1. ayını doldur.', 'icon' => 'badges/pasta.png', 'unlocked' => false],
    ];
}
@endphp

<script>
    const CURRENT_USERNAME = @json(auth()->user()->username ?? (auth()->user()->name ?? 'reader'));
    const ACHIEVEMENTS_DATA = @json(getAllAchievementsList());

    // --- KOLEKSİYON ÇEKMECESİ (DOSYA) VE ASKI ETKİLEŞİMİ ---
    const drawer = document.getElementById('collectionDrawer');
    const drawerList = document.getElementById('drawerBadgesList');
    let selectedBadgeToHang = null;

    function toggleCollectionDrawer() {
        drawer.classList.toggle('active');
        if (drawer.classList.contains('active')) {
            renderDrawerAchievements();
        }
    }

    function renderDrawerAchievements() {
        drawerList.innerHTML = '';
        Object.keys(ACHIEVEMENTS_DATA).forEach(key => {
            const item = ACHIEVEMENTS_DATA[key];
            const isUnlocked = item.unlocked;

            const card = document.createElement('div');
            card.className = `bag-badge-card ${isUnlocked ? 'unlocked' : 'locked'}`;

            card.innerHTML = `
                <img src="/images/${item.icon}" class="bag-badge-img" onerror="this.src='{{ asset('images/badges/maymun.png') }}';">
                <p class="bag-badge-title">${item.title}</p>
                <p class="bag-badge-desc">${item.req}</p>
                <span class="bag-badge-status ${isUnlocked ? 'status-unlocked' : 'status-locked'}">
                    ${isUnlocked ? '✓ Açık (As)' : '🔒 Kilitli'}
                </span>
            `;

            if (isUnlocked) {
                card.onclick = () => selectBadgeFromBag(key, item);
            }

            drawerList.appendChild(card);
        });
    }

    // Çantadan anahtarlık seçip kancaya asma akışı
    function selectBadgeFromBag(key, item) {
        // Boş ilk kancayı bul ve tak
        const slots = document.querySelectorAll('.keychain-hook-unit');
        let placed = false;

        for (let slot of slots) {
            const emptySlot = slot.querySelector('.empty-hook-slot');
            if (emptySlot) {
                emptySlot.remove();
                const img = document.createElement('img');
                img.src = `/images/${item.icon}`;
                img.className = 'keychain-plush-img';
                img.dataset.key = key;
                img.title = 'Tıkla ve kaldır';
                img.onerror = function() { this.src = '{{ asset('images/badges/maymun.png') }}'; };
                slot.appendChild(img);
                placed = true;
                break;
            }
        }

        if (!placed) {
            alert('Tüm 9 kanca dolu! Birini kaldırmak için üzerine tıkla.');
        }
    }

    // Kancadaki anahtarlığa tıklandığında kaldırma
    function handleHookSlotClick(slotNum) {
        const slotElem = document.querySelector(`.keychain-hook-unit[data-slot="${slotNum}"]`);
        const existingImg = slotElem.querySelector('.keychain-plush-img');

        if (existingImg) {
            if (confirm('Bu anahtarlığı kancadan çıkarıp çantana geri koymak istiyor musun?')) {
                existingImg.remove();
                const emptyBox = document.createElement('div');
                emptyBox.className = 'empty-hook-slot';
                emptyBox.title = 'Boş kanca - Çantadan as';
                slotElem.appendChild(emptyBox);
            }
        } else {
            // Boş kancaya tıklandıysa çantayı aç
            if (!drawer.classList.contains('active')) {
                toggleCollectionDrawer();
            }
        }
    }

    // --- DÜZENLEME MODU (PANO DRAG & DROP) ---
    let isEditing = false;
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const corkboard = document.getElementById('corkboardArea');

    if (toggleEditBtn) {
        toggleEditBtn.addEventListener('click', function() {
            isEditing = !isEditing;
            if (isEditing) {
                this.innerText = '💾 SAVE';
                this.classList.add('saving');
                corkboard.classList.add('is-editing');
                enableDragging();
            } else {
                this.innerText = '✏️ Edit Board';
                this.classList.remove('saving');
                corkboard.classList.remove('is-editing');
                disableDragging();
            }
        });
    }

    let activeDragItem = null;
    let dragOffsetX = 0, dragOffsetY = 0;

    function enableDragging() {
        document.querySelectorAll('.cork-postit').forEach(item => {
            item.onmousedown = startBoardDrag;
        });
    }

    function disableDragging() {
        document.querySelectorAll('.cork-postit').forEach(item => {
            item.onmousedown = null;
        });
    }

    function startBoardDrag(e) {
        if (!isEditing) return;
        activeDragItem = this;
        const rect = activeDragItem.getBoundingClientRect();
        dragOffsetX = e.clientX - rect.left;
        dragOffsetY = e.clientY - rect.top;
        document.onmousemove = doBoardDrag;
        document.onmouseup = stopBoardDrag;
    }

    function doBoardDrag(e) {
        if (!activeDragItem) return;
        const boardRect = corkboard.getBoundingClientRect();
        let left = e.clientX - boardRect.left - dragOffsetX;
        let top = e.clientY - boardRect.top - dragOffsetY;

        left = Math.max(0, Math.min(boardRect.width - activeDragItem.offsetWidth, left));
        top = Math.max(0, Math.min(boardRect.height - activeDragItem.offsetHeight, top));

        activeDragItem.style.left = (left / boardRect.width * 100) + '%';
        activeDragItem.style.top = (top / boardRect.height * 100) + '%';
    }

    function stopBoardDrag() {
        activeDragItem = null;
        document.onmousemove = null;
        document.onmouseup = null;
    }

    // --- POST-IT STÜDYO ETKİLEŞİMİ ---
    const modal = document.getElementById('postitStudioModal');
    const openModalBtn = document.getElementById('openPostitModalBtn');
    const textInput = document.getElementById('studioTextInput');
    const sizeRange = document.getElementById('studioSizeRange');
    const rotateRange = document.getElementById('studioRotateRange');
    const shapeSelect = document.getElementById('studioShapeSelect');
    const fileInput = document.getElementById('studioFileInput');
    const activeControlLabel = document.getElementById('activeControlLabel');

    const previewBox = document.getElementById('previewPostitBox');
    const previewText = document.getElementById('previewTextLayer');
    const previewSticker = document.getElementById('previewStickerLayer');
    const previewAuthor = document.getElementById('previewAuthorLabel');

    let selectedColor = '#fdf5a6';
    let loadedStickerSrc = null;
    let activeTarget = 'text';

    let textState = { size: 14, rotate: 0, top: 25, left: 10 };
    let stickerState = { size: 45, rotate: 0, top: 50, left: 40 };

    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            previewAuthor.innerText = '@' + CURRENT_USERNAME;
            modal.classList.add('active');
        });
    }

    function closePostitModal() {
        modal.classList.remove('active');
    }

    document.querySelectorAll('.color-ball').forEach(ball => {
        ball.addEventListener('click', function() {
            document.querySelectorAll('.color-ball').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            selectedColor = this.dataset.c;
            previewBox.style.backgroundColor = selectedColor;
        });
    });

    shapeSelect.addEventListener('change', () => {
        previewBox.className = 'cork-postit ' + shapeSelect.value;
    });

    textInput.addEventListener('input', () => {
        previewText.innerText = textInput.value || 'Hello!';
    });

    function selectTarget(target) {
        activeTarget = target;
        if (target === 'text') {
            previewText.classList.add('is-selected');
            previewSticker.classList.remove('is-selected');
            activeControlLabel.innerText = 'Düzenlenen: Metin';
            sizeRange.min = 10;
            sizeRange.max = 30;
            sizeRange.value = textState.size;
            rotateRange.value = textState.rotate;
        } else {
            previewSticker.classList.add('is-selected');
            previewText.classList.remove('is-selected');
            activeControlLabel.innerText = 'Düzenlenen: Sticker';
            sizeRange.min = 20;
            sizeRange.max = 85;
            sizeRange.value = stickerState.size;
            rotateRange.value = stickerState.rotate;
        }
    }

    previewText.addEventListener('mousedown', (e) => { e.stopPropagation(); selectTarget('text'); startStudioDrag(e, previewText, textState); });
    previewSticker.addEventListener('mousedown', (e) => { e.stopPropagation(); selectTarget('sticker'); startStudioDrag(e, previewSticker, stickerState); });

    sizeRange.addEventListener('input', () => {
        if (activeTarget === 'text') {
            textState.size = sizeRange.value;
            previewText.style.fontSize = textState.size + 'px';
        } else {
            stickerState.size = sizeRange.value;
            previewSticker.style.width = stickerState.size + 'px';
            previewSticker.style.height = stickerState.size + 'px';
        }
    });

    rotateRange.addEventListener('input', () => {
        if (activeTarget === 'text') {
            textState.rotate = rotateRange.value;
            previewText.style.transform = `rotate(${textState.rotate}deg)`;
        } else {
            stickerState.rotate = rotateRange.value;
            previewSticker.style.transform = `rotate(${stickerState.rotate}deg)`;
        }
    });

    function startStudioDrag(e, elem, stateObj) {
        e.preventDefault();
        const startX = e.clientX;
        const startY = e.clientY;
        const initLeft = elem.offsetLeft;
        const initTop = elem.offsetTop;

        function onMouseMove(event) {
            const dx = event.clientX - startX;
            const dy = event.clientY - startY;
            let nLeft = initLeft + dx;
            let nTop = initTop + dy;

            nLeft = Math.max(0, Math.min(previewBox.offsetWidth - elem.offsetWidth, nLeft));
            nTop = Math.max(12, Math.min(previewBox.offsetHeight - elem.offsetHeight, nTop));

            elem.style.left = nLeft + 'px';
            elem.style.top = nTop + 'px';
            stateObj.left = nLeft;
            stateObj.top = nTop;
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    }

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.type === 'image/png') {
            const reader = new FileReader();
            reader.onload = (e) => {
                loadedStickerSrc = e.target.result;
                previewSticker.src = loadedStickerSrc;
                previewSticker.style.display = 'block';
                selectTarget('sticker');
            };
            reader.readAsDataURL(file);
        }
    });

    function pinNoteToBoard() {
        const shape = shapeSelect.value;
        const text = textInput.value.trim() || 'Cozy note!';

        const newNote = document.createElement('div');
        newNote.className = `cork-postit ${shape}`;
        newNote.style.backgroundColor = selectedColor;
        newNote.style.top = '25%';
        newNote.style.left = '35%';
        newNote.style.transform = `rotate(${Math.floor(Math.random() * 8 - 4)}deg)`;

        let stickerHtml = loadedStickerSrc ? `
            <img src="${loadedStickerSrc}" class="postit-sticker-img" 
                 style="top:${stickerState.top}px; left:${stickerState.left}px; width:${stickerState.size}px; height:${stickerState.size}px; transform:rotate(${stickerState.rotate}deg);">
        ` : '';

        newNote.innerHTML = `
            <span class="postit-pin"></span>
            <button class="postit-delete-btn" onclick="this.parentElement.remove()">✕</button>
            <div class="postit-text-content" style="top:${textState.top}px; left:${textState.left}px; font-size:${textState.size}px; transform:rotate(${textState.rotate}deg);">
                ${text}
            </div>
            ${stickerHtml}
            <div class="postit-author">@${CURRENT_USERNAME}</div>
        `;

        corkboard.appendChild(newNote);
        closePostitModal();

        textInput.value = '';
        loadedStickerSrc = null;
        previewSticker.style.display = 'none';
        previewText.innerText = 'Hello!';
        selectTarget('text');
    }
</script>

</body>
</html>