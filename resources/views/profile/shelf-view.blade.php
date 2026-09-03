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
            max-width: 1100px;
            margin: 20px auto;
            padding: 0 16px 80px 16px;
        }

        /* ÇİZİMDEKİ GİBİ: PANO ORTADA, SAĞINDA ASKILIK */
        .corkboard-main-wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 24px;
            width: 100%;
            margin-top: 10px;
        }

        /* YATAY ORANINI ASLA BOZMAYAN VE EZİLMEYEN PANO */
        .corkboard-frame {
            flex: 1;
            max-width: 780px;
            width: 100%;
            aspect-ratio: 16 / 10; /* Yatay oranı kilitler, dikeyde sünmeyi engeller */
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

        /* PANO ALTINDAKİ MERKEZİ BUTON ALANI (Çizimdeki Add Post-it / Edit Butonu) */
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
            padding: 8px 24px;
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
            padding: 12px 10px 10px 10px;
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
            box-shadow: 4px 8px 18px rgba(0,0,0,0.35);
        }
        .cork-postit.square { width: 115px; height: 115px; }
        .cork-postit.rect { width: 115px; height: 155px; }

        /* RAPTİYE */
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
            z-index: 5;
        }

        .postit-text-content {
            font-size: 13px;
            line-height: 1.25;
            color: #1a3c11;
            word-wrap: break-word;
            pointer-events: none;
            transform-origin: center center;
        }
        .postit-sticker-img {
            position: absolute;
            bottom: 4px;
            right: 4px;
            max-width: 40px;
            max-height: 40px;
            object-fit: contain;
            pointer-events: none;
        }
        .postit-author {
            font-size: 10px;
            color: rgba(0,0,0,0.5);
            margin-top: 2px;
            align-self: flex-start;
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
            z-index: 10;
        }
        .is-editing .postit-delete-btn {
            display: flex;
        }

        /* SAĞDAKİ DİKEY AHŞAP ASKI ÇITASI */
        .keychain-rack {
            width: 78px;
            background: #8b5a2b;
            border: 3px solid #5c3a1e;
            border-radius: 14px;
            padding: 12px 6px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .hook-slot {
            width: 58px;
            height: 58px;
            background: rgba(0,0,0,0.18);
            border: 1.5px dashed #f5deb3;
            border-radius: 10px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hook-pin {
            position: absolute;
            top: -5px;
            width: 7px;
            height: 7px;
            background: linear-gradient(135deg, #eee, #999);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }

        .keychain-item {
            width: 46px;
            height: 46px;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .keychain-item:hover {
            transform: scale(1.12) rotate(6deg);
        }
        .keychain-item svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(2px 3px 3px rgba(0,0,0,0.25));
        }

        .btn-open-bag {
            width: 48px;
            height: 48px;
            background: #fdf5a6;
            border: 2px solid #5a7d3b;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
            margin-top: 4px;
        }

        /* MODAL: POST-IT OLUŞTURUCU (Çizimdeki Sol Pop-up) */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
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
            width: 90%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        .studio-columns {
            display: flex;
            gap: 16px;
        }
        .studio-tools {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .studio-preview {
            width: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* BAŞARIM DETAY POPUP */
        .badge-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            border: 2.5px solid #5a7d3b;
            border-radius: 16px;
            padding: 18px;
            width: 260px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            z-index: 100001;
            display: none;
        }
        .badge-popup.active { display: block; }

        /* MOBİL UYARLAMA (Masaüstü düzeni bozulmadan kanca alta kayar) */
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
            .keychain-rack {
                width: 100%;
                max-width: 100%;
                flex-direction: row;
                justify-content: flex-start;
                overflow-x: auto;
                padding: 8px 12px;
            }
            .hook-slot {
                width: 50px;
                height: 50px;
                flex-shrink: 0;
            }
            .keychain-item {
                width: 40px;
                height: 40px;
            }
            .btn-open-bag {
                flex-shrink: 0;
                width: 42px;
                height: 42px;
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

    <div class="corkboard-main-wrapper">
        
        <div class="corkboard-frame" id="corkboardArea">
            <div class="cork-postit square" style="top: 20%; left: 10%; background: #fdf5a6; transform: rotate(-3deg);">
                <span class="postit-pin"></span>
                <button class="postit-delete-btn" onclick="this.parentElement.remove()">✕</button>
                <div class="postit-text-content" style="font-size: 13px; transform: rotate(0deg);">
                    Kitaplar harika gidiyor! 📖
                </div>
                <div class="postit-author">@catlover</div>
            </div>

            <div class="cork-postit rect" style="top: 25%; left: 55%; background: #ffd1dc; transform: rotate(4deg);">
                <span class="postit-pin"></span>
                <button class="postit-delete-btn" onclick="this.parentElement.remove()">✕</button>
                <div class="postit-text-content" style="font-size: 14px; transform: rotate(-2deg);">
                    Bu panoya bayıldım ^^
                </div>
                <div class="postit-author">@irmak</div>
            </div>
        </div>

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

    <div class="board-bottom-bar">
        @if(auth()->check() && auth()->id() === ($user->id ?? 0))
            <button id="toggleEditBtn" class="btn-action desktop-only-action">✏️ Edit Board</button>
        @elseif(auth()->check())
            <button id="openPostitModalBtn" class="btn-action">📌 ADD POST-IT</button>
        @endif
    </div>

</div>

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

                <textarea id="studioTextInput" placeholder="Write something cozy..." maxlength="120" style="width:100%; height:54px; font-family:'Unkempt'; padding:6px; resize:none;"></textarea>

                <div style="font-size: 12px; display:flex; flex-direction:column; gap:3px;">
                    <label>Text Size: <input type="range" id="studioSizeRange" min="11" max="22" value="13"></label>
                    <label>Rotate: <input type="range" id="studioRotateRange" min="-25" max="25" value="0">°</label>
                </div>

                <label style="background:#eef6ea; border:1px solid #5a7d3b; padding:5px; text-align:center; border-radius:6px; font-size:12px; cursor:pointer;">
                    PNG Sticker?
                    <input type="file" id="studioFileInput" accept="image/png" style="display:none;">
                </label>
            </div>

            <div class="studio-preview">
                <div class="cork-postit square" id="previewPostitBox" style="position:relative; background:#fdf5a6; width:115px; height:115px;">
                    <span class="postit-pin"></span>
                    <div id="previewTextLayer" class="postit-text-content">Hello!</div>
                    <img id="previewStickerLayer" class="postit-sticker-img" style="display:none;">
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn-action" style="background:#ddd; padding:6px 16px; font-size:14px;" onclick="closePostitModal()">Cancel</button>
            <button type="button" class="btn-action" style="padding:6px 18px; font-size:14px;" onclick="pinNoteToBoard()">OK!</button>
        </div>
    </div>
</div>

<div class="badge-popup" id="badgePopupBox">
    <div id="badgePopupIcon" style="width:50px; height:50px; margin:0 auto 8px auto;"></div>
    <h4 id="badgePopupTitle" style="margin: 0 0 6px 0; font-size:15px;"></h4>
    <p id="badgePopupDesc" style="font-size: 12px; color: #555; margin: 0 0 12px 0;"></p>
    <button class="btn-action" style="padding:4px 14px; font-size:12px;" onclick="closeBadgeDetails()">Close</button>
</div>

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
        <circle cx="50" cy="18" r="9" fill="none" stroke="#666" stroke-width="5"/>
        <rect x="25" y="30" width="50" height="58" rx="14" fill="'.$c.'" stroke="#4b813b" stroke-width="4"/>
        <circle cx="42" cy="54" r="4" fill="#2d5a27"/>
        <circle cx="58" cy="54" r="4" fill="#2d5a27"/>
        <path d="M 46 62 Q 50 66 54 62" fill="none" stroke="#2d5a27" stroke-width="3" stroke-linecap="round"/>
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

    // --- POST-IT STÜDYO ETKİLEŞİMİ ---
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
        newNote.style.top = '25%';
        newNote.style.left = '35%';
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
        alert('Tüm başarımlar çantası: Açık olan başarımlarını kancalara sürükleyebilirsin!');
    }
</script>

</body>
</html>