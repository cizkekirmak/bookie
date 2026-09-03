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
            box-shadow: 0 8px 22px rgba(0,0,0,0.15);
            border-radius: 14px;
            overflow: hidden;
        }

        .board-bottom-bar {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
            width: 100%;
        }

        /* NAZİK BUTONLAR */
        .btn-action {
            background: #fdfaf0;
            border: 1px solid #7ea863;
            border-radius: 20px;
            padding: 7px 20px;
            font-family: 'Unkempt', cursive;
            font-size: 14px;
            font-weight: 600;
            color: #2b461c;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background: #ffffff;
            border-color: #5a7d3b;
        }
        .btn-action.saving {
            background: #d8eed0;
            border-color: #4b813b;
            color: #1e4215;
        }

        /* POST-IT BOYUTLARI (PANODA KÜÇÜK) */
        .cork-postit {
            position: absolute;
            box-shadow: 2px 4px 10px rgba(0,0,0,0.18);
            padding: 6px;
            cursor: grab;
            user-select: none;
            overflow: hidden;
            transition: box-shadow 0.15s;
            touch-action: none;
        }
        .cork-postit:active { cursor: grabbing; box-shadow: 3px 6px 14px rgba(0,0,0,0.28); }
        .cork-postit.size-square { width: 96px; height: 96px; }
        .cork-postit.size-portrait { width: 90px; height: 125px; }
        .cork-postit.size-landscape { width: 125px; height: 90px; }

        .postit-pin {
            position: absolute;
            top: 3px;
            left: 50%;
            transform: translateX(-50%);
            width: 9px;
            height: 9px;
            background: #e74c3c;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.25);
            border: 1px solid #c0392b;
            z-index: 20;
            pointer-events: none;
        }

        #corkboardArea .postit-text-content {
            font-size: 12px;
            line-height: 1.15;
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

        /* PANODAKİ SERBEST STICKER VE SEÇİM KUTUSU */
        .free-sticker-wrapper {
            position: absolute;
            cursor: grab;
            user-select: none;
            touch-action: none;
            z-index: 15;
            display: inline-block;
        }
        .free-sticker-wrapper:active { cursor: grabbing; }
        .free-sticker-wrapper.is-selected {
            outline: 1.5px dashed #2d5a27;
            outline-offset: 2px;
        }
        .free-sticker-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            pointer-events: none;
            -webkit-user-drag: none;
            filter: drop-shadow(0 3px 6px rgba(0,0,0,0.16));
        }

        .postit-author {
            position: absolute;
            bottom: 3px;
            left: 5px;
            font-size: 9px;
            color: rgba(0,0,0,0.55);
            pointer-events: none;
            z-index: 10;
        }

        .postit-delete-btn {
            display: none;
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ff7675;
            color: white;
            border: none;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 10px;
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
            transition: transform 0.15s;
        }
        .keychain-hook-unit.drag-over { transform: scale(1.12); }

        .hook-nail {
            width: 8px;
            height: 8px;
            background: radial-gradient(circle, #ffffff 25%, #888888 75%, #333333 100%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.4);
            z-index: 2;
            margin-bottom: -5px;
        }

        /* KEYCHAIN (EKSTRA PADDING/MARGIN KALDIRILMIŞ DOĞAL HALİ) */
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

        .is-editing-mode .keychain-plush-img:hover {
            opacity: 0.6;
            filter: drop-shadow(0 0 6px rgba(231, 76, 60, 0.7));
            cursor: pointer;
        }

        .empty-hook-slot {
            width: 42px;
            height: 42px;
            border: 1.5px dashed rgba(44, 68, 27, 0.22);
            border-radius: 12px;
            margin-top: 14px;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }
        .empty-hook-slot:hover,
        .drag-over .empty-hook-slot {
            background: rgba(255,255,255,0.3);
            border-color: #2d5a27;
        }

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

        /* MERKEZİ KOLEKSİYON ÇEKMECESİ */
        .keychain-collection-drawer {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            width: 90%;
            max-width: 460px;
            max-height: 75vh;
            background: #fdfaf3;
            border: 2px solid #7ea863;
            border-radius: 22px;
            box-shadow: 0 14px 36px rgba(0,0,0,0.22);
            display: none;
            flex-direction: column;
            z-index: 100000;
            overflow: hidden;
            opacity: 0;
            transition: all 0.2s ease-out;
        }
        .keychain-collection-drawer.active {
            display: flex;
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 18px;
            background: #f4f9f0;
            border-bottom: 1.5px solid #bddbb0;
        }
        .drawer-header h4 { margin: 0; font-size: 15px; color: #1a3c11; }
        .drawer-close-btn { background: none; border: none; font-size: 18px; font-weight: bold; color: #2d5a27; cursor: pointer; }

        .drawer-body {
            padding: 16px 14px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px 10px;
            justify-items: center;
        }

        .bag-badge-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: transparent;
            border: none;
            cursor: grab;
            user-select: none;
            transition: transform 0.15s;
        }
        .bag-badge-item:hover { transform: translateY(-3px) scale(1.08); }
        .bag-badge-item.locked { opacity: 0.35; filter: grayscale(100%); cursor: not-allowed; }

        .bag-badge-img {
            width: 58px;
            height: 58px;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.14));
            -webkit-user-drag: none;
        }
        .bag-badge-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a3c11;
            margin: 4px 0 0 0;
            line-height: 1.15;
            max-width: 72px;
            word-break: break-word;
        }

        /* POST-IT OLUŞTURUCU MODAL & DAHA BÜYÜK PREVIEW */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(2px);
            z-index: 100000;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }

        .studio-modal-box {
            background: #fdfcf8;
            border: 2px solid #7ea863;
            border-radius: 20px;
            padding: 22px;
            width: 95%;
            max-width: 660px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 12px 34px rgba(0,0,0,0.18);
        }

        .studio-columns {
            display: flex;
            gap: 24px;
            align-items: center;
        }
        .studio-tools {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* BÜYÜTÜLMÜŞ ÖNİZLEME ALANI */
        .studio-preview {
            width: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* PREVIEW İÇİNDE NET GÖRÜNEN BÜYÜK POST-IT */
        .studio-preview #previewPostitBox.size-square { width: 175px; height: 175px; }
        .studio-preview #previewPostitBox.size-portrait { width: 160px; height: 215px; }
        .studio-preview #previewPostitBox.size-landscape { width: 220px; height: 160px; }

        .shape-btn-group { display: flex; gap: 8px; width: 100%; }
        .shape-btn {
            flex: 1;
            background: #ffffff;
            border: 1px solid #bddbb0;
            border-radius: 14px;
            padding: 6px 4px;
            font-family: 'Unkempt', cursive;
            font-size: 13px;
            font-weight: bold;
            color: #355726;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .shape-btn.active {
            background: #3e682e;
            color: #ffffff;
            border-color: #3e682e;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }

        .color-selector { display: flex; gap: 8px; }
        .color-ball {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 1.5px solid rgba(0,0,0,0.12);
            cursor: pointer;
            transition: transform 0.15s;
        }
        .color-ball.selected {
            border: 2px solid #2d5a27;
            transform: scale(1.15);
        }

        .studio-input-box {
            width: 100%;
            height: 54px;
            font-family: 'Unkempt';
            padding: 8px 10px;
            resize: none;
            border: 1px solid #c8debf;
            border-radius: 12px;
            outline: none;
            background: #ffffff;
            font-size: 13px;
        }
        .studio-input-box:focus {
            border-color: #5a7d3b;
        }

        .btn-gentle-upload {
            background: #f1f7ed;
            border: 1px dashed #7ea863;
            padding: 7px;
            text-align: center;
            border-radius: 12px;
            font-size: 13px;
            cursor: pointer;
            color: #2d5a27;
            transition: background 0.15s;
        }
        .btn-gentle-upload:hover { background: #e5f1df; }

        /* KELİME KADAR DARALAN ESNEK KUTU */
        .transform-box {
            position: absolute;
            cursor: move;
            border: 1px dashed transparent;
            touch-action: none;
            display: inline-block;
            width: fit-content;
            max-width: 100%;
        }
        .transform-box.is-selected { border-color: #2d5a27; }

        .handle-btn {
            display: none;
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.25);
            z-index: 30;
        }
        .transform-box.is-selected .handle-btn,
        .free-sticker-wrapper.is-selected .handle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .handle-delete { top: -8px; left: -8px; background: #ff7675; color: white; font-size: 10px; cursor: pointer; }
        .handle-rotate { top: -14px; left: 50%; transform: translateX(-50%); background: #fab1a0; color: #2d3436; font-size: 10px; cursor: grab; }
        .handle-resize { bottom: -8px; right: -8px; background: #74b9ff; color: white; font-size: 10px; cursor: nwse-resize; }

        @media (max-width: 1024px) {
            .corkboard-main-wrapper { flex-direction: column; align-items: center; gap: 16px; }
            .corkboard-frame { max-width: 100%; border-radius: 10px; }
            .keychain-area-wrapper { width: 100%; margin-top: 6px; }
            .keychain-grid-9 { display: flex; flex-direction: row; overflow-x: auto; width: 100%; justify-content: flex-start; padding: 10px 6px; gap: 12px; }
            .keychain-hook-unit { width: 56px; height: 78px; flex-shrink: 0; }
            .keychain-collection-drawer { width: 92%; max-height: 70vh; }
            .drawer-body { grid-template-columns: repeat(3, 1fr); }
            .desktop-only-action { display: none !important; }
            .studio-columns { flex-direction: column; }
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
                        <div class="empty-hook-slot" title="{{ $isOwnProfile ? 'Boş kanca' : '' }}"></div>
                    </div>
                @endfor
            </div>

            <!-- DOSYA VE YAZI SADECE PROFİL SAHİBİNDE GÖRÜNÜR -->
            @if($isOwnProfile)
            <div class="folder-container" onclick="toggleCollectionDrawer()" title="All Keychains">
                <img src="{{ asset('images/dosya.png') }}" alt="Folder" class="folder-img" onerror="this.onerror=null; this.src='{{ asset('images/folder.png') }}';">
                <span class="folder-label">all keychains u own</span>
            </div>
            @endif
        </div>
    </div>

    <!-- ALT BUTON BARI (NAZİK TASARIM) -->
    <div class="board-bottom-bar">
        @if($isOwnProfile)
            <button id="toggleEditBtn" class="btn-action desktop-only-action">✏️ Edit Board</button>
            <button type="button" class="btn-action" onclick="document.getElementById('freeStickerUploadInput').click()">🖼️ ADD STICKER</button>
            <input type="file" id="freeStickerUploadInput" accept="image/png" style="display:none;">
        @endif
        @if(auth()->check())
            <button id="openPostitModalBtn" class="btn-action">📌 ADD POST-IT</button>
        @endif
    </div>

    @if($isOwnProfile)
    <div class="keychain-collection-drawer" id="collectionDrawer">
        <div class="drawer-header">
            <h4>🎒 Keychain Bag (Tutup kancaya bırak)</h4>
            <button type="button" class="drawer-close-btn" onclick="toggleCollectionDrawer()">✕</button>
        </div>
        <div class="drawer-body" id="drawerBadgesList"></div>
    </div>
    @endif

</div>

<!-- POST-IT MİNİ STÜDYO MODAL (DAHA BÜYÜK PREVIEW) -->
<div class="modal-overlay" id="postitStudioModal">
    <div class="studio-modal-box">
        <h3 style="margin: 0; font-size: 16px; color: #1e4215;">✨ Create Your Note</h3>
        
        <div class="studio-columns">
            <div class="studio-tools">
                <div>
                    <label style="font-size: 13px; color: #2c441b;">Color:</label>
                    <div class="color-selector" style="margin-top: 4px;">
                        <div class="color-ball selected" style="background:#fdf5a6;" data-c="#fdf5a6"></div>
                        <div class="color-ball" style="background:#ffd1dc;" data-c="#ffd1dc"></div>
                        <div class="color-ball" style="background:#c6e085;" data-c="#c6e085"></div>
                        <div class="color-ball" style="background:#b5e2fa;" data-c="#b5e2fa"></div>
                        <div class="color-ball" style="background:#dfccf1;" data-c="#dfccf1"></div>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; color: #2c441b;">Shape:</label>
                    <div class="shape-btn-group" style="margin-top: 4px;">
                        <button type="button" class="shape-btn active" data-shape="size-square">Square</button>
                        <button type="button" class="shape-btn" data-shape="size-portrait">Portrait</button>
                        <button type="button" class="shape-btn" data-shape="size-landscape">Landscape</button>
                    </div>
                </div>

                <textarea id="studioTextInput" class="studio-input-box" placeholder="Write something cozy..." maxlength="120"></textarea>

                <label class="btn-gentle-upload">
                    PNG Sticker Ekle
                    <input type="file" id="studioFileInput" accept="image/png" style="display:none;">
                </label>
            </div>

            <!-- BÜYÜTÜLMÜŞ ÖNİZLEME -->
            <div class="studio-preview">
                <div class="cork-postit size-square" id="previewPostitBox" style="position:relative; background:#fdf5a6; overflow:hidden;">
                    <span class="postit-pin"></span>
                    
                    <div id="textTransformBox" class="transform-box is-selected" style="top:32px; left:16px;">
                        <div class="postit-text-content" id="previewTextLayer" style="position:relative; font-size:16px; white-space:nowrap;">selam</div>
                        <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                        <div class="handle-btn handle-resize" title="Büyüt / Küçült">⤡</div>
                    </div>

                    <div id="stickerTransformBox" class="transform-box" style="display:none; top:60px; left:40px; width:65px; height:65px;">
                        <img id="previewStickerLayer" class="postit-sticker-img" style="width:100%; height:100%; position:relative;">
                        <div class="handle-btn handle-delete" id="btnDeleteSticker" title="Sil">✕</div>
                        <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                        <div class="handle-btn handle-resize" title="Büyüt">⤡</div>
                    </div>

                    <div class="postit-author" id="previewAuthorLabel"></div>
                </div>
                <span style="font-size:10px; color:#777;">*Köşelerden tutup döndür / boyutlandır</span>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top: 4px;">
            <button type="button" class="btn-action" style="background:#f0f0f0; border-color:#ccc;" onclick="closePostitModal()">Cancel</button>
            <button type="button" class="btn-action" style="background:#e3f2dc; border-color:#7ea863;" onclick="pinNoteToBoard()">OK!</button>
        </div>
    </div>
</div>

@php
function getAllAchievementsList() {
    return [
        'ask'       => ['title' => 'Aşk',       'icon' => asset('images/badges/aşk.png'),       'unlocked' => true],
        'ayicik'    => ['title' => 'Ayıcık',    'icon' => asset('images/badges/ayıcık.png'),    'unlocked' => true],
        'burger'    => ['title' => 'Burger',    'icon' => asset('images/badges/burger.png'),    'unlocked' => true],
        'cilek'     => ['title' => 'Çilek',     'icon' => asset('images/badges/çilek.png'),     'unlocked' => true],
        'elma'      => ['title' => 'Elma',      'icon' => asset('images/badges/elma.png'),      'unlocked' => true],
        'geyik'     => ['title' => 'Geyik',     'icon' => asset('images/badges/geyik.png'),     'unlocked' => true],
        'jake'      => ['title' => 'Jake',      'icon' => asset('images/badges/jake.png'),      'unlocked' => true],
        'kedi'      => ['title' => 'Kedi',      'icon' => asset('images/badges/kedi.png'),      'unlocked' => true],
        'kitap'     => ['title' => 'Kitap',     'icon' => asset('images/badges/kitap.png'),     'unlocked' => true],
        'kruvasan'  => ['title' => 'Kruvasan',  'icon' => asset('images/badges/kruvasan.png'),  'unlocked' => true],
        'maymun'    => ['title' => 'Maymun',    'icon' => asset('images/badges/maymun.png'),    'unlocked' => true],
        'tama'      => ['title' => 'Tama',      'icon' => asset('images/badges/tama.png'),      'unlocked' => true],
        'usagi'     => ['title' => 'Usagi',     'icon' => asset('images/badges/usagi.png'),     'unlocked' => true],
        'yengec'    => ['title' => 'Yengeç',    'icon' => asset('images/badges/yengeç.png'),    'unlocked' => true],
        'yonca'     => ['title' => 'Yonca',     'icon' => asset('images/badges/yonca.png'),     'unlocked' => true],
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
    const STORAGE_KEY_BOARD = `bookie_full_board_${PROFILE_USER_ID}`;
    const STORAGE_KEY_KEYCHAINS = `bookie_board_keychains_${PROFILE_USER_ID}`;

    function saveBoardToStorage() {
        const boardItems = [];
        
        // Post-it'leri topla
        document.querySelectorAll('#corkboardArea .cork-postit').forEach(item => {
            const authorText = item.querySelector('.postit-author') ? item.querySelector('.postit-author').innerText : '';
            const textElem = item.querySelector('.postit-text-content');
            const fontSize = textElem ? window.getComputedStyle(textElem).fontSize : '12px';

            boardItems.push({
                type: 'postit',
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

        // Serbest PNG'leri topla
        document.querySelectorAll('#corkboardArea .free-sticker-wrapper').forEach(wrap => {
            const img = wrap.querySelector('img');
            boardItems.push({
                type: 'free_sticker',
                src: img ? img.src : '',
                top: wrap.style.top,
                left: wrap.style.left,
                width: wrap.style.width,
                height: wrap.style.height,
                transform: wrap.style.transform
            });
        });

        localStorage.setItem(STORAGE_KEY_BOARD, JSON.stringify(boardItems));

        const keychains = [];
        document.querySelectorAll('.keychain-hook-unit').forEach(hook => {
            const img = hook.querySelector('.keychain-plush-img');
            keychains.push(img ? img.dataset.key : null);
        });
        localStorage.setItem(STORAGE_KEY_KEYCHAINS, JSON.stringify(keychains));
    }

    function loadBoardFromStorage() {
        const savedBoard = localStorage.getItem(STORAGE_KEY_BOARD);
        if (savedBoard) {
            const items = JSON.parse(savedBoard);
            corkboard.innerHTML = '';
            items.forEach(item => {
                if (item.type === 'postit') {
                    const noteDiv = document.createElement('div');
                    noteDiv.className = `cork-postit ${item.shapeClass}`;
                    noteDiv.style.backgroundColor = item.bg;
                    noteDiv.style.top = item.top;
                    noteDiv.style.left = item.left;
                    noteDiv.style.transform = item.transform;
                    noteDiv.innerHTML = item.html;
                    
                    const textElem = noteDiv.querySelector('.postit-text-content');
                    if (textElem && item.savedFontSize) {
                        textElem.style.fontSize = item.savedFontSize;
                    }

                    const isAuthor = item.author.includes(`@${CURRENT_USERNAME}`);
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

                    makeItemDraggable(noteDiv, isAuthor);
                    corkboard.appendChild(noteDiv);
                } else if (item.type === 'free_sticker') {
                    createFreeStickerElement(item.src, item.top, item.left, item.width, item.height, item.transform);
                }
            });
        } else {
            corkboard.innerHTML = `
                <div class="cork-postit size-square" style="top: 20%; left: 12%; background: #fdf5a6; transform: rotate(-2deg);">
                    <span class="postit-pin"></span>
                    <button class="postit-delete-btn" onclick="this.parentElement.remove(); saveBoardToStorage();">✕</button>
                    <div class="postit-text-content" style="top: 24px; left: 8px; font-size: 12px;">Kitaplar harika gidiyor! 📖</div>
                    <div class="postit-author">@catlover</div>
                </div>
            `;
            const defaultNote = corkboard.querySelector('.cork-postit');
            if (defaultNote) makeItemDraggable(defaultNote, false);
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
                    img.title = ACHIEVEMENTS_DATA[key].title;
                    img.onerror = function() { this.src = FALLBACK_BADGE_SVG; };
                    slot.appendChild(img);
                } else {
                    const empty = document.createElement('div');
                    empty.className = 'empty-hook-slot';
                    slot.appendChild(empty);
                }
            });
        }
    }

    // --- PANODAKİ SERBEST PNG İÇİN STORY BOYUTLANDIRICI & SÜRÜKLEME ---
    function createFreeStickerElement(src, top = '30%', left = '40%', width = '75px', height = '75px', transform = 'rotate(0deg)') {
        const wrap = document.createElement('div');
        wrap.className = 'free-sticker-wrapper';
        wrap.style.top = top;
        wrap.style.left = left;
        wrap.style.width = width || '75px';
        wrap.style.height = height || '75px';
        wrap.style.transform = transform || 'rotate(0deg)';

        wrap.innerHTML = `
            <img src="${src}">
            ${IS_OWN_PROFILE ? `
                <div class="handle-btn handle-delete" title="Sil">✕</div>
                <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                <div class="handle-btn handle-resize" title="Büyüt / Küçült">⤡</div>
            ` : ''}
        `;

        if (IS_OWN_PROFILE) {
            setupFreeStickerControls(wrap);
            makeItemDraggable(wrap, true);
        }

        corkboard.appendChild(wrap);
        return wrap;
    }

    function setupFreeStickerControls(wrap) {
        const delBtn = wrap.querySelector('.handle-delete');
        const rotBtn = wrap.querySelector('.handle-rotate');
        const resBtn = wrap.querySelector('.handle-resize');
        let rotation = 0;

        wrap.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('handle-btn')) return;
            document.querySelectorAll('.free-sticker-wrapper').forEach(w => w.classList.remove('is-selected'));
            wrap.classList.add('is-selected');
        });

        if (delBtn) {
            delBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                wrap.remove();
                saveBoardToStorage();
            });
        }

        if (resBtn) {
            resBtn.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                e.preventDefault();
                let startX = e.clientX;
                let startWidth = wrap.offsetWidth;

                function resize(ev) {
                    let delta = ev.clientX - startX;
                    let newW = Math.max(30, startWidth + delta);
                    wrap.style.width = newW + 'px';
                    wrap.style.height = newW + 'px';
                }
                function stop() {
                    window.removeEventListener('mousemove', resize);
                    window.removeEventListener('mouseup', stop);
                    saveBoardToStorage();
                }
                window.addEventListener('mousemove', resize);
                window.addEventListener('mouseup', stop);
            });
        }

        if (rotBtn) {
            rotBtn.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                e.preventDefault();

                function rotate(ev) {
                    const rect = wrap.getBoundingClientRect();
                    const centerX = rect.left + rect.width / 2;
                    const centerY = rect.top + rect.height / 2;
                    const rad = Math.atan2(ev.clientX - centerX, -(ev.clientY - centerY));
                    rotation = rad * (180 / Math.PI);
                    wrap.style.transform = `rotate(${rotation}deg)`;
                }
                function stop() {
                    window.removeEventListener('mousemove', rotate);
                    window.removeEventListener('mouseup', stop);
                    saveBoardToStorage();
                }
                window.addEventListener('mousemove', rotate);
                window.addEventListener('mouseup', stop);
            });
        }
    }

    // Panonun boş yerine tıklayınca serbest sticker seçimini kaldır
    corkboard.addEventListener('mousedown', (e) => {
        if (!e.target.closest('.free-sticker-wrapper')) {
            document.querySelectorAll('.free-sticker-wrapper').forEach(w => w.classList.remove('is-selected'));
        }
    });

    // --- KULLANICI PANOSUNA DİREKT PNG EKLEME ---
    const freeStickerInput = document.getElementById('freeStickerUploadInput');
    if (freeStickerInput) {
        freeStickerInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.type === 'image/png') {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const newElem = createFreeStickerElement(e.target.result, '30%', '40%');
                    document.querySelectorAll('.free-sticker-wrapper').forEach(w => w.classList.remove('is-selected'));
                    newElem.classList.add('is-selected');
                    saveBoardToStorage();
                };
                reader.readAsDataURL(file);
                this.value = '';
            }
        });
    }

    // --- DÜZENLEME MODU ---
    if (toggleEditBtn) {
        toggleEditBtn.addEventListener('click', function() {
            isEditing = !isEditing;
            const grid = document.getElementById('keychainHooksGrid');
            if (isEditing) {
                this.innerText = '💾 SAVE';
                this.classList.add('saving');
                corkboard.classList.add('is-editing');
                if (grid) grid.classList.add('is-editing-mode');
            } else {
                this.innerText = '✏️ Edit Board';
                this.classList.remove('saving');
                corkboard.classList.remove('is-editing');
                if (grid) grid.classList.remove('is-editing-mode');
                saveBoardToStorage();
            }
        });
    }

    function handleHookSlotClick(slotNum) {
        if (!IS_OWN_PROFILE) return;
        const slotElem = document.querySelector(`.keychain-hook-unit[data-slot="${slotNum}"]`);
        const existingImg = slotElem.querySelector('.keychain-plush-img');

        if (isEditing && existingImg) {
            existingImg.remove();
            const emptyBox = document.createElement('div');
            emptyBox.className = 'empty-hook-slot';
            slotElem.appendChild(emptyBox);
            saveBoardToStorage();
            return;
        }

        if (!existingImg && drawer && !drawer.classList.contains('active')) {
            toggleCollectionDrawer();
        }
    }

    // --- MODAL İÇİ STORY TUTAMAÇLARI ---
    function setupStoryTransformer(boxId) {
        const box = document.getElementById(boxId);
        const rotateBtn = box.querySelector('.handle-rotate');
        const resizeBtn = box.querySelector('.handle-resize');
        let rotation = 0;

        box.addEventListener('mousedown', (e) => {
            document.querySelectorAll('#postitStudioModal .transform-box').forEach(b => b.classList.remove('is-selected'));
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
                let initialFontSize = textElement ? parseFloat(window.getComputedStyle(textElement).fontSize) : 16;

                function resize(ev) {
                    let delta = ev.clientX - startX;
                    let newWidth = Math.max(15, startWidth + delta);
                    box.style.width = newWidth + 'px';

                    if (boxId === 'stickerTransformBox') {
                        box.style.height = newWidth + 'px';
                    }

                    if (textElement) {
                        let scaleRatio = newWidth / Math.max(startWidth, 20);
                        let newFontSize = Math.max(8, Math.min(52, initialFontSize * scaleRatio));
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
    const textTransformBox = document.getElementById('textTransformBox');
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
        previewText.innerText = textInput.value || 'selam';
        textTransformBox.style.width = 'fit-content';
    });

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.type === 'image/png') {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewSticker.src = e.target.result;
                stickerBox.style.display = 'block';
                document.querySelectorAll('#postitStudioModal .transform-box').forEach(b => b.classList.remove('is-selected'));
                stickerBox.classList.add('is-selected');
            };
            reader.readAsDataURL(file);
        }
    });

    function pinNoteToBoard() {
        const text = textInput.value.trim() || 'selam';
        const newNote = document.createElement('div');
        newNote.className = `cork-postit ${selectedShape} can-delete`;
        newNote.style.backgroundColor = selectedColor;
        newNote.style.top = '28%';
        newNote.style.left = '35%';
        newNote.style.transform = `rotate(${Math.floor(Math.random() * 8 - 4)}deg)`;

        const tBox = document.getElementById('textTransformBox');
        const sBox = document.getElementById('stickerTransformBox');
        
        // Preview daha büyük olduğu için panoya asarken hafif orantılıyoruz (~0.75x)
        const computedFs = parseFloat(window.getComputedStyle(previewText).fontSize);
        const scaledFontSize = Math.max(9, Math.round(computedFs * 0.75)) + 'px';

        let stickerHtml = (sBox.style.display !== 'none' && previewSticker.src) ? `
            <img src="${previewSticker.src}" class="postit-sticker-img" 
                 style="top:${sBox.style.top}; left:${sBox.style.left}; width:${sBox.offsetWidth * 0.75}px; height:${sBox.offsetHeight * 0.75}px; transform:${sBox.style.transform};">
        ` : '';

        newNote.innerHTML = `
            <span class="postit-pin"></span>
            <button class="postit-delete-btn" onclick="this.parentElement.remove(); saveBoardToStorage();">✕</button>
            <div class="postit-text-content" style="top:${tBox.style.top}; left:${tBox.style.left}; width:${tBox.style.width || 'auto'}; font-size:${scaledFontSize}; transform:${tBox.style.transform};">
                ${text}
            </div>
            ${stickerHtml}
            <div class="postit-author">@${CURRENT_USERNAME}</div>
        `;

        makeItemDraggable(newNote, true);
        corkboard.appendChild(newNote);
        closePostitModal();

        textInput.value = '';
        stickerBox.style.display = 'none';
        previewSticker.src = '';
        fileInput.value = '';

        saveBoardToStorage();
    }

    // --- EVRENSEL SÜRÜKLEME ---
    function makeItemDraggable(element, isMyItem = false) {
        element.ondragstart = () => false;

        element.onmousedown = function(e) {
            if (e.target.classList.contains('postit-delete-btn') || e.target.classList.contains('handle-btn')) return;

            if (!IS_OWN_PROFILE && !isMyItem) return;
            if (IS_OWN_PROFILE && !isEditing && !isMyItem) return;

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

    // --- ÇANTA & SÜRÜKLE-BIRAK (DRAG & DROP) ---
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

            const wrap = document.createElement('div');
            wrap.className = `bag-badge-item ${isUnlocked ? 'unlocked' : 'locked'}`;
            wrap.title = isUnlocked ? 'Tıkla veya kancaya sürükle' : 'Kilitli';

            wrap.innerHTML = `
                <img src="${item.icon}" class="bag-badge-img" alt="${item.title}" onerror="this.onerror=null; this.src='${FALLBACK_BADGE_SVG}';">
                <span class="bag-badge-title">${item.title}</span>
            `;

            if (isUnlocked) {
                wrap.onclick = () => selectBadgeFromBag(key, item);

                wrap.setAttribute('draggable', 'true');
                wrap.ondragstart = (e) => {
                    e.dataTransfer.setData('text/plain', key);
                    wrap.style.opacity = '0.5';
                };
                wrap.ondragend = () => {
                    wrap.style.opacity = '1';
                };
            }

            drawerList.appendChild(wrap);
        });
    }

    function selectBadgeFromBag(key, item) {
        if (!IS_OWN_PROFILE) return;
        const slots = document.querySelectorAll('.keychain-hook-unit');
        for (let slot of slots) {
            const emptySlot = slot.querySelector('.empty-hook-slot');
            if (emptySlot) {
                hangBadgeToSlot(slot, key, item.icon, item.title);
                return;
            }
        }
        alert('Tüm 9 kanca dolu! Birini boşaltmak için Edit modunda tıkla.');
    }

    function hangBadgeToSlot(slotElem, key, iconUrl, title) {
        const emptySlot = slotElem.querySelector('.empty-hook-slot');
        if (emptySlot) emptySlot.remove();

        const oldImg = slotElem.querySelector('.keychain-plush-img');
        if (oldImg) oldImg.remove();

        const img = document.createElement('img');
        img.src = iconUrl;
        img.className = 'keychain-plush-img';
        img.dataset.key = key;
        img.title = title;
        img.onerror = function() { this.src = FALLBACK_BADGE_SVG; };
        slotElem.appendChild(img);

        saveBoardToStorage();
    }

    document.querySelectorAll('.keychain-hook-unit').forEach(hook => {
        hook.ondragover = (e) => {
            e.preventDefault();
            hook.classList.add('drag-over');
        };
        hook.ondragleave = () => {
            hook.classList.remove('drag-over');
        };
        hook.ondrop = (e) => {
            e.preventDefault();
            hook.classList.remove('drag-over');
            const key = e.dataTransfer.getData('text/plain');
            if (key && ACHIEVEMENTS_DATA[key] && ACHIEVEMENTS_DATA[key].unlocked) {
                hangBadgeToSlot(hook, key, ACHIEVEMENTS_DATA[key].icon, ACHIEVEMENTS_DATA[key].title);
            }
        };
    });

    document.addEventListener('DOMContentLoaded', loadBoardFromStorage);
</script>

</body>
</html>