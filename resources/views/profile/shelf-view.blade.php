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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 16px 80px 16px;
        }

        .board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: #f4fbf0;
            border: 2px solid #5a7d3b;
            border-radius: 18px;
            padding: 12px 24px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .board-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        /* DÜZENLEME & POST-IT BUTONLARI */
        .btn-action {
            background: #fdf5a6;
            border: 2px solid #5a7d3b;
            border-radius: 12px;
            padding: 8px 18px;
            font-family: 'Unkempt', cursive;
            font-size: 16px;
            font-weight: bold;
            color: #2c441b;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .btn-action:hover {
            transform: scale(1.04);
        }
        .btn-action.saving {
            background: #badfa0;
        }

        /* MANTAR PANO & ASKI DÜZENİ */
        .corkboard-main-wrapper {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 20px;
            width: 100%;
        }

        /* MANTAR PANO ÇERÇEVESİ (PANO.JPG KULLANILDI) */
        .corkboard-frame {
            flex: 1;
            min-height: 580px;
            background-color: #c29b62;
            background-image: url('{{ asset('images/pano.jpg') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
            padding: 3%; /* Görselin kendi ahşap çerçevesine post-it'ler taşmasın diye */
        }

        /* POST-IT KUTUCUKLARI */
        .cork-postit {
            position: absolute;
            box-shadow: 2px 5px 12px rgba(0,0,0,0.25);
            padding: 16px 12px 12px 12px;
            cursor: grab;
            user-select: none;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: box-shadow 0.15s;
        }
        .cork-postit:active {
            cursor: grabbing;
            box-shadow: 4px 10px 20px rgba(0,0,0,0.35);
        }
        .cork-postit.square { width: 140px; height: 140px; }
        .cork-postit.rect { width: 140px; height: 190px; }

        /* RAPTİYE */
        .postit-pin {
            position: absolute;
            top: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 12px;
            height: 12px;
            background: #e74c3c;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            border: 1px solid #c0392b;
            z-index: 5;
        }

        /* NOT METNİ & STICKER */
        .postit-text-content {
            font-size: 15px;
            line-height: 1.2;
            color: #1a3c11;
            word-wrap: break-word;
            pointer-events: none;
            transform-origin: center center;
        }
        .postit-sticker-img {
            position: absolute;
            bottom: 6px;
            right: 6px;
            max-width: 48px;
            max-height: 48px;
            object-fit: contain;
            pointer-events: none;
        }
        .postit-author {
            font-size: 11px;
            color: rgba(0,0,0,0.5);
            margin-top: 4px;
            align-self: flex-start;
        }
        .postit-delete-btn {
            display: none;
            position: absolute;
            top: 4px;
            right: 4px;
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
            z-index: 10;
        }
        .is-editing .postit-delete-btn {
            display: flex;
        }

        /* SAĞ/ALT: AHŞAP ANAHTARLIK ASKI ÇITASI */
        .keychain-rack {
            width: 110px;
            background: #9b6b3e;
            border: 4px solid #6b4421;
            border-radius: 14px;
            padding: 16px 8px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .hook-slot {
            width: 76px;
            height: 76px;
            background: rgba(0,0,0,0.15);
            border: 2px dashed #e8d0ab;
            border-radius: 12px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hook-pin {
            position: absolute;
            top: -6px;
            width: 8px;
            height: 8px;
            background: #silver;
            background: linear-gradient(135deg, #eee, #999);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }

        .keychain-item {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .keychain-item:hover {
            transform: scale(1.1) rotate(5deg);
        }
        .keychain-item svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(2px 4px 4px rgba(0,0,0,0.25));
        }

        /* ÇANTA / TÜM BAŞARIMLAR BUTONU */
        .btn-open-bag {
            width: 58px;
            height: 58px;
            background: #fdf5a6;
            border: 2px solid #5a7d3b;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            margin-top: 10px;
        }

        /* MODAL: POST-IT OLUŞTURUCU (MİNİ STÜDYO) */
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
            padding: 24px;
            width: 90%;
            max-width: 540px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .studio-columns {
            display: flex;
            gap: 20px;
        }
        .studio-tools {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .studio-preview {
            width: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .color-selector {
            display: flex;
            gap: 8px;
        }
        .color-ball {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid rgba(0,0,0,0.15);
            cursor: pointer;
        }
        .color-ball.selected {
            border: 2px solid #1a3c11;
            transform: scale(1.15);
        }

        /* BAŞARIM DETAY POPUP */
        .badge-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            border: 2.5px solid #5a7d3b;
            border-radius: 16px;
            padding: 20px;
            width: 280px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            z-index: 100001;
            display: none;
        }
        .badge-popup.active { display: block; }

        /* MOBİL UYARLAMA */
        @media (max-width: 1024px) {
            .board-page-container {
                padding: 0 10px 40px 10px;
            }
            .corkboard-main-wrapper {
                flex-direction: column;
                align-items: center;
            }
            .corkboard-frame {
                width: 100%;
                min-height: 440px;
                padding: 4%;
            }
            .keychain-rack {
                width: 100%;
                flex-direction: row;
                justify-content: flex-start;
                overflow-x: auto;
                padding: 10px;
            }
            .hook-slot {
                width: 58px;
                height: 58px;
                flex-shrink: 0;
            }
            .keychain-item {
                width: 48px;
                height: 48px;
            }
            .btn-open-bag {
                flex-shrink: 0;
                width: 48px;
                height: 48px;
                margin-top: 0;
            }
            .cork-postit {
                pointer-events: auto !important;
                touch-action: pan-y;
            }
            .desktop-only-action {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="board-page-container">
    <div class="board-header">
        <h2 class="board-title">📌 {{ $user->username ?? 'User' }}'s Cozy Board</h2>
        <div>
            @if(auth()->check() && auth()->id() === ($user->id ?? 0))
                <button id="toggleEditBtn" class="btn-action desktop-only-action">✏️ Edit Board</button>
            @elseif(auth()->check())
                <button id="openPostitModalBtn" class="btn-action">📌 Add Post-it</button>
            @endif
        </div>
    </div>

    <div class="corkboard-main-wrapper">
        <!-- 1. MANTAR PANO (pano.jpg) -->
        <div class="corkboard-frame" id="corkboardArea">
            <!-- Örnek Post-it 1 -->
            <div class="cork-postit square" style="top: 15%; left: 8%; background: #fdf5a6; transform: rotate(-3deg);">
                <span class="postit-pin"></span>
                <button class="postit-delete-btn" onclick="this.parentElement.remove()">✕</button>
                <div class="postit-text-content" style="font-size: 15px; transform: rotate(0deg);">
                    Kitaplar harika gidiyor! 📖
                </div>
                <div class="postit-author">@catlover</div>
            </div>

            <!-- Örnek Post-it 2 -->
            <div class="cork-postit rect" style="top: 25%; left: 45%; background: #ffd1dc; transform: rotate(4deg);">
                <span class="postit-pin"></span>
                <button class="postit-delete-btn" onclick="this.parentElement.remove()">✕</button>
                <div class="postit-text-content" style="font-size: 16px; transform: rotate(-2deg);">
                    Bu panoya bayıldım ^^
                </div>
                <div class="postit-author">@irmak</div>
            </div>
        </div>

        <!-- 2. SAĞDA/MOBİLDE ALTTA: ANAHTARLIK ASKI ÇITASI (6 SLOT + ÇANTA) -->
        <div class="keychain-rack">
            <div class="hook-slot"><span class="hook-pin"></span><div class="keychain-item" data-badge="tavsan" onclick="showBadgeDetails('tavsan')">{!! getBadgeSvg('tavsan') !!}</div></div>
            <div class="hook-slot"><span class="hook-pin"></span><div class="keychain-item" data-badge="kalp" onclick="showBadgeDetails('kalp')">{!! getBadgeSvg('kalp') !!}</div></div>
            <div class="hook-slot"><span class="hook-pin"></span><div class="keychain-item" data-badge="kaset" onclick="showBadgeDetails('kaset')">{!! getBadgeSvg('kaset') !!}</div></div>
            <div class="hook-slot"><span class="hook-pin"></span><div class="keychain-item" data-badge="kahve" onclick="showBadgeDetails('kahve')">{!! getBadgeSvg('kahve') !!}</div></div>
            <div class="hook-slot"><span class="hook-pin"></span><div class="keychain-item" data-badge="kedi" onclick="showBadgeDetails('kedi')">{!! getBadgeSvg('kedi') !!}</div></div>
            <div class="hook-slot"><span class="hook-pin"></span><div class="keychain-item" data-badge="yildiz" onclick="showBadgeDetails('yildiz')">{!! getBadgeSvg('yildiz') !!}</div></div>
            
            <button class="btn-open-bag" title="All Achievements" onclick="openAllBadgesModal()">📁</button>
        </div>
    </div>
</div>

<!-- 3. POST-IT MİNİ STÜDYO MODAL -->
<div class="modal-overlay" id="postitStudioModal">
    <div class="studio-modal-box">
        <h3 style="margin: 0; font-size: 18px;">✨ Create Your Note</h3>
        
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

                <textarea id="studioTextInput" placeholder="Write something cozy..." maxlength="120" style="width:100%; height:60px; font-family:'Unkempt'; padding:6px; resize:none;"></textarea>

                <div style="font-size: 12px; display:flex; flex-direction:column; gap:4px;">
                    <label>Text Size: <input type="range" id="studioSizeRange" min="12" max="24" value="15"></label>
                    <label>Rotate: <input type="range" id="studioRotateRange" min="-25" max="25" value="0">°</label>
                </div>

                <label style="background:#eef6ea; border:1px solid #5a7d3b; padding:5px; text-align:center; border-radius:6px; font-size:12px; cursor:pointer;">
                    PNG Sticker?
                    <input type="file" id="studioFileInput" accept="image/png" style="display:none;">
                </label>
            </div>

            <div class="studio-preview">
                <div class="cork-postit square" id="previewPostitBox" style="position:relative; background:#fdf5a6; width:130px; height:130px;">
                    <span class="postit-pin"></span>
                    <div id="previewTextLayer" class="postit-text-content">Hello!</div>
                    <img id="previewStickerLayer" class="postit-sticker-img" style="display:none;">
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn-action" style="background:#ddd;" onclick="closePostitModal()">Cancel</button>
            <button type="button" class="btn-action" onclick="pinNoteToBoard()">OK!</button>
        </div>
    </div>
</div>

<!-- 4. BAŞARIM DETAY POPUP -->
<div class="badge-popup" id="badgePopupBox">
    <div id="badgePopupIcon" style="width:60px; height:60px; margin:0 auto 10px auto;"></div>
    <h4 id="badgePopupTitle" style="margin: 0 0 6px 0;"></h4>
    <p id="badgePopupDesc" style="font-size: 13px; color: #555; margin: 0 0 14px 0;"></p>
    <button class="btn-action" style="padding:4px 12px; font-size:13px;" onclick="closeBadgeDetails()">Close</button>
</div>

<!-- 15 ADET HAZIR BAŞARIM LİSTESİ & SVG RENDER -->
@php
function getBadgesList() {
    return [
        'tavsan' => ['title' => '10+ Friends!', 'desc' => '10 arkadaş edinerek peluş tavşan anahtarlığı kazandın.', 'color' => '#b5e2fa'],
        'kalp'   => ['title' => 'Top Reviewer', 'desc' => 'İncelemelerine 20+ beğeni geldiği için örgü kalp kazandın.', 'color' => '#ffd1dc'],
        'kaset'  => ['title' => 'Sci-Fi Explorer', 'desc' => '5 bilim kurgu kitabı bitirdiğin için nostaljik kaset verildi.', 'color' => '#dfccf1'],
        'kahve'  => ['title' => 'Classic Reader', 'desc' => 'Klasik eserleri tüketenlere özel sıcak kahve fincanı.', 'color' => '#fdf5a6'],
        'kedi'   => ['title' => 'Night Owl', 'desc' => 'Gece yarısı kitap kaydeden sevimli gece kedisi.', 'color' => '#badfa0'],
        'yildiz' => ['title' => 'Book Worm', 'desc' => 'Tek oturuşta 100 sayfa deviren parlayan yıldız.', 'color' => '#fee16c'],
        'kupa'   => ['title' => 'Speed Reader', 'desc' => 'Aynı haftada 2 kitap bitiren şampiyonlara özel mini kupa.', 'color' => '#ffd1dc'],
        'kitap'  => ['title' => 'First Step', 'desc' => 'İlk kitabını rafa eklediğin için aldığın ahşap kitaplık.', 'color' => '#c6e085'],
        'mektup' => ['title' => 'Friendly Postman', 'desc' => 'Arkadaşlarının panolarına 5 post-it bırakan usta postacı.', 'color' => '#b5e2fa'],
        'ayicik' => ['title' => 'Warm Home', 'desc' => 'Panosuna 10 not iliştirilen sıcak yuva sahibi.', 'color' => '#fdf5a6'],
        'cicek'  => ['title' => 'Spring Blossom', 'desc' => 'Profilini ve bio alanını eksiksiz dolduran çiçek gibi okur.', 'color' => '#ffd1dc'],
        'mantar' => ['title' => 'Fantasy Lover', 'desc' => 'Büyülü dünyalara dalan fantastik edebiyat hayranı.', 'color' => '#dfccf1'],
        'kamera' => ['title' => 'Aesthetic Soul', 'desc' => 'Kendi özel avatarını yükleyen görsel ruha polaroid kamera.', 'color' => '#badfa0'],
        'pasta'  => ['title' => 'Book Birthday', 'desc' => 'Bookie’de 1. ayını dolduranlara leziz çilekli pasta.', 'color' => '#fee16c'],
        'anahtar'=> ['title' => 'Secret Keeper', 'desc' => 'Gizli easter egg’leri bulan meraklı dedektif anahtarı.', 'color' => '#c29b62']
    ];
}

function getBadgeSvg($key) {
    $badges = getBadgesList();
    $badge = $badges[$key] ?? ['color' => '#badfa0'];
    $c = $badge['color'];
    return '
    <svg viewBox="0 0 100 100">
        <circle cx="50" cy="20" r="10" fill="none" stroke="#666" stroke-width="6"/>
        <rect x="25" y="32" width="50" height="56" rx="16" fill="'.$c.'" stroke="#4b813b" stroke-width="4"/>
        <circle cx="42" cy="56" r="4" fill="#2d5a27"/>
        <circle cx="58" cy="56" r="4" fill="#2d5a27"/>
        <path d="M 46 64 Q 50 68 54 64" fill="none" stroke="#2d5a27" stroke-width="3" stroke-linecap="round"/>
    </svg>';
}
@endphp

<script>
    const BADGES_DATA = @json(getBadgesList());

    // --- DÜZENLEME MODU (DRAG & DROP SADECE MASAÜSTÜNDE) ---
    let isEditing = false;
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const corkboard = document.getElementById('corkboardArea');

    if (toggleEditBtn) {
        toggleEditBtn.addEventListener('click', function() {
            isEditing = !isEditing;
            if (isEditing) {
                this.innerText = '💾 Save Board';
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
            item.onmousedown = startDrag;
        });
    }

    function disableDragging() {
        document.querySelectorAll('.cork-postit').forEach(item => {
            item.onmousedown = null;
        });
    }

    function startDrag(e) {
        if (!isEditing) return;
        activeDragItem = this;
        const rect = activeDragItem.getBoundingClientRect();
        dragOffsetX = e.clientX - rect.left;
        dragOffsetY = e.clientY - rect.top;
        document.onmousemove = doDrag;
        document.onmouseup = stopDrag;
    }

    function doDrag(e) {
        if (!activeDragItem) return;
        const boardRect = corkboard.getBoundingClientRect();
        let left = e.clientX - boardRect.left - dragOffsetX;
        let top = e.clientY - boardRect.top - dragOffsetY;

        left = Math.max(0, Math.min(boardRect.width - activeDragItem.offsetWidth, left));
        top = Math.max(0, Math.min(boardRect.height - activeDragItem.offsetHeight, top));

        activeDragItem.style.left = (left / boardRect.width * 100) + '%';
        activeDragItem.style.top = (top / boardRect.height * 100) + '%';
    }

    function stopDrag() {
        activeDragItem = null;
        document.onmousemove = null;
        document.onmouseup = null;
    }

    // --- POST-IT OLUŞTURUCU MİNİ STÜDYO ETKİLEŞİMİ ---
    const modal = document.getElementById('postitStudioModal');
    const openModalBtn = document.getElementById('openPostitModalBtn');
    const textInput = document.getElementById('studioTextInput');
    const sizeRange = document.getElementById('studioSizeRange');
    const rotateRange = document.getElementById('studioRotateRange');
    const shapeSelect = document.getElementById('studioShapeSelect');
    const fileInput = document.getElementById('studioFileInput');

    const previewBox = document.getElementById('previewPostitBox');
    const previewText = document.getElementById('previewTextLayer');
    const previewSticker = document.getElementById('previewStickerLayer');

    let selectedColor = '#fdf5a6';
    let loadedStickerSrc = null;

    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => modal.classList.add('active'));
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

    function updatePreviewTransform() {
        previewText.style.fontSize = sizeRange.value + 'px';
        previewText.style.transform = `rotate(${rotateRange.value}deg)`;
    }
    sizeRange.addEventListener('input', updatePreviewTransform);
    rotateRange.addEventListener('input', updatePreviewTransform);

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.type === 'image/png') {
            const reader = new FileReader();
            reader.onload = (e) => {
                loadedStickerSrc = e.target.result;
                previewSticker.src = loadedStickerSrc;
                previewSticker.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    function pinNoteToBoard() {
        const shape = shapeSelect.value;
        const text = textInput.value.trim() || 'Cozy note!';
        const fSize = sizeRange.value;
        const rot = rotateRange.value;

        const newNote = document.createElement('div');
        newNote.className = `cork-postit ${shape}`;
        newNote.style.backgroundColor = selectedColor;
        newNote.style.top = '30%';
        newNote.style.left = '30%';
        newNote.style.transform = `rotate(${Math.floor(Math.random() * 8 - 4)}deg)`;

        let stickerHtml = loadedStickerSrc ? `<img src="${loadedStickerSrc}" class="postit-sticker-img">` : '';

        newNote.innerHTML = `
            <span class="postit-pin"></span>
            <button class="postit-delete-btn" onclick="this.parentElement.remove()">✕</button>
            <div class="postit-text-content" style="font-size:${fSize}px; transform:rotate(${rot}deg);">${text}</div>
            ${stickerHtml}
            <div class="postit-author">@{{ auth()->user()->username ?? 'me' }}</div>
        `;

        corkboard.appendChild(newNote);
        closePostitModal();

        textInput.value = '';
        loadedStickerSrc = null;
        previewSticker.style.display = 'none';
        previewText.innerText = 'Hello!';
    }

    // --- BAŞARIM POPUP DETAYLARI ---
    const badgePopup = document.getElementById('badgePopupBox');
    const badgeIcon = document.getElementById('badgePopupIcon');
    const badgeTitle = document.getElementById('badgePopupTitle');
    const badgeDesc = document.getElementById('badgePopupDesc');

    function showBadgeDetails(key) {
        const badge = BADGES_DATA[key];
        if (!badge) return;

        badgeTitle.innerText = badge.title;
        badgeDesc.innerText = badge.desc;
        const item = document.querySelector(`[data-badge="${key}"]`);
        badgeIcon.innerHTML = item ? item.innerHTML : '';
        badgePopup.classList.add('active');
    }

    function closeBadgeDetails() {
        badgePopup.classList.remove('active');
    }

    function openAllBadgesModal() {
        alert('Tüm başarımlar çantası: Buradan açık olan 15 başarımını kancalara sürükleyebilirsin!');
    }
</script>

</body>
</html>