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

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

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

        .corkboard-main-wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 28px;
            width: 100%;
        }

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
        .btn-action:hover { transform: scale(1.05); }
        .btn-action.saving { background: #badfa0; }

        /* POST-IT BOYUTLARI */
        .cork-postit {
            position: absolute;
            box-shadow: 2px 5px 12px rgba(0,0,0,0.22);
            padding: 8px;
            cursor: grab;
            user-select: none;
            overflow: hidden;
            transition: box-shadow 0.15s;
            touch-action: none;
        }
        .cork-postit:active { cursor: grabbing; box-shadow: 4px 8px 18px rgba(0,0,0,0.35); }
        .cork-postit.size-square { width: 130px; height: 130px; }
        .cork-postit.size-portrait { width: 120px; height: 165px; }
        .cork-postit.size-landscape { width: 165px; height: 120px; }

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

        #corkboardArea .postit-text-content {
            font-size: 14px;
            line-height: 1.2;
            color: #1a3c11;
            word-wrap: break-word;
            position: absolute;
            user-select: none;
            pointer-events: none !important;
            transform-origin: center center;
        }

        #corkboardArea .postit-sticker-img {
            position: absolute;
            object-fit: contain;
            user-select: none;
            -webkit-user-drag: none !important;
            pointer-events: none !important;
            transform-origin: center center;
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
            pointer-events: auto;
        }
        .is-editing .postit-delete-btn,
        .can-delete .postit-delete-btn { display: flex; }

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
            pointer-events: auto !important;
        }
        .keychain-plush-img:hover { transform: rotate(8deg) scale(1.08); }

        .empty-hook-slot {
            width: 42px;
            height: 42px;
            border: 1.5px dashed rgba(44, 68, 27, 0.22);
            border-radius: 12px;
            margin-top: 14px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .empty-hook-slot:hover { background: rgba(255,255,255,0.2); }

        .folder-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            margin-top: 2px;
            transition: transform 0.2s;
        }
        .folder-container:hover { transform: scale(1.06); }
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
        }

        /* KOLEKSİYON ÇEKMECESİ */
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
        .keychain-collection-drawer.active { display: flex; }

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
        .drawer-header h4 { margin: 0; font-size: 15px; color: #1a3c11; }
        .drawer-close-btn { background: none; border: none; font-size: 18px; font-weight: bold; color: #2d5a27; cursor: pointer; }

        .drawer-body {
            padding: 12px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

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
        }
        .bag-badge-card.unlocked { cursor: pointer; }
        .bag-badge-card.locked { opacity: 0.55; filter: grayscale(85%); background: #f3f3f3; cursor: not-allowed; }

        .bag-badge-img { width: 46px; height: 46px; object-fit: contain; }
        .bag-badge-title { font-size: 12px; font-weight: bold; color: #1a3c11; margin: 0; }
        .bag-badge-desc { font-size: 10px; color: #555; margin: 0; }
        .bag-badge-status { font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 8px; }
        .status-unlocked { background: #e1f5d6; color: #235c15; }
        .status-locked { background: #e0e0e0; color: #666; }

        /* POST-IT OLUŞTURUCU MODAL & KONTROLLER */
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
        .modal-overlay.active { display: flex; }

        .studio-modal-box {
            background: #fdfaf3;
            border: 3px solid #5a7d3b;
            border-radius: 20px;
            padding: 20px;
            width: 95%;
            max-width: 580px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        .studio-columns { display: flex; gap: 18px; }
        .studio-tools { flex: 1; display: flex; flex-direction: column; gap: 10px; }
        .studio-preview { width: 190px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; }

        .shape-btn-group { display: flex; gap: 6px; width: 100%; }
        .shape-btn {
            flex: 1;
            background: #ffffff;
            border: 1.5px solid #7ea863;
            border-radius: 8px;
            padding: 5px 2px;
            font-family: 'Unkempt', cursive;
            font-size: 12px;
            font-weight: bold;
            color: #1a3c11;
            cursor: pointer;
        }
        .shape-btn.active { background: #2d5a27; color: #ffffff; border-color: #2d5a27; }

        .color-selector { display: flex; gap: 6px; }
        .color-ball { width: 24px; height: 24px; border-radius: 50%; border: 2px solid rgba(0,0,0,0.15); cursor: pointer; }
        .color-ball.selected { border: 2px solid #1a3c11; transform: scale(1.15); }

        .transform-box {
            position: absolute;
            cursor: move;
            border: 1.5px dashed transparent;
            touch-action: none;
        }
        .transform-box.is-selected { border-color: #2d5a27; }

        .handle-btn {
            display: none;
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            z-index: 30;
        }
        .transform-box.is-selected .handle-btn { display: flex; align-items: center; justify-content: center; }

        .handle-delete { top: -8px; left: -8px; background: #e74c3c; color: white; font-size: 10px; cursor: pointer; }
        .handle-rotate { top: -14px; left: 50%; transform: translateX(-50%); background: #f39c12; color: white; font-size: 10px; cursor: grab; }
        .handle-resize { bottom: -8px; right: -8px; background: #3498db; color: white; font-size: 10px; cursor: nwse-resize; }

        @media (max-width: 1024px) {
            .corkboard-main-wrapper { flex-direction: column; align-items: center; gap: 16px; }
            .corkboard-frame { max-width: 100%; border-radius: 10px; }
            .keychain-area-wrapper { width: 100%; margin-top: 6px; }
            .keychain-grid-9 { display: flex; flex-direction: row; overflow-x: auto; width: 100%; justify-content: flex-start; padding: 10px 6px; gap: 12px; }
            .keychain-hook-unit { width: 56px; height: 78px; flex-shrink: 0; }
            .keychain-collection-drawer { position: fixed; bottom: 10px; left: 10px; right: 10px; width: auto; max-height: 60vh; }
            .desktop-only-action { display: none !important; }
        }
    </style>
</head>
<body>

@php
    $isOwnProfile = auth()->check() && (auth()->id() === ($user->id ?? 0));
@endphp

<div class="board-page-container">

    <div class="corkboard-main-wrapper">
        <div class="corkboard-frame" id="corkboardArea"></div>

        <div class="keychain-area-wrapper">
            <div class="keychain-grid-9" id="keychainHooksGrid">
                @for($slot = 1; $slot <= 9; $slot++)
                    <div class="keychain-hook-unit" data-slot="{{ $slot }}" onclick="handleHookSlotClick({{ $slot }})">
                        <div class="hook-nail"></div>
                        <div class="empty-hook-slot" title="{{ $isOwnProfile ? 'Boş kanca - Çantadan as' : '' }}"></div>
                    </div>
                @endfor
            </div>

            <div class="folder-container" @if($isOwnProfile) onclick="toggleCollectionDrawer()" @endif title="All Keychains">
                <img src="{{ asset('images/dosya.png') }}" alt="Folder" class="folder-img" onerror="this.onerror=null; this.src='{{ asset('images/folder.png') }}';">
                <span class="folder-label">all keychains u own</span>
            </div>
        </div>
    </div>

    <div class="board-bottom-bar">
        @if($isOwnProfile)
            <button id="toggleEditBtn" class="btn-action desktop-only-action">✏️ Edit Board</button>
        @elseif(auth()->check())
            <button id="openPostitModalBtn" class="btn-action">📌 ADD POST-IT</button>
        @endif
    </div>

    @if($isOwnProfile)
    <div class="keychain-collection-drawer" id="collectionDrawer">
        <div class="drawer-header">
            <h4>🎒 Keychain Bag & Badges</h4>
            <button type="button" class="drawer-close-btn" onclick="toggleCollectionDrawer()">✕</button>
        </div>
        <div class="drawer-body" id="drawerBadgesList"></div>
    </div>
    @endif

</div>

<!-- POST-IT MİNİ STÜDYO MODAL -->
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
                    <div class="shape-btn-group">
                        <button type="button" class="shape-btn active" data-shape="size-square">Square</button>
                        <button type="button" class="shape-btn" data-shape="size-portrait">Portrait</button>
                        <button type="button" class="shape-btn" data-shape="size-landscape">Landscape</button>
                    </div>
                </div>

                <textarea id="studioTextInput" placeholder="Write something cozy..." maxlength="120" style="width:100%; height:50px; font-family:'Unkempt'; padding:6px; resize:none;"></textarea>

                <label style="background:#eef6ea; border:1px solid #5a7d3b; padding:6px; text-align:center; border-radius:6px; font-size:12px; cursor:pointer;">
                    PNG Sticker Ekle
                    <input type="file" id="studioFileInput" accept="image/png" style="display:none;">
                </label>
            </div>

            <div class="studio-preview">
                <div class="cork-postit size-square" id="previewPostitBox" style="position:relative; background:#fdf5a6; overflow:hidden;">
                    <span class="postit-pin"></span>
                    
                    <div id="textTransformBox" class="transform-box is-selected" style="top:25px; left:12px; width:100px; height:auto;">
                        <div class="postit-text-content" id="previewTextLayer" style="position:relative; width:100%; font-size:14px;">Hello!</div>
                        <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                        <div class="handle-btn handle-resize" title="Büyüt">⤡</div>
                    </div>

                    <div id="stickerTransformBox" class="transform-box" style="display:none; top:45px; left:35px; width:55px; height:55px;">
                        <img id="previewStickerLayer" class="postit-sticker-img" style="width:100%; height:100%; position:relative;">
                        <div class="handle-btn handle-delete" id="btnDeleteSticker" title="Sil">✕</div>
                        <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                        <div class="handle-btn handle-resize" title="Büyüt">⤡</div>
                    </div>

                    <div class="postit-author" id="previewAuthorLabel"></div>
                </div>
                <span style="font-size:10px; color:#666;">*Köşelerden tutup döndür / büyüt</span>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn-action" style="background:#ddd; padding:6px 16px; font-size:14px;" onclick="closePostitModal()">Cancel</button>
            <button type="button" class="btn-action" style="padding:6px 18px; font-size:14px;" onclick="pinNoteToBoard()">OK!</button>
        </div>
    </div>
</div>

@php
function getAllAchievementsList() {
    return [
        'maymun' => ['title' => 'Monkey Reader', 'req' => 'İlk kitap incelemeni yazdığında açılır.', 'icon' => asset('images/badges/maymun.png'), 'unlocked' => true],
        'tavsan' => ['title' => '10+ Friends!', 'req' => '10 arkadaş edindiğinde açılır.', 'icon' => asset('images/badges/tavsan.png'), 'unlocked' => true],
        'kalp'   => ['title' => 'Top Reviewer', 'req' => 'Yorumların 20+ beğeni aldığında açılır.', 'icon' => asset('images/badges/kalp.png'), 'unlocked' => false],
        'kaset'  => ['title' => 'Sci-Fi Explorer', 'req' => '5 bilim kurgu kitabı bitirdiğinde açılır.', 'icon' => asset('images/badges/kaset.png'), 'unlocked' => false],
        'kahve'  => ['title' => 'Classic Reader', 'req' => '3 klasik roman bitirdiğinde açılır.', 'icon' => asset('images/badges/kahve.png'), 'unlocked' => true],
        'kedi'   => ['title' => 'Night Owl', 'req' => 'Gece 00:00 - 05:00 arası kitap kaydettiğinde açılır.', 'icon' => asset('images/badges/kedi.png'), 'unlocked' => false],
        'yildiz' => ['title' => 'Book Worm', 'req' => 'Tek oturuşta 100 sayfa okuduğunda açılır.', 'icon' => asset('images/badges/yildiz.png'), 'unlocked' => false],
        'kupa'   => ['title' => 'Speed Reader', 'req' => 'Aynı haftada 2 kitap bitirdiğinde açılır.', 'icon' => asset('images/badges/kupa.png'), 'unlocked' => false],
        'kitap'  => ['title' => 'First Step', 'req' => 'İlk kitabını panoya eklediğinde açılır.', 'icon' => asset('images/badges/kitap.png'), 'unlocked' => true],
        'mektup' => ['title' => 'Postman', 'req' => 'Arkadaşlarının panolarına 5 post-it astığında açılır.', 'icon' => asset('images/badges/mektup.png'), 'unlocked' => false],
        'ayicik' => ['title' => 'Warm Home', 'req' => 'Panona 10 post-it iliştirildiğinde açılır.', 'icon' => asset('images/badges/ayicik.png'), 'unlocked' => false],
        'cicek'  => ['title' => 'Spring Blossom', 'req' => 'Profil bilgilerini ve bionu eksiksiz doldur.', 'icon' => asset('images/badges/cicek.png'), 'unlocked' => true],
        'mantar' => ['title' => 'Fantasy Lover', 'req' => '5 fantastik kurgu eseri bitir.', 'icon' => asset('images/badges/mantar.png'), 'unlocked' => false],
        'kamera' => ['title' => 'Aesthetic Soul', 'req' => 'Özel profil avatarı yüklediğinde açılır.', 'icon' => asset('images/badges/kamera.png'), 'unlocked' => false],
        'pasta'  => ['title' => 'Book Birthday', 'req' => 'Bookie üyeliğinde 1. ayını doldur.', 'icon' => asset('images/badges/pasta.png'), 'unlocked' => false],
        'anahtar'=> ['title' => 'Secret Keeper', 'req' => 'Gizli easter egg’leri bulan meraklı dedektif anahtarı.', 'icon' => asset('images/badges/anahtar.png'), 'unlocked' => false],
    ];
}
@endphp

<script>
    const CURRENT_USERNAME = @json(auth()->user()->username ?? (auth()->user()->name ?? 'reader'));
    const IS_OWN_PROFILE = @json($isOwnProfile);
    const PROFILE_USER_ID = @json($user->id ?? 0);
    const ACHIEVEMENTS_DATA = @json(getAllAchievementsList());
    const FALLBACK_BADGE_SVG = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="18" r="8" fill="none" stroke="%23888" stroke-width="4"/><rect x="25" y="32" width="50" height="56" rx="14" fill="%23badfa0" stroke="%234b813b" stroke-width="3"/><circle cx="42" cy="54" r="4" fill="%232d5a27"/><circle cx="58" cy="54" r="4" fill="%232d5a27"/><path d="M 45 64 Q 50 68 55 64" fill="none" stroke="%232d5a27" stroke-width="3" stroke-linecap="round"/></svg>`;

    const corkboard = document.getElementById('corkboardArea');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    let isEditing = false;

    // --- LOCALSTORAGE KALICILIK ---
    const STORAGE_KEY_NOTES = `bookie_board_notes_${PROFILE_USER_ID}`;
    const STORAGE_KEY_KEYCHAINS = `bookie_board_keychains_${PROFILE_USER_ID}`;

    function saveBoardToStorage() {
        const notes = [];
        document.querySelectorAll('#corkboardArea .cork-postit').forEach(item => {
            const authorText = item.querySelector('.postit-author') ? item.querySelector('.postit-author').innerText : '';
            const textElem = item.querySelector('.postit-text-content');
            const fontSize = textElem ? window.getComputedStyle(textElem).fontSize : '14px';

            notes.push({
                shapeClass: item.classList.contains('size-portrait') ? 'size-portrait' : (item.classList.contains('size-landscape') ? 'size-landscape' : 'size-square'),
                bg: item.style.backgroundColor,
                top: item.style.top,
                left: item.style.left,
                transform: item.style.transform,
                html: item.innerHTML,
                author: authorText,
                savedFontSize: fontSize
            });
        });
        localStorage.setItem(STORAGE_KEY_NOTES, JSON.stringify(notes));

        const keychains = [];
        document.querySelectorAll('.keychain-hook-unit').forEach(hook => {
            const img = hook.querySelector('.keychain-plush-img');
            keychains.push(img ? img.dataset.key : null);
        });
        localStorage.setItem(STORAGE_KEY_KEYCHAINS, JSON.stringify(keychains));
    }

    function loadBoardFromStorage() {
        const savedNotes = localStorage.getItem(STORAGE_KEY_NOTES);
        if (savedNotes) {
            const notesArr = JSON.parse(savedNotes);
            corkboard.innerHTML = '';
            notesArr.forEach(n => {
                const noteDiv = document.createElement('div');
                noteDiv.className = `cork-postit ${n.shapeClass}`;
                noteDiv.style.backgroundColor = n.bg;
                noteDiv.style.top = n.top;
                noteDiv.style.left = n.left;
                noteDiv.style.transform = n.transform;
                noteDiv.innerHTML = n.html;
                
                const textElem = noteDiv.querySelector('.postit-text-content');
                if (textElem && n.savedFontSize) {
                    textElem.style.fontSize = n.savedFontSize;
                }

                const isAuthor = n.author.includes(`@${CURRENT_USERNAME}`);
                if (IS_OWN_PROFILE || isAuthor) {
                    noteDiv.classList.add('can-delete');
                }

                const delBtn = noteDiv.querySelector('.postit-delete-btn');
                if (delBtn) {
                    delBtn.onclick = function(e) {
                        e.stopPropagation();
                        noteDiv.remove();
                        saveBoardToStorage();
                    };
                }

                makePostitDraggable(noteDiv, isAuthor);
                corkboard.appendChild(noteDiv);
            });
        } else {
            corkboard.innerHTML = `
                <div class="cork-postit size-square" style="top: 20%; left: 12%; background: #fdf5a6; transform: rotate(-2deg);">
                    <span class="postit-pin"></span>
                    <button class="postit-delete-btn" onclick="this.parentElement.remove(); saveBoardToStorage();">✕</button>
                    <div class="postit-text-content" style="top: 30px; left: 12px; font-size: 14px;">Kitaplar harika gidiyor! 📖</div>
                    <div class="postit-author">@catlover</div>
                </div>
            `;
            const defaultNote = corkboard.querySelector('.cork-postit');
            if (defaultNote) makePostitDraggable(defaultNote, false);
        }

        const savedHooks = localStorage.getItem(STORAGE_KEY_KEYCHAINS);
        if (savedHooks) {
            const hookKeys = JSON.parse(savedHooks);
            hookKeys.forEach((key, index) => {
                const slot = document.querySelector(`.keychain-hook-unit[data-slot="${index + 1}"]`);
                if (!slot) return;
                slot.innerHTML = '<div class="hook-nail"></div>';
                if (key && ACHIEVEMENTS_DATA[key]) {
                    const img = document.createElement('img');
                    img.src = ACHIEVEMENTS_DATA[key].icon;
                    img.className = 'keychain-plush-img';
                    img.dataset.key = key;
                    img.title = IS_OWN_PROFILE ? 'Tıkla ve kaldır' : ACHIEVEMENTS_DATA[key].title;
                    img.onerror = function() { this.src = FALLBACK_BADGE_SVG; };
                    slot.appendChild(img);
                } else {
                    const empty = document.createElement('div');
                    empty.className = 'empty-hook-slot';
                    empty.title = IS_OWN_PROFILE ? 'Boş kanca - Çantadan as' : '';
                    slot.appendChild(empty);
                }
            });
        }
    }

    if (toggleEditBtn) {
        toggleEditBtn.addEventListener('click', function() {
            isEditing = !isEditing;
            if (isEditing) {
                this.innerText = '💾 SAVE';
                this.classList.add('saving');
                corkboard.classList.add('is-editing');
            } else {
                this.innerText = '✏️ Edit Board';
                this.classList.remove('saving');
                corkboard.classList.remove('is-editing');
                saveBoardToStorage();
            }
        });
    }

    // --- STORY TUTAMAÇLARI ---
    function setupStoryTransformer(boxId) {
        const box = document.getElementById(boxId);
        const rotateBtn = box.querySelector('.handle-rotate');
        const resizeBtn = box.querySelector('.handle-resize');
        let rotation = 0;

        box.addEventListener('mousedown', (e) => {
            document.querySelectorAll('.transform-box').forEach(b => b.classList.remove('is-selected'));
            box.classList.add('is-selected');
        });

        box.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('handle-btn')) return;
            e.preventDefault();
            let startX = e.clientX - box.offsetLeft;
            let startY = e.clientY - box.offsetTop;

            function move(ev) {
                box.style.left = (ev.clientX - startX) + 'px';
                box.style.top = (ev.clientY - startY) + 'px';
            }
            function stop() {
                window.removeEventListener('mousemove', move);
                window.removeEventListener('mouseup', stop);
            }
            window.addEventListener('mousemove', move);
            window.addEventListener('mouseup', stop);
        });

        if (resizeBtn) {
            resizeBtn.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                e.preventDefault();
                let startX = e.clientX;
                let startWidth = box.offsetWidth;
                const textElement = box.querySelector('.postit-text-content');
                let initialFontSize = textElement ? parseFloat(window.getComputedStyle(textElement).fontSize) : 14;

                function resize(ev) {
                    let delta = ev.clientX - startX;
                    let newWidth = Math.max(35, startWidth + delta);
                    box.style.width = newWidth + 'px';

                    if (boxId === 'stickerTransformBox') {
                        box.style.height = newWidth + 'px';
                    }

                    if (textElement) {
                        let scaleRatio = newWidth / startWidth;
                        let newFontSize = Math.max(10, Math.min(50, initialFontSize * scaleRatio));
                        textElement.style.fontSize = newFontSize + 'px';
                    }
                }
                function stop() {
                    window.removeEventListener('mousemove', resize);
                    window.removeEventListener('mouseup', stop);
                }
                window.addEventListener('mousemove', resize);
                window.addEventListener('mouseup', stop);
            });
        }

        if (rotateBtn) {
            rotateBtn.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                e.preventDefault();

                function rotate(ev) {
                    const rect = box.getBoundingClientRect();
                    const centerX = rect.left + rect.width / 2;
                    const centerY = rect.top + rect.height / 2;
                    const rad = Math.atan2(ev.clientX - centerX, -(ev.clientY - centerY));
                    rotation = rad * (180 / Math.PI);
                    box.style.transform = `rotate(${rotation}deg)`;
                }
                function stop() {
                    window.removeEventListener('mousemove', rotate);
                    window.removeEventListener('mouseup', stop);
                }
                window.addEventListener('mousemove', rotate);
                window.addEventListener('mouseup', stop);
            });
        }
    }

    setupStoryTransformer('textTransformBox');
    setupStoryTransformer('stickerTransformBox');

    document.getElementById('btnDeleteSticker').addEventListener('click', (e) => {
        e.stopPropagation();
        const sBox = document.getElementById('stickerTransformBox');
        sBox.style.display = 'none';
        document.getElementById('previewStickerLayer').src = '';
        document.getElementById('studioFileInput').value = '';
    });

    // --- STÜDYO MODAL VE ÇİVİLEME ---
    const modal = document.getElementById('postitStudioModal');
    const openModalBtn = document.getElementById('openPostitModalBtn');
    const textInput = document.getElementById('studioTextInput');
    const previewBox = document.getElementById('previewPostitBox');
    const previewText = document.getElementById('previewTextLayer');
    const previewSticker = document.getElementById('previewStickerLayer');
    const stickerBox = document.getElementById('stickerTransformBox');
    const fileInput = document.getElementById('studioFileInput');

    let selectedColor = '#fdf5a6';
    let selectedShape = 'size-square';

    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            document.getElementById('previewAuthorLabel').innerText = '@' + CURRENT_USERNAME;
            modal.classList.add('active');
        });
    }

    function closePostitModal() { modal.classList.remove('active'); }

    document.querySelectorAll('.color-ball').forEach(b => {
        b.addEventListener('click', function() {
            document.querySelectorAll('.color-ball').forEach(x => x.classList.remove('selected'));
            this.classList.add('selected');
            selectedColor = this.dataset.c;
            previewBox.style.backgroundColor = selectedColor;
        });
    });

    document.querySelectorAll('.shape-btn').forEach(b => {
        b.addEventListener('click', function() {
            document.querySelectorAll('.shape-btn').forEach(x => x.classList.remove('active'));
            this.classList.add('active');
            selectedShape = this.dataset.shape;
            previewBox.className = 'cork-postit ' + selectedShape;
        });
    });

    textInput.addEventListener('input', () => {
        previewText.innerText = textInput.value || 'Hello!';
    });

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.type === 'image/png') {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewSticker.src = e.target.result;
                stickerBox.style.display = 'block';
                document.querySelectorAll('.transform-box').forEach(b => b.classList.remove('is-selected'));
                stickerBox.classList.add('is-selected');
            };
            reader.readAsDataURL(file);
        }
    });

    function pinNoteToBoard() {
        const text = textInput.value.trim() || 'Cozy note!';
        const newNote = document.createElement('div');
        newNote.className = `cork-postit ${selectedShape} can-delete`;
        newNote.style.backgroundColor = selectedColor;
        newNote.style.top = '30%';
        newNote.style.left = '35%';
        newNote.style.transform = `rotate(${Math.floor(Math.random() * 8 - 4)}deg)`;

        const tBox = document.getElementById('textTransformBox');
        const sBox = document.getElementById('stickerTransformBox');
        const currentFontSize = window.getComputedStyle(previewText).fontSize;

        let stickerHtml = (sBox.style.display !== 'none' && previewSticker.src) ? `
            <img src="${previewSticker.src}" class="postit-sticker-img" 
                 style="top:${sBox.style.top}; left:${sBox.style.left}; width:${sBox.style.width}; height:${sBox.style.height}; transform:${sBox.style.transform};">
        ` : '';

        newNote.innerHTML = `
            <span class="postit-pin"></span>
            <button class="postit-delete-btn" onclick="this.parentElement.remove(); saveBoardToStorage();">✕</button>
            <div class="postit-text-content" style="top:${tBox.style.top}; left:${tBox.style.left}; width:${tBox.style.width}; font-size:${currentFontSize}; transform:${tBox.style.transform};">
                ${text}
            </div>
            ${stickerHtml}
            <div class="postit-author">@${CURRENT_USERNAME}</div>
        `;

        makePostitDraggable(newNote, true);
        corkboard.appendChild(newNote);
        closePostitModal();

        textInput.value = '';
        stickerBox.style.display = 'none';
        previewSticker.src = '';
        fileInput.value = '';

        saveBoardToStorage();
    }

    // --- SERBEST SÜRÜKLEME ---
    function makePostitDraggable(element, isMyNote = false) {
        element.ondragstart = () => false;

        element.onmousedown = function(e) {
            if (e.target.classList.contains('postit-delete-btn')) return;

            if (!IS_OWN_PROFILE && !isMyNote) return;
            if (IS_OWN_PROFILE && !isEditing && !isMyNote) return;

            e.preventDefault();

            let activeDragItem = element;
            const rect = activeDragItem.getBoundingClientRect();
            let dragOffsetX = e.clientX - rect.left;
            let dragOffsetY = e.clientY - rect.top;

            function onMouseMove(event) {
                const boardRect = corkboard.getBoundingClientRect();
                let left = event.clientX - boardRect.left - dragOffsetX;
                let top = event.clientY - boardRect.top - dragOffsetY;

                left = Math.max(0, Math.min(boardRect.width - activeDragItem.offsetWidth, left));
                top = Math.max(0, Math.min(boardRect.height - activeDragItem.offsetHeight, top));

                activeDragItem.style.left = (left / boardRect.width * 100) + '%';
                activeDragItem.style.top = (top / boardRect.height * 100) + '%';
            }

            function onMouseUp() {
                window.removeEventListener('mousemove', onMouseMove);
                window.removeEventListener('mouseup', onMouseUp);
                saveBoardToStorage();
            }

            window.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);
        };
    }

    // --- ÇANTA & KANCA İŞLEMLERİ ---
    const drawer = document.getElementById('collectionDrawer');
    const drawerList = document.getElementById('drawerBadgesList');

    function toggleCollectionDrawer() {
        if (!IS_OWN_PROFILE || !drawer) return;
        drawer.classList.toggle('active');
        if (drawer.classList.contains('active')) renderDrawerAchievements();
    }

    function renderDrawerAchievements() {
        if (!drawerList) return;
        drawerList.innerHTML = '';
        Object.keys(ACHIEVEMENTS_DATA).forEach(key => {
            const item = ACHIEVEMENTS_DATA[key];
            const isUnlocked = item.unlocked;

            const card = document.createElement('div');
            card.className = `bag-badge-card ${isUnlocked ? 'unlocked' : 'locked'}`;
            card.innerHTML = `
                <img src="${item.icon}" class="bag-badge-img" alt="${item.title}" onerror="this.onerror=null; this.src='${FALLBACK_BADGE_SVG}';">
                <p class="bag-badge-title">${item.title}</p>
                <p class="bag-badge-desc">${item.req}</p>
                <span class="bag-badge-status ${isUnlocked ? 'status-unlocked' : 'status-locked'}">
                    ${isUnlocked ? '✓ Açık (As)' : '🔒 Kilitli'}
                </span>
            `;
            if (isUnlocked) card.onclick = () => selectBadgeFromBag(key, item);
            drawerList.appendChild(card);
        });
    }

    function selectBadgeFromBag(key, item) {
        if (!IS_OWN_PROFILE) return;
        const slots = document.querySelectorAll('.keychain-hook-unit');
        for (let slot of slots) {
            const emptySlot = slot.querySelector('.empty-hook-slot');
            if (emptySlot) {
                emptySlot.remove();
                const img = document.createElement('img');
                img.src = item.icon;
                img.className = 'keychain-plush-img';
                img.dataset.key = key;
                img.title = 'Tıkla ve kaldır';
                img.onerror = function() { this.src = FALLBACK_BADGE_SVG; };
                slot.appendChild(img);
                saveBoardToStorage();
                return;
            }
        }
        alert('Tüm 9 kanca dolu! Birini kaldırmak için üzerine tıkla.');
    }

    function handleHookSlotClick(slotNum) {
        if (!IS_OWN_PROFILE) return;
        const slotElem = document.querySelector(`.keychain-hook-unit[data-slot="${slotNum}"]`);
        const existingImg = slotElem.querySelector('.keychain-plush-img');

        if (existingImg) {
            if (confirm('Bu anahtarlığı kancadan çıkarıp çantana geri koymak istiyor musun?')) {
                existingImg.remove();
                const emptyBox = document.createElement('div');
                emptyBox.className = 'empty-hook-slot';
                emptyBox.title = 'Boş kanca - Çantadan as';
                slotElem.appendChild(emptyBox);
                saveBoardToStorage();
            }
        } else {
            if (drawer && !drawer.classList.contains('active')) toggleCollectionDrawer();
        }
    }

    document.addEventListener('DOMContentLoaded', loadBoardFromStorage);
</script>

</body>
</html>