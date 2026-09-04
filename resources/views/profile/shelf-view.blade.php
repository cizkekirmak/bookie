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
        display: flex !important;
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
    #stickerTransformBox.is-hidden { display: none !important; }

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
    while (is_string($rawItems)) { $rawItems = json_decode($rawItems, true); }
    $boardItems = is_array($rawItems) ? $rawItems : [];

    $rawHooks = $currentBoard->hook_slots ?? [];
    while (is_string($rawHooks)) { $rawHooks = json_decode($rawHooks, true); }
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
                        $canManage = $isOwnProfile || (!empty($loggedUsername) && str_contains(mb_strtolower($authorText, 'UTF-8'), mb_strtolower($loggedUsername, 'UTF-8')));
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
            <button type="button" id="btnAddStickerBtn" class="btn-action" style="display:none;" onclick="document.getElementById('freeStickerUploadInput').click()">🖼️ ADD STICKER</button>
            <input type="file" id="freeStickerUploadInput" accept="image/*" style="display:none;">
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
                    <input type="file" id="studioFileInput" accept="image/*" style="display:none;">
                </label>
            </div>

            <div class="studio-preview">
                <div class="postit-inner-card size-square" id="previewPostitBox" style="background:#fdf5a6; position: relative;">
                    <span class="postit-pin"></span>
                    
                    <div id="textTransformBox" class="transform-box is-selected" style="top:25px; left:16px; position: absolute; z-index: 10;">
                        <div class="postit-text-content" id="previewTextLayer" style="position:relative; font-size:18px; word-break: break-word; user-select: none;">selam</div>
                        <div class="handle-btn handle-rotate" title="Döndür">↻</div>
                        <div class="handle-btn handle-resize" title="Büyüt / Küçült">⤡</div>
                    </div>

                    <div id="stickerTransformBox" class="transform-box is-hidden" style="top:55px; left:35px; width:70px; height:auto; position: absolute; z-index: 11;">
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
    var CURRENT_USERNAME = @json(auth()->user()->username ?? (auth()->user()->name ?? 'reader'));
    var IS_OWN_PROFILE = @json($isOwnProfile);
    var isBoardLocked = @json($isBoardLocked);

    var LOCK_ICON_PATH = '{{ asset("images/locke.png") }}';
    var UNLOCKED_ICON_PATH = '{{ asset("images/unlocked.png") }}';
    var SAVE_URL = @json(route('board.save', $user->id ?? auth()->id()));
    var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    var ACHIEVEMENTS_DATA = @json($achievements ?? []);
    var FALLBACK_BADGE_SVG = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="18" r="8" fill="none" stroke="%23888" stroke-width="4"/><rect x="25" y="32" width="50" height="56" rx="14" fill="%23badfa0" stroke="%234b813b" stroke-width="3"/><circle cx="42" cy="54" r="4" fill="%232d5a27"/><circle cx="58" cy="54" r="4" fill="%232d5a27"/><path d="M 45 64 Q 50 68 55 64" fill="none" stroke="%232d5a27" stroke-width="3" stroke-linecap="round"/></svg>`;

    var corkboard = document.getElementById('corkboardArea');
    var isEditingModeActive = false;
    var globalMaxZIndex = 100;
    var stickerAspectRatio = 1;

    function bringToFront(element) {
        globalMaxZIndex++;
        element.style.zIndex = globalMaxZIndex;
    }

    function showFriendSaveButton() {
        if (!IS_OWN_PROFILE) {
            var btn = document.getElementById('friendSaveBtn');
            if (btn) btn.style.display = 'inline-block';
        }
    }

    // --- MODAL AÇILIŞ VE İŞLEMLERİ ---
    var modal = document.getElementById('postitStudioModal');
    var textInput = document.getElementById('studioTextInput');
    var previewText = document.getElementById('previewTextLayer');
    var previewBox = document.getElementById('previewPostitBox');
    var fileInput = document.getElementById('studioFileInput');
    var previewSticker = document.getElementById('previewStickerLayer');
    var stickerBox = document.getElementById('stickerTransformBox');
    var textTransformBox = document.getElementById('textTransformBox');
    var delStickerBtn = document.getElementById('btnDeleteSticker');

    window.openPostitModal = function() {
        if (!modal) return;
        var authorLabel = document.getElementById('previewAuthorLabel');
        if (authorLabel) authorLabel.innerText = '@' + CURRENT_USERNAME;

        if (textInput) textInput.value = '';
        if (previewText) previewText.innerText = 'selam';
        if (stickerBox) stickerBox.classList.add('is-hidden');
        if (previewSticker) previewSticker.src = '';
        if (fileInput) fileInput.value = '';
        if (textTransformBox) {
            textTransformBox.style.width = 'fit-content';
            textTransformBox.classList.add('is-selected');
        }

        modal.classList.add('active');
    };

    window.closePostitModal = function() {
        if (modal) modal.classList.remove('active');
    };

    if (textInput && previewText) {
        textInput.addEventListener('input', function() {
            previewText.innerText = this.value.trim() !== '' ? this.value : 'selam';
            if (textTransformBox) textTransformBox.style.width = 'fit-content';
        });
    }

    document.querySelectorAll('.color-ball').forEach(function(b) {
        b.onclick = function() {
            document.querySelectorAll('.color-ball').forEach(function(x) { x.classList.remove('selected'); });
            this.classList.add('selected');
            if (previewBox) previewBox.style.backgroundColor = this.getAttribute('data-c');
        };
    });

    document.querySelectorAll('.shape-btn').forEach(function(b) {
        b.onclick = function() {
            document.querySelectorAll('.shape-btn').forEach(function(x) { x.classList.remove('active'); });
            this.classList.add('active');
            if (previewBox) previewBox.className = 'postit-inner-card ' + this.getAttribute('data-shape');
        };
    });

    if (fileInput && previewSticker && stickerBox) {
        fileInput.onchange = function() {
            var file = this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.onload = function() {
                    stickerAspectRatio = img.naturalHeight / img.naturalWidth;
                    var w = 70;
                    stickerBox.style.width = w + 'px';
                    stickerBox.style.height = (w * stickerAspectRatio) + 'px';
                    previewSticker.src = e.target.result;
                    stickerBox.classList.remove('is-hidden');

                    document.querySelectorAll('#postitStudioModal .transform-box').forEach(function(b) { b.classList.remove('is-selected'); });
                    stickerBox.classList.add('is-selected');
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        };
    }

    if (delStickerBtn && stickerBox && previewSticker && fileInput) {
        delStickerBtn.onclick = function(e) {
            e.stopPropagation();
            stickerBox.classList.add('is-hidden');
            previewSticker.src = '';
            fileInput.value = '';
            if (textTransformBox) textTransformBox.classList.add('is-selected');
        };
    }

    // Modal İçi Transform Box
    function setupModalTransformer(boxId) {
        var box = document.getElementById(boxId);
        if (!box) return;

        var rotBtn = box.querySelector('.handle-rotate');
        var resBtn = box.querySelector('.handle-resize');

        box.addEventListener('mousedown', function(e) {
            document.querySelectorAll('#postitStudioModal .transform-box').forEach(function(b) { b.classList.remove('is-selected'); });
            box.classList.add('is-selected');

            if (e.target.classList.contains('handle-btn')) return;
            e.preventDefault();
            var startX = e.clientX - box.offsetLeft;
            var startY = e.clientY - box.offsetTop;

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

        if (resBtn) {
            resBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                e.preventDefault();
                var startX = e.clientX;
                var startWidth = box.offsetWidth;
                var txt = box.querySelector('.postit-text-content');
                var initFs = txt ? parseFloat(window.getComputedStyle(txt).fontSize) : 18;

                function resize(ev) {
                    var newW = Math.max(20, startWidth + (ev.clientX - startX));
                    box.style.width = newW + 'px';
                    if (boxId === 'stickerTransformBox') {
                        box.style.height = (newW * stickerAspectRatio) + 'px';
                    }
                    if (txt) {
                        txt.style.fontSize = Math.max(8, Math.min(56, initFs * (newW / startWidth))) + 'px';
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

        if (rotBtn) {
            rotBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                e.preventDefault();
                var rect = box.getBoundingClientRect();
                var cx = rect.left + rect.width / 2;
                var cy = rect.top + rect.height / 2;

                function rotate(ev) {
                    var rad = Math.atan2(ev.clientX - cx, -(ev.clientY - cy));
                    box.style.transform = 'rotate(' + Math.round(rad * (180 / Math.PI)) + 'deg)';
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

    setupModalTransformer('textTransformBox');
    setupModalTransformer('stickerTransformBox');

    // Panoya Post-it Basma
    window.pinNoteToBoard = function() {
        if (!corkboard || !previewBox) return;

        var postitWrapper = document.createElement('div');
        postitWrapper.className = 'cork-postit can-delete';
        postitWrapper.style.top = '25%';
        postitWrapper.style.left = '35%';
        postitWrapper.setAttribute('data-can-manage', '1');

        var initialScale = 0.65;
        var rot = Math.floor(Math.random() * 8 - 4);
        postitWrapper.setAttribute('data-scale', initialScale);
        postitWrapper.setAttribute('data-rotation', rot);
        postitWrapper.style.transform = 'scale(' + initialScale + ') rotate(' + rot + 'deg)';
        bringToFront(postitWrapper);

        var clonedCard = previewBox.cloneNode(true);
        clonedCard.removeAttribute('id');

        clonedCard.querySelectorAll('.handle-btn').forEach(function(btn) { btn.remove(); });
        clonedCard.querySelectorAll('.transform-box').forEach(function(b) {
            b.removeAttribute('id');
            b.classList.remove('is-selected');
            b.style.border = 'none';
        });

        if (stickerBox && stickerBox.classList.contains('is-hidden')) {
            var sEl = clonedCard.querySelector('.postit-sticker-img');
            if (sEl && sEl.closest('.transform-box')) {
                sEl.closest('.transform-box').remove();
            }
        }

        postitWrapper.appendChild(clonedCard);
        postitWrapper.innerHTML += `
            <div class="handle-btn handle-delete postit-delete-btn" title="Sil">✕</div>
            <div class="handle-btn handle-rotate" title="Döndür">↻</div>
            <div class="handle-btn handle-resize" title="Post-it'i Büyüt / Küçült">⤡</div>
        `;

        setupBoardElement(postitWrapper, true);
        corkboard.appendChild(postitWrapper);
        window.closePostitModal();

        showFriendSaveButton();
    };

    // --- PANODAKİ NOT VE SERBEST STICKERLARI YÖNETME (TUTAMAÇ + SÜRÜKLEME BİRLEŞİK) ---
    function setupBoardElement(wrapper, canManage) {
        wrapper.ondragstart = function() { return false; };

        wrapper.addEventListener('mousedown', function(e) {
            if (IS_OWN_PROFILE && !isEditingModeActive) return;
            if (!IS_OWN_PROFILE && !canManage) return;

            document.querySelectorAll('.cork-postit, .free-sticker-wrapper').forEach(function(el) { el.classList.remove('is-selected'); });
            wrapper.classList.add('is-selected');
            bringToFront(wrapper);

            if (e.target.classList.contains('handle-btn')) return;

            e.preventDefault();
            var prevMouseX = e.clientX;
            var prevMouseY = e.clientY;
            var boardRect = corkboard.getBoundingClientRect();
            var curLeftPct = parseFloat(wrapper.style.left) || 20;
            var curTopPct = parseFloat(wrapper.style.top) || 20;

            function onMouseMove(event) {
                var dx = event.clientX - prevMouseX;
                var dy = event.clientY - prevMouseY;
                prevMouseX = event.clientX;
                prevMouseY = event.clientY;

                curLeftPct = Math.max(0, Math.min(88, curLeftPct + (dx / boardRect.width) * 100));
                curTopPct = Math.max(0, Math.min(88, curTopPct + (dy / boardRect.height) * 100));
                wrapper.style.left = curLeftPct + '%';
                wrapper.style.top = curTopPct + '%';
            }

            function onMouseUp() {
                window.removeEventListener('mousemove', onMouseMove);
                window.removeEventListener('mouseup', onMouseUp);
                showFriendSaveButton();
            }

            window.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);
        });

        if (!canManage) return;

        // Sil butonu
        var delBtn = wrapper.querySelector('.handle-delete');
        if (delBtn) {
            delBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (IS_OWN_PROFILE && !isEditingModeActive) return;
                wrapper.remove();
                showFriendSaveButton();
            });
        }

        // Boyutlandırma butonu
        var resBtn = wrapper.querySelector('.handle-resize');
        if (resBtn) {
            resBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                e.preventDefault();
                if (IS_OWN_PROFILE && !isEditingModeActive) return;

                var startX = e.clientX;
                var isPostit = wrapper.classList.contains('cork-postit');

                if (isPostit) {
                    var initScale = parseFloat(wrapper.getAttribute('data-scale')) || 0.65;
                    var rot = parseFloat(wrapper.getAttribute('data-rotation')) || 0;

                    function onResizeP(ev) {
                        var newScale = Math.max(0.35, Math.min(1.5, initScale + (ev.clientX - startX) * 0.004));
                        wrapper.setAttribute('data-scale', newScale.toFixed(3));
                        wrapper.style.transform = 'scale(' + newScale + ') rotate(' + rot + 'deg)';
                    }
                    function onStopP() {
                        window.removeEventListener('mousemove', onResizeP);
                        window.removeEventListener('mouseup', onStopP);
                        showFriendSaveButton();
                    }
                    window.addEventListener('mousemove', onResizeP);
                    window.addEventListener('mouseup', onStopP);
                } else {
                    var startW = wrapper.offsetWidth;
                    var ratio = wrapper.offsetHeight / wrapper.offsetWidth;

                    function onResizeS(ev) {
                        var nw = Math.max(25, startW + (ev.clientX - startX));
                        wrapper.style.width = nw + 'px';
                        wrapper.style.height = (nw * ratio) + 'px';
                    }
                    function onStopS() {
                        window.removeEventListener('mousemove', onResizeS);
                        window.removeEventListener('mouseup', onStopS);
                        showFriendSaveButton();
                    }
                    window.addEventListener('mousemove', onResizeS);
                    window.addEventListener('mouseup', onStopS);
                }
            });
        }

        // Döndürme butonu
        var rotBtn = wrapper.querySelector('.handle-rotate');
        if (rotBtn) {
            rotBtn.addEventListener('mousedown', function(e) {
                e.stopPropagation();
                e.preventDefault();
                if (IS_OWN_PROFILE && !isEditingModeActive) return;

                var rect = wrapper.getBoundingClientRect();
                var cx = rect.left + rect.width / 2;
                var cy = rect.top + rect.height / 2;
                var isPostit = wrapper.classList.contains('cork-postit');
                var sc = parseFloat(wrapper.getAttribute('data-scale')) || 0.65;

                function onRotateEv(ev) {
                    var deg = Math.round(Math.atan2(ev.clientX - cx, -(ev.clientY - cy)) * (180 / Math.PI));
                    if (isPostit) {
                        wrapper.setAttribute('data-rotation', deg);
                        wrapper.style.transform = 'scale(' + sc + ') rotate(' + deg + 'deg)';
                    } else {
                        wrapper.style.transform = 'rotate(' + deg + 'deg)';
                    }
                }
                function onRotateStop() {
                    window.removeEventListener('mousemove', onRotateEv);
                    window.removeEventListener('mouseup', onRotateStop);
                    showFriendSaveButton();
                }
                window.addEventListener('mousemove', onRotateEv);
                window.addEventListener('mouseup', onRotateStop);
            });
        }
    }

    // --- SERBEST STICKER YÜKLEME ---
    var freeInput = document.getElementById('freeStickerUploadInput');
    if (freeInput) {
        freeInput.onchange = function() {
            var file = this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.onload = function() {
                    var w = 80;
                    var h = (img.naturalHeight / img.naturalWidth) * 80;

                    var wrap = document.createElement('div');
                    wrap.className = 'free-sticker-wrapper is-selected';
                    wrap.style.top = '30%';
                    wrap.style.left = '40%';
                    wrap.style.width = w + 'px';
                    wrap.style.height = h + 'px';
                    wrap.style.transform = 'rotate(0deg)';
                    bringToFront(wrap);

                    wrap.innerHTML = '<img src="' + e.target.result + '">' +
                        '<div class="handle-btn handle-delete" title="Sil">✕</div>' +
                        '<div class="handle-btn handle-rotate" title="Döndür">↻</div>' +
                        '<div class="handle-btn handle-resize" title="Büyüt / Küçült">⤡</div>';

                    setupBoardElement(wrap, true);
                    corkboard.appendChild(wrap);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
            this.value = '';
        };
    }

    // --- ASYNC SAVE DATABASE ---
    window.saveBoardToDatabase = async function(triggerBtn = null) {
        if (triggerBtn) {
            triggerBtn.innerText = '⏳ Kaydediliyor...';
            triggerBtn.disabled = true;
        }

        var boardItems = [];
        document.querySelectorAll('#corkboardArea .cork-postit').forEach(function(item) {
            var authorText = item.querySelector('.postit-author') ? item.querySelector('.postit-author').innerText : '';
            var inner = item.querySelector('.postit-inner-card');
            var sh = 'size-square';
            if (inner.classList.contains('size-portrait')) sh = 'size-portrait';
            if (inner.classList.contains('size-landscape')) sh = 'size-landscape';

            boardItems.push({
                type: 'postit',
                bg: inner.style.backgroundColor,
                shapeClass: sh,
                top: item.style.top,
                left: item.style.left,
                scale: item.getAttribute('data-scale') || '0.65',
                rotation: item.getAttribute('data-rotation') || '0',
                zIndex: item.style.zIndex || 10,
                html: inner.innerHTML,
                author: authorText
            });
        });

        document.querySelectorAll('#corkboardArea .free-sticker-wrapper').forEach(function(wrap) {
            var img = wrap.querySelector('img');
            boardItems.push({
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

        var hookSlots = [];
        document.querySelectorAll('.keychain-hook-unit').forEach(function(hook) {
            var img = hook.querySelector('.keychain-plush-img');
            hookSlots.push(img ? img.getAttribute('data-key') : null);
        });

        try {
            var res = await fetch(SAVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    board_items: boardItems,
                    hook_slots: hookSlots,
                    is_locked: isBoardLocked
                })
            });

            var data = await res.json();
            if (res.ok) {
                if (triggerBtn) {
                    triggerBtn.innerText = '✅ Kaydedildi!';
                    setTimeout(function() {
                        if (!IS_OWN_PROFILE) triggerBtn.style.display = 'none';
                        triggerBtn.innerText = IS_OWN_PROFILE ? '✏️ Edit Board' : '💾 SAVE';
                        triggerBtn.disabled = false;
                    }, 1200);
                }
            } else {
                alert('Kayıt başarısız: ' + (data.error || 'Bilinmeyen hata'));
                if (triggerBtn) {
                    triggerBtn.innerText = '💾 SAVE';
                    triggerBtn.disabled = false;
                }
            }
        } catch (error) {
            console.error('Kayıt isteği hatası:', error);
            if (triggerBtn) {
                triggerBtn.innerText = '💾 SAVE';
                triggerBtn.disabled = false;
            }
        }
    };

    // Edit Board Buton Döngüsü
    var toggleEditBtn = document.getElementById('toggleEditBtn');
    var btnAddStickerBtn = document.getElementById('btnAddStickerBtn');
    if (toggleEditBtn && IS_OWN_PROFILE) {
        toggleEditBtn.onclick = async function() {
            isEditingModeActive = !isEditingModeActive;
            var grid = document.getElementById('keychainHooksGrid');

            if (isEditingModeActive) {
                this.innerText = '💾 SAVE';
                this.classList.add('saving');
                if (corkboard) corkboard.classList.add('is-editing-active');
                if (grid) grid.classList.add('is-editing-mode');
                if (btnAddStickerBtn) btnAddStickerBtn.style.display = 'inline-block';
            } else {
                this.innerText = '⏳ Kaydediliyor...';
                this.disabled = true;

                await window.saveBoardToDatabase(this);

                this.innerText = '✏️ Edit Board';
                this.disabled = false;
                this.classList.remove('saving');
                if (corkboard) corkboard.classList.remove('is-editing-active');
                if (grid) grid.classList.remove('is-editing-mode');
                if (btnAddStickerBtn) btnAddStickerBtn.style.display = 'none';
                document.querySelectorAll('.cork-postit, .free-sticker-wrapper').forEach(function(el) { el.classList.remove('is-selected'); });
            }
        };
    }

    // Pano Kilit Butonu
    var boardLockBtn = document.getElementById('boardLockBtn');
    var boardLockImg = document.getElementById('boardLockImg');
    if (boardLockBtn && IS_OWN_PROFILE) {
        boardLockBtn.onclick = function() {
            isBoardLocked = !isBoardLocked;
            if (boardLockImg) boardLockImg.src = isBoardLocked ? LOCK_ICON_PATH : UNLOCKED_ICON_PATH;
            boardLockBtn.title = isBoardLocked ? 'Pano Ziyaretçilere Kilitli' : 'Pano Ziyaretçilere Açık';
            window.saveBoardToDatabase(null);
        };
    }

    // --- KEYCHAIN ÇANTA (DRAWER) VE KANCA SÜRÜKLE-BIRAK ---
    var drawer = document.getElementById('collectionDrawer');
    var drawerList = document.getElementById('drawerBadgesList');

    window.toggleCollectionDrawer = function() {
        if (!IS_OWN_PROFILE || !drawer) return;
        drawer.classList.toggle('active');
        if (drawer.classList.contains('active')) renderBadgesList();
    };

    function renderBadgesList() {
        if (!drawerList) return;
        drawerList.innerHTML = '';
        Object.keys(ACHIEVEMENTS_DATA).forEach(function(key) {
            var item = ACHIEVEMENTS_DATA[key];
            var isUnlocked = item.unlocked;

            var wrap = document.createElement('div');
            wrap.className = 'bag-badge-item ' + (isUnlocked ? 'unlocked' : 'locked');
            wrap.title = isUnlocked ? 'Tıkla veya kancaya sürükle' : 'Kilitli';
            wrap.innerHTML = '<img src="/images/badges/' + item.file + '" class="bag-badge-img" alt="' + item.title + '" onerror="this.onerror=null; this.src=\'' + FALLBACK_BADGE_SVG + '\';">' +
                             '<span class="bag-badge-title">' + item.title + '</span>';

            if (isUnlocked && isEditingModeActive) {
                wrap.onclick = function() {
                    var slots = document.querySelectorAll('.keychain-hook-unit');
                    for (var i = 0; i < slots.length; i++) {
                        if (slots[i].querySelector('.empty-hook-slot')) {
                            hangBadgeToHook(slots[i], key, '/images/badges/' + item.file, item.title);
                            return;
                        }
                    }
                    alert('Tüm 9 kanca dolu! Birini boşaltmak için Edit modunda tıkla.');
                };

                wrap.setAttribute('draggable', 'true');
                wrap.ondragstart = function(e) {
                    e.dataTransfer.setData('text/plain', key);
                    wrap.style.opacity = '0.5';
                };
                wrap.ondragend = function() {
                    wrap.style.opacity = '1';
                };
            }

            drawerList.appendChild(wrap);
        });
    }

    function hangBadgeToHook(slotElem, key, iconUrl, title) {
        var emptySlot = slotElem.querySelector('.empty-hook-slot');
        if (emptySlot) emptySlot.remove();

        var oldImg = slotElem.querySelector('.keychain-plush-img');
        if (oldImg) oldImg.remove();

        var img = document.createElement('img');
        img.src = iconUrl;
        img.className = 'keychain-plush-img';
        img.setAttribute('data-key', key);
        img.title = title;
        img.onerror = function() { this.src = FALLBACK_BADGE_SVG; };
        slotElem.appendChild(img);
    }

    window.handleHookSlotClick = function(slotNum) {
        if (!IS_OWN_PROFILE || !isEditingModeActive) return;
        var slotElem = document.querySelector('.keychain-hook-unit[data-slot="' + slotNum + '"]');
        var existingImg = slotElem.querySelector('.keychain-plush-img');

        if (existingImg) {
            existingImg.remove();
            var emptyBox = document.createElement('div');
            emptyBox.className = 'empty-hook-slot';
            slotElem.appendChild(emptyBox);
            return;
        }

        if (!existingImg && drawer && !drawer.classList.contains('active')) {
            window.toggleCollectionDrawer();
        }
    };

    document.querySelectorAll('.keychain-hook-unit').forEach(function(hook) {
        hook.ondragover = function(e) {
            if (!IS_OWN_PROFILE || !isEditingModeActive) return;
            e.preventDefault();
            hook.classList.add('drag-over');
        };
        hook.ondragleave = function() {
            hook.classList.remove('drag-over');
        };
        hook.ondrop = function(e) {
            if (!IS_OWN_PROFILE || !isEditingModeActive) return;
            e.preventDefault();
            hook.classList.remove('drag-over');
            var key = e.dataTransfer.getData('text/plain');
            if (key && ACHIEVEMENTS_DATA[key] && ACHIEVEMENTS_DATA[key].unlocked) {
                hangBadgeToHook(hook, key, '/images/badges/' + ACHIEVEMENTS_DATA[key].file, ACHIEVEMENTS_DATA[key].title);
            }
        };
    });

    // Panodaki Elemanları Sayfa Açıldığında Başlat
    document.querySelectorAll('#corkboardArea .cork-postit').forEach(function(wrapper) {
        var canManage = wrapper.getAttribute('data-can-manage') === '1';
        setupBoardElement(wrapper, canManage);
    });

    document.querySelectorAll('#corkboardArea .free-sticker-wrapper').forEach(function(wrap) {
        if (IS_OWN_PROFILE) setupBoardElement(wrap, true);
    });
})();
</script>