<style>
    @font-face {
        font-family: 'Unkempt';
        src: url('{{ asset('fonts/Unkempt-Regular.ttf') }}') format('truetype');
        font-weight: 400;
        font-display: swap;
    }

    .board-page-container {
        width: 100%;
        max-width: 1180px;
        margin: 15px auto;
        padding: 0 16px 80px 16px;
        position: relative;
        font-family: 'Unkempt', cursive;
        color: #1b3711;
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

    .board-lock-badge {
        position: absolute;
        top: 14px;
        right: 16px;
        width: 50px;
        height: 50px;
        z-index: 10000;
        cursor: pointer;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
        transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .board-lock-badge:hover { transform: scale(1.15); }
    .board-lock-badge img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        user-select: none;
        -webkit-user-drag: none;
    }

    .board-bottom-bar {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 16px;
        width: 100%;
    }

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
        font-weight: bold;
    }
    .btn-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        filter: grayscale(60%);
    }

    .cork-postit {
        position: absolute;
        cursor: default;
        user-select: none;
        touch-action: none;
        transform-origin: center center;
    }
    .is-editing-active .cork-postit,
    .cork-postit.can-delete { cursor: grab; }
    .is-editing-active .cork-postit:active,
    .cork-postit.can-delete:active { cursor: grabbing; }

    .postit-inner-card {
        position: relative;
        box-shadow: 2px 5px 14px rgba(0,0,0,0.2);
        padding: 8px;
        overflow: hidden;
        border-radius: 2px;
    }
    .postit-inner-card.size-square { width: 180px; height: 180px; }
    .postit-inner-card.size-portrait { width: 160px; height: 220px; }
    .postit-inner-card.size-landscape { width: 220px; height: 160px; }

    .cork-postit.is-selected .postit-inner-card {
        outline: 2px dashed #2d5a27;
        outline-offset: 4px;
    }

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
        z-index: 20;
        pointer-events: none;
    }

    #corkboardArea .postit-inner-card .transform-box {
        border-color: transparent !important;
        cursor: default !important;
        pointer-events: none !important;
    }
    #corkboardArea .postit-inner-card .postit-text-content {
        pointer-events: none !important;
        user-select: none !important;
    }
    #corkboardArea .postit-inner-card .postit-sticker-img {
        pointer-events: none !important;
        user-select: none !important;
        -webkit-user-drag: none !important;
        object-fit: contain;
        width: 100%;
        height: 100%;
    }

    .postit-author {
        position: absolute;
        bottom: 4px;
        left: 6px;
        font-size: 11px;
        color: rgba(0,0,0,0.55);
        pointer-events: none;
        z-index: 10;
    }

    .free-sticker-wrapper {
        position: absolute;
        cursor: default;
        user-select: none;
        touch-action: none;
        display: inline-block;
        transform-origin: center center;
    }
    .is-editing-active .free-sticker-wrapper { cursor: grab; }
    .is-editing-active .free-sticker-wrapper:active { cursor: grabbing; }
    .is-editing-active .free-sticker-wrapper.is-selected {
        outline: 2px dashed #2d5a27;
        outline-offset: 4px;
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

    .handle-btn {
        display: none;
        position: absolute;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 1.5px solid #ffffff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.25);
        z-index: 60;
    }
    .transform-box.is-selected .handle-btn,
    .is-editing-active .free-sticker-wrapper.is-selected .handle-btn,
    .cork-postit.can-delete.is-selected .handle-btn {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .handle-delete { top: -10px; left: -10px; background: #ff7675; color: white; font-size: 11px; cursor: pointer; }
    .handle-rotate { top: -16px; left: 50%; transform: translateX(-50%); background: #fab1a0; color: #2d3436; font-size: 11px; cursor: grab; }
    .handle-resize { bottom: -10px; right: -10px; background: #74b9ff; color: white; font-size: 11px; cursor: nwse-resize; }

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
        display: flex !important;
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
    .modal-overlay.active { display: flex !important; }

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

    .studio-columns { display: flex; gap: 24px; align-items: center; }
    .studio-tools { flex: 1; display: flex; flex-direction: column; gap: 12px; }

    .studio-preview {
        width: 250px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

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
    .studio-input-box:focus { border-color: #5a7d3b; }

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
        display: block;
    }
    .btn-gentle-upload:hover { background: #e5f1df; }

    .transform-box {
        position: absolute;
        cursor: move;
        border: 1px dashed transparent;
        touch-action: none;
        display: inline-block;
    }
    .transform-box.is-selected { border-color: #2d5a27; }

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

@php
    $authId = auth()->id();
    $targetId = $user->id ?? 0;
    $isOwnProfile = auth()->check() && ($authId === $targetId);

    $isFriendUser = false;
    if (auth()->check() && !$isOwnProfile) {
        $isFriendUser = \DB::table('friendships')
            ->where('status', 'accepted')
            ->where(function ($q) use ($authId, $targetId) {
                $q->where(function($sub) use ($authId, $targetId) {
                    $sub->where('user_id', $authId)->where('friend_id', $targetId);
                })->orWhere(function($sub) use ($authId, $targetId) {
                    $sub->where('user_id', $targetId)->where('friend_id', $authId);
                });
            })
            ->exists();
    }

    $currentBoard = $board ?? \App\Models\UserBoard::firstOrCreate(['user_id' => $targetId]);

    $isBoardLocked = $currentBoard->is_locked ?? false;
    $canAddPostit = $isOwnProfile || ($isFriendUser && !$isBoardLocked);

    $rawItems = $currentBoard->board_items ?? [];
    while (is_string($rawItems)) {
        $rawItems = json_decode($rawItems, true);
    }
    $boardItems = is_array($rawItems) ? $rawItems : [];

    $rawHooks = $currentBoard->hook_slots ?? [];
    while (is_string($rawHooks)) {
        $rawHooks = json_decode($rawHooks, true);
    }
    $hookSlots = is_array($rawHooks) ? $rawHooks : array_fill(0, 9, null);

    $achievements = $achievements ?? [
        'ask'       => ['title' => 'Aşk',       'file' => 'aşk.png',       'unlocked' => true],
        'ayicik'    => ['title' => 'Ayıcık',    'file' => 'ayıcık.png',    'unlocked' => true],
        'burger'    => ['title' => 'Burger',    'file' => 'burger.png',    'unlocked' => false],
        'cilek'     => ['title' => 'Çilek',     'file' => 'çilek.png',     'unlocked' => true],
        'elma'      => ['title' => 'Elma',      'file' => 'elma.png',      'unlocked' => false],
        'geyik'     => ['title' => 'Geyik',     'file' => 'geyik.png',     'unlocked' => false],
        'jake'      => ['title' => 'Jake',      'file' => 'jake.png',      'unlocked' => true],
        'kedi'      => ['title' => 'Kedi',      'file' => 'kedi.png',      'unlocked' => false],
        'kitap'     => ['title' => 'Kitap',     'file' => 'kitap.png',     'unlocked' => true],
        'kruvasan'  => ['title' => 'Kruvasan',  'file' => 'kruvasan.png',  'unlocked' => false],
        'maymun'    => ['title' => 'Maymun',    'file' => 'maymun.png',    'unlocked' => true],
        'tama'      => ['title' => 'Tama',      'file' => 'tama.png',      'unlocked' => false],
        'usagi'     => ['title' => 'Usagi',     'file' => 'usagi.png',     'unlocked' => true],
        'yengec'    => ['title' => 'Yengeç',    'file' => 'yengeç.png',    'unlocked' => false],
        'yonca'     => ['title' => 'Yonca',     'file' => 'yonca.png',     'unlocked' => false],
    ];
@endphp

<div class="board-page-container">

    <div class="corkboard-main-wrapper">
        <div class="corkboard-frame" id="corkboardArea">
            <div class="board-lock-badge" id="boardLockBtn" 
                 title="{{ $isOwnProfile ? 'Ziyaretçilere Kilitle / Aç' : ($isBoardLocked ? 'Pano Kilitli' : 'Pano Açık') }}" 
                 style="{{ (!$isOwnProfile && !$isBoardLocked) ? 'display:none;' : '' }}">
                <img id="boardLockImg" 
                     src="{{ $isBoardLocked ? asset('images/locke.png') : asset('images/unlocked.png') }}" 
                     alt="Lock Status" 
                     onerror="this.onerror=null; this.src='{{ asset('images/lock.png') }}';">
            </div>

            @foreach($boardItems as $item)
                @if(($item['type'] ?? '') === 'postit')
                    @php
                        $loggedUsername = auth()->user()->username ?? '';
                        $authorText = $item['author'] ?? '';

                        $normalize = function($str) {
                            $str = str_replace(['I', 'İ'], ['ı', 'i'], $str);
                            return mb_strtolower($str, 'UTF-8');
                        };

                        $cleanLogged = $normalize($loggedUsername);
                        $cleanAuthor = $normalize($authorText);

                        $isAuthor = !empty($cleanLogged) && (
                            str_contains($cleanAuthor, '@' . $cleanLogged) || 
                            str_contains($cleanAuthor, $cleanLogged)
                        );

                        $canManage = $isOwnProfile || ($isAuthor && !$isBoardLocked);
                        $scale = $item['scale'] ?? 0.65;
                        $rot = $item['rotation'] ?? 0;
                    @endphp
                    <div class="cork-postit {{ $canManage ? 'can-delete' : '' }}" 
                        style="top: {{ $item['top'] }}; left: {{ $item['left'] }}; z-index: {{ $item['zIndex'] ?? 10 }}; transform: scale({{ $scale }}) rotate({{ $rot }}deg);"
                        data-scale="{{ $scale }}"
                        data-rotation="{{ $rot }}"
                        data-can-manage="{{ $canManage ? '1' : '0' }}">
                        <div class="postit-inner-card {{ $item['shapeClass'] ?? 'size-square' }}" style="background-color: {{ $item['bg'] }};">
                            {!! $item['html'] !!}
                        </div>
                        @if($canManage)
                            <div class="handle-btn handle-delete postit-delete-btn" title="Sil">✕</div>
                            <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                            <div class="handle-btn handle-resize" title="Post-it'i Büyüt / Küçült">⤡</div>
                        @endif
                    </div>
                @elseif(($item['type'] ?? '') === 'free_sticker')
                    <div class="free-sticker-wrapper" 
                         style="top: {{ $item['top'] }}; left: {{ $item['left'] }}; width: {{ $item['width'] ?? '80px' }}; height: {{ $item['height'] ?? '80px' }}; transform: {{ $item['transform'] ?? 'rotate(0deg)' }}; z-index: {{ $item['zIndex'] ?? 10 }};">
                        <img src="{{ $item['src'] }}">
                        @if($isOwnProfile)
                            <div class="handle-btn handle-delete" title="Sil">✕</div>
                            <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                            <div class="handle-btn handle-resize" title="Büyüt / Küçült">⤡</div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <div class="keychain-area-wrapper">
            <div class="keychain-grid-9" id="keychainHooksGrid">
                @for($slot = 1; $slot <= 9; $slot++)
                    @php
                        $key = $hookSlots[$slot - 1] ?? null;
                        $badge = ($key && isset($achievements[$key])) ? $achievements[$key] : null;
                    @endphp
                    <div class="keychain-hook-unit" data-slot="{{ $slot }}" onclick="handleHookSlotClick({{ $slot }})">
                        <div class="hook-nail"></div>
                        @if($badge)
                            <img src="{{ asset('images/badges/' . $badge['file']) }}" 
                                 class="keychain-plush-img" 
                                 data-key="{{ $key }}" 
                                 title="{{ $badge['title'] }}"
                                 onerror="this.src='{{ asset('images/badges/maymun.png') }}';">
                        @else
                            <div class="empty-hook-slot" title="{{ $isOwnProfile ? 'Boş kanca' : '' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>

            @if($isOwnProfile)
            <div class="folder-container" onclick="toggleCollectionDrawer()" title="All Keychains">
                <img src="{{ asset('images/dosya.png') }}" alt="Folder" class="folder-img" onerror="this.onerror=null; this.src='{{ asset('images/folder.png') }}';">
                <span class="folder-label">all keychains u own</span>
            </div>
            @endif
        </div>
    </div>

    <!-- ALT BUTON BARI -->
    <div class="board-bottom-bar">
        @if($isOwnProfile)
            <button type="button" id="toggleEditBtn" class="btn-action desktop-only-action">✏️ Edit Board</button>
            <button type="button" id="btnAddStickerBtn" class="btn-action" style="display:none;">🖼️ ADD STICKER</button>
            <input type="file" id="freeStickerUploadInput" accept="image/png, image/jpeg, image/jpg, image/webp" style="display:none;">
        @endif

        @if($canAddPostit)
            <button type="button" id="openPostitModalBtn" class="btn-action" onclick="openPostitModal()">📌 ADD POST-IT</button>
            @if(!$isOwnProfile)
                <button type="button" id="friendSaveBtn" class="btn-action saving" style="display:none;" onclick="saveBoardToDatabase(this)">💾 SAVE</button>
            @endif
        @elseif($isBoardLocked)
            <button type="button" class="btn-action" disabled>🔒 BOARD LOCKED</button>
        @elseif(auth()->check() && !$isOwnProfile && !$isFriendUser)
            <button type="button" class="btn-action" disabled title="Bu panoya yalnızca arkadaşlar not bırakabilir.">👥 SADECE ARKADAŞLAR</button>
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

<!-- POST-IT MİNİ STÜDYO MODAL -->
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
                    Resim / Sticker Ekle
                    <input type="file" id="studioFileInput" accept="image/png, image/jpeg, image/jpg, image/webp" style="display:none;">
                </label>
            </div>

            <div class="studio-preview">
                <div class="postit-inner-card size-square" id="previewPostitBox" style="background:#fdf5a6; position: relative;">
                    <span class="postit-pin"></span>
                    
                    <!-- Metin Kutusu (Başlangıçta seçili olan tek katman) -->
                    <div id="textTransformBox" class="transform-box is-selected" style="top:25px; left:16px; position: absolute; z-index: 10;">
                        <div class="postit-text-content" id="previewTextLayer" style="position:relative; font-size:18px; white-space:nowrap; user-select: none;">selam</div>
                        <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                        <div class="handle-btn handle-resize" title="Büyüt / Küçült">⤡</div>
                    </div>

                    <!-- Sticker Kutusu (Resim seçilene kadar KESİNLİKLE gizli) -->
                    <div id="stickerTransformBox" class="transform-box" style="display: none; top:55px; left:35px; width:70px; height:auto; position: absolute; z-index: 11;">
                        <img id="previewStickerLayer" class="postit-sticker-img" src="" style="width:100%; height:auto; display:block; pointer-events: none;">
                        <div class="handle-btn handle-delete" id="btnDeleteSticker" title="Sil">✕</div>
                        <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                        <div class="handle-btn handle-resize" title="Büyüt">⤡</div>
                    </div>

                    <div class="postit-author" id="previewAuthorLabel" style="position: absolute; bottom: 4px; left: 6px; font-size: 11px;"></div>
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

<script>
(function() {
    const CURRENT_USER = @json(auth()->user()->username ?? (auth()->user()->name ?? 'reader'));
    const IS_OWN = @json($isOwnProfile);
    let boardLocked = @json($isBoardLocked);

    const LOCK_ICON = '{{ asset("images/locke.png") }}';
    const UNLOCK_ICON = '{{ asset("images/unlocked.png") }}';
    const SAVE_URL = @json(route('board.save', $user->id ?? auth()->id()));
    const CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    const ACHIEVEMENTS = @json($achievements ?? []);
    const FALLBACK_SVG = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="18" r="8" fill="none" stroke="%23888" stroke-width="4"/><rect x="25" y="32" width="50" height="56" rx="14" fill="%23badfa0" stroke="%234b813b" stroke-width="3"/><circle cx="42" cy="54" r="4" fill="%232d5a27"/><circle cx="58" cy="54" r="4" fill="%232d5a27"/><path d="M 45 64 Q 50 68 55 64" fill="none" stroke="%232d5a27" stroke-width="3" stroke-linecap="round"/></svg>`;

    const corkboard = document.getElementById('corkboardArea');
    let isEditingMode = false;
    let maxZ = 100;
    let stickerRatio = 1;
    let chosenColor = '#fdf5a6';
    let chosenShape = 'size-square';

    function bringToFront(el) {
        maxZ++;
        el.style.zIndex = maxZ;
    }

    function showFriendSave() {
        if (!IS_OWN) {
            const btn = document.getElementById('friendSaveBtn');
            if (btn) btn.style.display = 'inline-block';
        }
    }

    // --- MODAL İŞLEMLERİ ---
    const modal = document.getElementById('postitStudioModal');
    const textInput = document.getElementById('studioTextInput');
    const previewText = document.getElementById('previewTextLayer');
    const previewBox = document.getElementById('previewPostitBox');
    const stickerBox = document.getElementById('stickerTransformBox');
    const textTransformBox = document.getElementById('textTransformBox');
    const previewSticker = document.getElementById('previewStickerLayer');
    const fileInput = document.getElementById('studioFileInput');
    const delStickerBtn = document.getElementById('btnDeleteSticker');

    window.openPostitModal = function() {
        if (!modal) return;
        const authorLbl = document.getElementById('previewAuthorLabel');
        if (authorLbl) authorLbl.innerText = '@' + CURRENT_USER;

        // Her açılışta temizle ve sıfırla
        if (textInput) textInput.value = '';
        if (previewText) previewText.innerText = 'selam';
        if (textTransformBox) {
            textTransformBox.style.width = 'fit-content';
            textTransformBox.classList.add('is-selected');
        }
        if (stickerBox) {
            stickerBox.style.display = 'none';
            stickerBox.classList.remove('is-selected');
        }
        if (previewSticker) previewSticker.src = '';
        if (fileInput) fileInput.value = '';

        modal.classList.add('active');
    };

    window.closePostitModal = function() {
        if (modal) modal.classList.remove('active');
    };

    // Yazı yazıldıkça önizlemeyi anlık güncelleme
    if (textInput && previewText) {
        textInput.addEventListener('input', function() {
            previewText.innerText = this.value.trim() !== '' ? this.value : 'selam';
            if (textTransformBox) textTransformBox.style.width = 'fit-content';
        });
    }

    // Renk seçimi
    document.querySelectorAll('.color-ball').forEach(b => {
        b.addEventListener('click', function() {
            document.querySelectorAll('.color-ball').forEach(x => x.classList.remove('selected'));
            this.classList.add('selected');
            chosenColor = this.getAttribute('data-c');
            if (previewBox) previewBox.style.backgroundColor = chosenColor;
        });
    });

    // Şekil seçimi
    document.querySelectorAll('.shape-btn').forEach(b => {
        b.addEventListener('click', function() {
            document.querySelectorAll('.shape-btn').forEach(x => x.classList.remove('active'));
            this.classList.add('active');
            chosenShape = this.getAttribute('data-shape');
            if (previewBox) previewBox.className = 'postit-inner-card ' + chosenShape;
        });
    });

    // Sticker Yükleme
    if (fileInput && previewSticker && stickerBox) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    stickerRatio = img.naturalHeight / img.naturalWidth;
                    const w = 70;
                    stickerBox.style.width = w + 'px';
                    stickerBox.style.height = (w * stickerRatio) + 'px';
                    previewSticker.src = e.target.result;
                    stickerBox.style.display = 'block';

                    document.querySelectorAll('#postitStudioModal .transform-box').forEach(b => b.classList.remove('is-selected'));
                    stickerBox.classList.add('is-selected');
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    if (delStickerBtn && stickerBox && previewSticker && fileInput) {
        delStickerBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            stickerBox.style.display = 'none';
            previewSticker.src = '';
            fileInput.value = '';
            if (textTransformBox) textTransformBox.classList.add('is-selected');
        });
    }

    // Modal içi boyutlandırma/döndürme
    function setupModalBox(boxId) {
        const box = document.getElementById(boxId);
        if (!box) return;

        const rotBtn = box.querySelector('.handle-rotate');
        const resBtn = box.querySelector('.handle-resize');

        box.addEventListener('mousedown', function(e) {
            document.querySelectorAll('#postitStudioModal .transform-box').forEach(b => b.classList.remove('is-selected'));
            box.classList.add('is-selected');

            if (e.target.classList.contains('handle-btn')) return;
            e.preventDefault();
            const sx = e.clientX - box.offsetLeft;
            const sy = e.clientY - box.offsetTop;

            function onMove(ev) {
                box.style.left = (ev.clientX - sx) + 'px';
                box.style.top = (ev.clientY - sy) + 'px';
            }
            function onUp() {
                window.removeEventListener('mousemove', onMove);
                window.removeEventListener('mouseup', onUp);
            }
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onUp);
        });

        if (resBtn) {
            resBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const sx = e.clientX;
                const sw = box.offsetWidth;
                const txt = box.querySelector('.postit-text-content');
                const fs = txt ? parseFloat(window.getComputedStyle(txt).fontSize) : 18;

                function onResize(ev) {
                    const nw = Math.max(20, sw + (ev.clientX - sx));
                    box.style.width = nw + 'px';
                    if (boxId === 'stickerTransformBox') {
                        box.style.height = (nw * stickerRatio) + 'px';
                    }
                    if (txt) {
                        txt.style.fontSize = Math.max(8, Math.min(56, fs * (nw / sw))) + 'px';
                    }
                }
                function onStop() {
                    window.removeEventListener('mousemove', onResize);
                    window.removeEventListener('mouseup', onStop);
                }
                window.addEventListener('mousemove', onResize);
                window.addEventListener('mouseup', onStop);
            });
        }

        if (rotBtn) {
            rotBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const rect = box.getBoundingClientRect();
                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;

                function onRotate(ev) {
                    const rad = Math.atan2(ev.clientX - cx, -(ev.clientY - cy));
                    box.style.transform = 'rotate(' + Math.round(rad * (180 / Math.PI)) + 'deg)';
                }
                function onStop() {
                    window.removeEventListener('mousemove', onRotate);
                    window.removeEventListener('mouseup', onStop);
                }
                window.addEventListener('mousemove', onRotate);
                window.addEventListener('mouseup', onStop);
            });
        }
    }

    setupModalBox('textTransformBox');
    setupModalBox('stickerTransformBox');

    // Panoya Post-it Ekleme (OK Butonu)
    window.pinNoteToBoard = function() {
        if (!corkboard || !previewBox) return;

        const wrap = document.createElement('div');
        wrap.className = 'cork-postit can-delete';
        wrap.style.top = '25%';
        wrap.style.left = '35%';
        wrap.setAttribute('data-can-manage', '1');

        const initialScale = 0.65;
        const rot = Math.floor(Math.random() * 8 - 4);
        wrap.setAttribute('data-scale', initialScale);
        wrap.setAttribute('data-rotation', rot);
        wrap.style.transform = `scale(${initialScale}) rotate(${rot}deg)`;
        bringToFront(wrap);

        const cloned = previewBox.cloneNode(true);
        cloned.removeAttribute('id');
        cloned.querySelectorAll('.handle-btn').forEach(btn => btn.remove());
        cloned.querySelectorAll('.transform-box').forEach(b => {
            b.removeAttribute('id');
            b.classList.remove('is-selected');
            b.style.border = 'none';
        });

        if (stickerBox && stickerBox.style.display === 'none') {
            const sImg = cloned.querySelector('.postit-sticker-img');
            if (sImg && sImg.closest('.transform-box')) {
                sImg.closest('.transform-box').remove();
            }
        }

        wrap.appendChild(cloned);
        wrap.innerHTML += `
            <div class="handle-btn handle-delete postit-delete-btn" title="Sil">✕</div>
            <div class="handle-btn handle-rotate" title="Döndür">↻</div>
            <div class="handle-btn handle-resize" title="Büyüt">⤡</div>
        `;

        setupBoardItem(wrap, true);
        corkboard.appendChild(wrap);
        window.closePostitModal();
        showFriendSave();
    };

    // --- PANODAKİ ELEMANLARI DÜZENLEME, SİLME VE SÜRÜKLEME (TEK BİRLEŞİK FONKSİYON) ---
    function setupBoardItem(el, canManage) {
        el.ondragstart = () => false;

        // Hem seçme (tutamaçları çıkarma) hem sürükleme olayını birbirini ezmeden bağlama
        el.addEventListener('mousedown', function(e) {
            if (IS_OWN && !isEditingMode) return;
            if (!IS_OWN && !canManage) return;

            // Seçileni aktif yap
            document.querySelectorAll('.cork-postit, .free-sticker-wrapper').forEach(x => x.classList.remove('is-selected'));
            el.classList.add('is-selected');
            bringToFront(el);

            // Eğer tutamaçlara tıklandıysa sürüklemeyi başlatma
            if (e.target.classList.contains('handle-btn')) return;

            e.preventDefault();
            let prevX = e.clientX;
            let prevY = e.clientY;
            const bRect = corkboard.getBoundingClientRect();
            let curL = parseFloat(el.style.left) || 20;
            let curT = parseFloat(el.style.top) || 20;

            function onDrag(ev) {
                const dx = ev.clientX - prevX;
                const dy = ev.clientY - prevY;
                prevX = ev.clientX;
                prevY = ev.clientY;

                curL = Math.max(0, Math.min(88, curL + (dx / bRect.width) * 100));
                curT = Math.max(0, Math.min(88, curT + (dy / bRect.height) * 100));
                el.style.left = curL + '%';
                el.style.top = curT + '%';
            }

            function onDragEnd() {
                window.removeEventListener('mousemove', onDrag);
                window.removeEventListener('mouseup', onDragEnd);
                showFriendSave();
            }

            window.addEventListener('mousemove', onDrag);
            window.addEventListener('mouseup', onDragEnd);
        });

        if (!canManage) return;

        // Silme Butonu (✕)
        const delBtn = el.querySelector('.handle-delete');
        if (delBtn) {
            delBtn.onclick = function(e) {
                e.stopPropagation();
                if (IS_OWN && !isEditingMode) return;
                el.remove();
                showFriendSave();
            };
        }

        // Boyutlandırma Butonu (⤡)
        const resBtn = el.querySelector('.handle-resize');
        if (resBtn) {
            resBtn.addEventListener('mousedown', function(e) {
                if (IS_OWN && !isEditingMode) return;
                e.stopPropagation();
                e.preventDefault();
                bringToFront(el);

                const sx = e.clientX;
                const isPostit = el.classList.contains('cork-postit');

                if (isPostit) {
                    let sc = parseFloat(el.getAttribute('data-scale')) || 0.65;
                    let rot = parseFloat(el.getAttribute('data-rotation')) || 0;

                    function onResizeP(ev) {
                        const nsc = Math.max(0.35, Math.min(1.5, sc + (ev.clientX - sx) * 0.004));
                        el.setAttribute('data-scale', nsc.toFixed(3));
                        el.style.transform = `scale(${nsc}) rotate(${rot}deg)`;
                    }
                    function onStopP() {
                        window.removeEventListener('mousemove', onResizeP);
                        window.removeEventListener('mouseup', onStopP);
                        showFriendSave();
                    }
                    window.addEventListener('mousemove', onResizeP);
                    window.addEventListener('mouseup', onStopP);
                } else {
                    // Serbest sticker boyutlandırma
                    const sw = el.offsetWidth;
                    const ratio = el.offsetHeight / el.offsetWidth;

                    function onResizeS(ev) {
                        const nw = Math.max(25, sw + (ev.clientX - sx));
                        el.style.width = nw + 'px';
                        el.style.height = (nw * ratio) + 'px';
                    }
                    function onStopS() {
                        window.removeEventListener('mousemove', onResizeS);
                        window.removeEventListener('mouseup', onStopS);
                    }
                    window.addEventListener('mousemove', onResizeS);
                    window.addEventListener('mouseup', onStopS);
                }
            });
        }

        // Döndürme Butonu (↻)
        const rotBtn = el.querySelector('.handle-rotate');
        if (rotBtn) {
            rotBtn.addEventListener('mousedown', function(e) {
                if (IS_OWN && !isEditingMode) return;
                e.stopPropagation();
                e.preventDefault();
                bringToFront(el);

                const rect = el.getBoundingClientRect();
                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;
                const isPostit = el.classList.contains('cork-postit');
                const sc = parseFloat(el.getAttribute('data-scale')) || 0.65;

                function onRot(ev) {
                    const deg = Math.round(Math.atan2(ev.clientX - cx, -(ev.clientY - cy)) * (180 / Math.PI));
                    if (isPostit) {
                        el.setAttribute('data-rotation', deg);
                        el.style.transform = `scale(${sc}) rotate(${deg}deg)`;
                    } else {
                        el.style.transform = `rotate(${deg}deg)`;
                    }
                }
                function onStopR() {
                    window.removeEventListener('mousemove', onRot);
                    window.removeEventListener('mouseup', onStopR);
                    showFriendSave();
                }
                window.addEventListener('mousemove', onRot);
                window.addEventListener('mouseup', onStopR);
            });
        }
    }

    // --- SERBEST STICKER YÜKLEME ---
    const btnAddSticker = document.getElementById('btnAddStickerBtn');
    const freeUploadInput = document.getElementById('freeStickerUploadInput');

    if (btnAddSticker && freeUploadInput) {
        btnAddSticker.onclick = function(e) {
            e.preventDefault();
            freeUploadInput.click();
        };

        freeUploadInput.onchange = function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = new Image();
                    img.onload = function() {
                        const w = 80;
                        const h = (img.naturalHeight / img.naturalWidth) * 80;
                        const wrap = document.createElement('div');
                        wrap.className = 'free-sticker-wrapper is-selected';
                        wrap.style.top = '30%';
                        wrap.style.left = '40%';
                        wrap.style.width = w + 'px';
                        wrap.style.height = h + 'px';
                        wrap.style.transform = 'rotate(0deg)';
                        bringToFront(wrap);

                        wrap.innerHTML = `
                            <img src="${ev.target.result}">
                            <div class="handle-btn handle-delete" title="Sil">✕</div>
                            <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                            <div class="handle-btn handle-resize" title="Büyüt">⤡</div>
                        `;

                        setupBoardItem(wrap, true);
                        corkboard.appendChild(wrap);
                    };
                    img.src = ev.target.result;
                };
                reader.readAsDataURL(file);
                this.value = '';
            }
        };
    }

    // --- VERİTABANINA ASYNC KAYIT ---
    window.saveBoardToDatabase = async function(triggerBtn = null) {
        if (triggerBtn) {
            triggerBtn.innerText = '⏳ Kaydediliyor...';
            triggerBtn.disabled = true;
        }

        const items = [];
        document.querySelectorAll('#corkboardArea .cork-postit').forEach(it => {
            const author = it.querySelector('.postit-author') ? it.querySelector('.postit-author').innerText : '';
            const inner = it.querySelector('.postit-inner-card');
            let sh = 'size-square';
            if (inner.classList.contains('size-portrait')) sh = 'size-portrait';
            if (inner.classList.contains('size-landscape')) sh = 'size-landscape';

            items.push({
                type: 'postit',
                bg: inner.style.backgroundColor,
                shapeClass: sh,
                top: it.style.top,
                left: it.style.left,
                scale: it.getAttribute('data-scale') || '0.65',
                rotation: it.getAttribute('data-rotation') || '0',
                zIndex: it.style.zIndex || 10,
                html: inner.innerHTML,
                author: author
            });
        });

        document.querySelectorAll('#corkboardArea .free-sticker-wrapper').forEach(wrap => {
            const img = wrap.querySelector('img');
            items.push({
                type: 'free_sticker',
                src: img ? img.src : '',
                top: wrap.style.top,
                left: wrap.style.left,
                width: wrap.style.width,
                height: wrap.style.height,
                transform: wrap.style.transform,
                zIndex: wrap.style.zIndex || 10
            });
        });

        const hooks = [];
        document.querySelectorAll('.keychain-hook-unit').forEach(h => {
            const img = h.querySelector('.keychain-plush-img');
            hooks.push(img ? img.getAttribute('data-key') : null);
        });

        try {
            const res = await fetch(SAVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    board_items: items,
                    hook_slots: hooks,
                    is_locked: boardLocked
                })
            });

            if (res.ok) {
                if (triggerBtn) {
                    triggerBtn.innerText = '✅ Kaydedildi!';
                    setTimeout(() => {
                        if (!IS_OWN) triggerBtn.style.display = 'none';
                        triggerBtn.innerText = IS_OWN ? '✏️ Edit Board' : '💾 SAVE';
                        triggerBtn.disabled = false;
                    }, 1200);
                }
            } else {
                alert('Kayıt başarısız oldu.');
                if (triggerBtn) {
                    triggerBtn.innerText = '💾 SAVE';
                    triggerBtn.disabled = false;
                }
            }
        } catch (err) {
            console.error(err);
            if (triggerBtn) {
                triggerBtn.innerText = '💾 SAVE';
                triggerBtn.disabled = false;
            }
        }
    };

    // Edit Board Butonu Döngüsü
    const toggleEdit = document.getElementById('toggleEditBtn');
    if (toggleEdit && IS_OWN) {
        toggleEdit.onclick = async function() {
            isEditingMode = !isEditingMode;
            const grid = document.getElementById('keychainHooksGrid');

            if (isEditingMode) {
                this.innerText = '💾 SAVE';
                this.classList.add('saving');
                if (corkboard) corkboard.classList.add('is-editing-active');
                if (grid) grid.classList.add('is-editing-mode');
                if (btnAddSticker) btnAddSticker.style.display = 'inline-block';
            } else {
                this.innerText = '⏳ Kaydediliyor...';
                this.disabled = true;

                await window.saveBoardToDatabase(this);

                this.innerText = '✏️ Edit Board';
                this.disabled = false;
                this.classList.remove('saving');
                if (corkboard) corkboard.classList.remove('is-editing-active');
                if (grid) grid.classList.remove('is-editing-mode');
                if (btnAddSticker) btnAddSticker.style.display = 'none';
                document.querySelectorAll('.cork-postit, .free-sticker-wrapper').forEach(el => el.classList.remove('is-selected'));
            }
        };
    }

    // Pano Kilitleme
    const lockBtn = document.getElementById('boardLockBtn');
    const lockImg = document.getElementById('boardLockImg');
    if (lockBtn && IS_OWN) {
        lockBtn.onclick = function() {
            boardLocked = !boardLocked;
            if (lockImg) lockImg.src = boardLocked ? LOCK_ICON : UNLOCK_ICON;
            window.saveBoardToDatabase(null);
        };
    }

    // --- KEYCHAIN / ÇANTA FONKSİYONLARI ---
    const drawer = document.getElementById('collectionDrawer');
    const drawerList = document.getElementById('drawerBadgesList');

    window.toggleCollectionDrawer = function() {
        if (!IS_OWN || !drawer) return;
        drawer.classList.toggle('active');
        if (drawer.classList.contains('active')) renderBadges();
    };

    function renderBadges() {
        if (!drawerList) return;
        drawerList.innerHTML = '';
        Object.keys(ACHIEVEMENTS).forEach(key => {
            const item = ACHIEVEMENTS[key];
            const unlocked = item.unlocked;

            const wrap = document.createElement('div');
            wrap.className = 'bag-badge-item ' + (unlocked ? 'unlocked' : 'locked');
            wrap.title = unlocked ? 'Tıkla veya kancaya sürükle' : 'Kilitli';
            wrap.innerHTML = `<img src="/images/badges/${item.file}" class="bag-badge-img" alt="${item.title}" onerror="this.onerror=null; this.src='${FALLBACK_SVG}';"><span class="bag-badge-title">${item.title}</span>`;

            if (unlocked && isEditingMode) {
                wrap.onclick = () => {
                    const slots = document.querySelectorAll('.keychain-hook-unit');
                    for (let s of slots) {
                        if (s.querySelector('.empty-hook-slot')) {
                            hangBadge(s, key, `/images/badges/${item.file}`, item.title);
                            return;
                        }
                    }
                    alert('Tüm 9 kanca dolu! Birini boşaltmak için Edit modunda tıkla.');
                };

                wrap.setAttribute('draggable', 'true');
                wrap.ondragstart = (e) => e.dataTransfer.setData('text/plain', key);
            }
            drawerList.appendChild(wrap);
        });
    }

    function hangBadge(slotElem, key, iconUrl, title) {
        const emptySlot = slotElem.querySelector('.empty-hook-slot');
        if (emptySlot) emptySlot.remove();
        const oldImg = slotElem.querySelector('.keychain-plush-img');
        if (oldImg) oldImg.remove();

        const img = document.createElement('img');
        img.src = iconUrl;
        img.className = 'keychain-plush-img';
        img.setAttribute('data-key', key);
        img.title = title;
        img.onerror = function() { this.src = FALLBACK_SVG; };
        slotElem.appendChild(img);
    }

    window.handleHookSlotClick = function(slotNum) {
        if (!IS_OWN || !isEditingMode) return;
        const slot = document.querySelector(`.keychain-hook-unit[data-slot="${slotNum}"]`);
        const img = slot.querySelector('.keychain-plush-img');

        if (img) {
            img.remove();
            const empty = document.createElement('div');
            empty.className = 'empty-hook-slot';
            slot.appendChild(empty);
            return;
        }

        if (drawer && !drawer.classList.contains('active')) {
            window.toggleCollectionDrawer();
        }
    };

    document.querySelectorAll('.keychain-hook-unit').forEach(hook => {
        hook.ondragover = (e) => {
            if (!IS_OWN || !isEditingMode) return;
            e.preventDefault();
            hook.classList.add('drag-over');
        };
        hook.ondragleave = () => hook.classList.remove('drag-over');
        hook.ondrop = (e) => {
            if (!IS_OWN || !isEditingMode) return;
            e.preventDefault();
            hook.classList.remove('drag-over');
            const key = e.dataTransfer.getData('text/plain');
            if (key && ACHIEVEMENTS[key] && ACHIEVEMENTS[key].unlocked) {
                hangBadge(hook, key, `/images/badges/${ACHIEVEMENTS[key].file}`, ACHIEVEMENTS[key].title);
            }
        };
    });

    // Panodaki mevcut öğeleri başlat
    document.querySelectorAll('#corkboardArea .cork-postit').forEach(wrapper => {
        const canManage = wrapper.getAttribute('data-can-manage') === '1';
        setupBoardItem(wrapper, canManage);
    });

    document.querySelectorAll('#corkboardArea .free-sticker-wrapper').forEach(wrap => {
        if (IS_OWN) setupBoardItem(wrap, true);
    });
})();
</script>