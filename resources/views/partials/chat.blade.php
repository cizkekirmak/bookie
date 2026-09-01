<div id="chat-draggable-btn" class="chat-bubble-btn">
    <img src="{{ asset('images/chat-icon.png') }}" alt="{{ __('Messages') }}" class="chat-main-icon" draggable="false">
    <span id="chat-unread-dot" class="chat-unread-badge" style="display: none;"></span>
</div>

<div id="chat-popup-container" class="chat-popup" style="display: none;">
    <div class="chat-friends-sidebar" id="chat-friends-list"></div>

    <div class="chat-main-area">
        <div class="chat-header">
            <a href="#" id="chat-header-user" class="chat-header-user" style="display: none; text-decoration: none; cursor: pointer;">
                <img id="chat-active-avatar" src="{{ asset('images/default-avatar.jpg') }}" alt="Avatar" class="chat-header-avatar">
                <span id="chat-active-name" class="chat-header-name"></span>
            </a>
            <div id="chat-header-placeholder" class="chat-header-placeholder">
                {{ __('Choose a friend to start chatting.') }}
            </div>
            <button type="button" class="chat-close-btn" id="chat-close-btn">&times;</button>
        </div>

        <div class="chat-messages-body" id="chat-messages-body">
            <div class="chat-empty-state" id="chat-empty-state">
                {{ __("You didn't pick anyone yet :<") }}
            </div>
        </div>

        <div class="chat-sticker-picker" id="chat-sticker-picker" style="display: none;">
            <img src="{{ asset('images/sticker1.png') }}" class="sticker-item" data-sticker="sticker1.png" alt="Sticker 1" draggable="false">
            <img src="{{ asset('images/sticker2.png') }}" class="sticker-item" data-sticker="sticker2.png" alt="Sticker 2" draggable="false">
            <img src="{{ asset('images/sticker3.png') }}" class="sticker-item" data-sticker="sticker3.png" alt="Sticker 3" draggable="false">
            <img src="{{ asset('images/sticker4.png') }}" class="sticker-item" data-sticker="sticker4.png" alt="Sticker 4" draggable="false">
            <img src="{{ asset('images/sticker5.png') }}" class="sticker-item" data-sticker="sticker5.png" alt="Sticker 5" draggable="false">
            <img src="{{ asset('images/sticker6.png') }}" class="sticker-item" data-sticker="sticker6.png" alt="Sticker 6" draggable="false">
        </div>

        <form id="chat-input-form" class="chat-input-area">
            <button type="button" id="chat-toggle-sticker" class="chat-action-btn" title="{{ __('Choose Sticker') }}">
                <img src="{{ asset('images/sticker-icon.png') }}" alt="{{ __('Sticker') }}" class="chat-btn-icon sticker-btn-icon" draggable="false">
            </button>
            <input type="text" id="chat-message-input" placeholder="{{ __('Write a message...') }}" autocomplete="off" disabled>
            <button type="submit" id="chat-send-btn" class="chat-action-btn" title="{{ __('Send') }}" disabled>
                <img src="{{ asset('images/send-icon.png') }}" alt="{{ __('Send') }}" class="chat-btn-icon" draggable="false">
            </button>
        </form>
    </div>
</div>

<style>
/* SÜRÜKLENEBİLİR BUTON */
.chat-bubble-btn {
    position: fixed;
    bottom: 20px;
    left: 20px;
    width: 95px;
    height: 95px;
    cursor: grab;
    z-index: 99999;
    user-select: none;
    touch-action: none;
}
.chat-bubble-btn:active {
    cursor: grabbing;
}
.chat-main-icon {
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
    filter: drop-shadow(0 4px 10px rgba(0,0,0,0.22));
}
.chat-unread-badge {
    position: absolute;
    top: 23px;
    right: 19px;
    width: 12px;
    height: 12px;
    background-color: #eba4b4;
    border-radius: 50%;
    border: 1px solid #27211f;
}

/* ARKADAŞ LİSTESİ */
.chat-friend-item {
    position: relative;
    cursor: pointer;
}
.chat-friend-dot {
    position: absolute;
    top: 0;
    right: 2px;
    width: 12px;
    height: 12px;
    background-color: #eba4b4;
    border-radius: 50%;
    border: 2px solid #fff;
    pointer-events: none;
}
.chat-friend-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid transparent;
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.chat-friend-item.active .chat-friend-avatar {
    border-color: #9fa3c2;
    transform: scale(1.08);
}

/* POP-UP: MASAÜSTÜ */
.chat-popup {
    position: fixed;
    bottom: 135px;
    left: 30px;
    width: 360px;
    height: 430px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.16);
    display: flex;
    overflow: hidden;
    z-index: 99998;
    border: 1px solid rgba(0,0,0,0.08);
}

/* POP-UP: MOBİL */
@media (max-width: 768px) {
    .chat-popup {
        width: 92vw !important;
        max-width: 350px !important;
        height: 430px !important;
        left: 50% !important;
        right: auto !important;
        top: auto !important;
        bottom: 88px !important;
        transform: translateX(-50%) !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.25) !important;
    }
}

/* SOL PANEL */
.chat-friends-sidebar {
    width: 62px;
    background-color: #f2feff;
    border-right: 1px solid #eaeaea;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 0;
    gap: 10px;
    overflow-y: auto;
}

/* SAĞ PANEL */
.chat-main-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    background: #fff;
    min-width: 0;
}
.chat-header {
    height: 46px;
    border-bottom: 1px solid #ffedf8;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 12px;
    background: #fff6f9;
}
.chat-header-user {
    display: flex;
    align-items: center;
    gap: 8px;
    transition: opacity 0.2s ease;
}
.chat-header-user:hover {
    opacity: 0.8;
}
.chat-header-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}
.chat-header-name {
    font-weight: 600;
    font-size: 13px;
    color: #333;
}
.chat-header-placeholder {
    font-size: 12px;
    color: #888;
}
.chat-close-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #333;
}

.chat-messages-body {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    background-image: url('{{ asset("images/chat-bg.jpg") }}');
    background-size: 340px auto;
    background-position: center;
    background-repeat: repeat-y;
    background-color: #fff1f9;
}
.chat-empty-state {
    margin: auto;
    color: #777;
    font-size: 12px;
    background: rgba(255,255,255,0.9);
    padding: 5px 12px;
    border-radius: 10px;
}

/* BALONCUKLAR */
.chat-bubble {
    max-width: 80%;
    padding: 6px 10px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.35;
    word-break: break-word;
    cursor: pointer;
    position: relative;
}
.chat-bubble.mine {
    align-self: flex-end;
    background-color: #e5fbff;
    color: #111;
    border-bottom-right-radius: 2px;
}
.chat-bubble.theirs {
    align-self: flex-start;
    background-color: #fdf3ff;
    color: #111;
    border-bottom-left-radius: 2px;
}
.chat-bubble-sticker {
    width: 70px;
    height: auto;
    display: block;
}

.chat-bubble-time {
    display: none;
    font-size: 9px;
    color: #666;
    margin-top: 3px;
    text-align: right;
}
.chat-bubble.show-time .chat-bubble-time,
.chat-bubble:hover .chat-bubble-time {
    display: block;
}

/* STICKER MENÜSÜ */
.chat-sticker-picker {
    position: absolute;
    bottom: 50px;
    left: 8px;
    right: 8px;
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 8px;
    display: flex;
    justify-content: space-around;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    z-index: 10;
}
.sticker-item {
    width: 38px;
    height: 38px;
    cursor: pointer;
    transition: transform 0.15s ease;
}
.sticker-item:hover {
    transform: scale(1.2);
}

/* GİRDİ FORMU */
.chat-input-area {
    height: 48px;
    border-top: 1px solid #eaeaea;
    display: flex;
    align-items: center;
    padding: 0 8px;
    gap: 6px;
    background: #f6ffec;
}
.chat-input-area input {
    flex: 1;
    height: 34px;
    border: 1px solid #ddd;
    border-radius: 18px;
    padding: 0 12px;
    font-size: 12px;
    outline: none;
}
.chat-messages-body::-webkit-scrollbar {
    width: 6px;
}
.chat-messages-body::-webkit-scrollbar-thumb {
    background: rgb(255, 198, 106);
    border-radius: 10px;
}
.chat-input-area input::placeholder {
    color: #bfb279;
}
.chat-input-area input:disabled {
    background-color: #ffffff;
    opacity: 1;
}
.chat-action-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.chat-btn-icon {
    width: 26px;
    height: 26px;
    pointer-events: none;
}
.sticker-btn-icon {
    width: 32px;
    height: 32px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('chat-draggable-btn');
    const popup = document.getElementById('chat-popup-container');
    const closeBtn = document.getElementById('chat-close-btn');
    const friendsList = document.getElementById('chat-friends-list');
    const messagesBody = document.getElementById('chat-messages-body');
    const headerUser = document.getElementById('chat-header-user');
    const headerPlaceholder = document.getElementById('chat-header-placeholder');
    const activeAvatar = document.getElementById('chat-active-avatar');
    const activeName = document.getElementById('chat-active-name');
    const inputForm = document.getElementById('chat-input-form');
    const messageInput = document.getElementById('chat-message-input');
    const sendBtn = document.getElementById('chat-send-btn');
    const toggleStickerBtn = document.getElementById('chat-toggle-sticker');
    const stickerPicker = document.getElementById('chat-sticker-picker');
    const unreadDot = document.getElementById('chat-unread-dot');

    const emptyChatText = @json(__('No messages yet. Send the first one!'));
    const csrfToken = "{{ csrf_token() }}";
    const defaultAvatarUrl = "{{ asset('images/default-avatar.jpg') }}";

    // --- SES MOTORU (GARANTİLİ TAZE ÇALMA) ---
    const SOUND_URLS = {
        closed: "{{ asset('sounds/yeni-mesaj.mp3') }}",
        inChat: "{{ asset('sounds/mesaj-atma.mp3') }}"
    };

    let audioUnlocked = false;
    function unlockAudio() {
        if (!audioUnlocked) {
            const silent1 = new Audio(SOUND_URLS.closed);
            const silent2 = new Audio(SOUND_URLS.inChat);
            silent1.play().then(() => { silent1.pause(); }).catch(() => {});
            silent2.play().then(() => { silent2.pause(); }).catch(() => {});
            audioUnlocked = true;
            document.removeEventListener('click', unlockAudio);
        }
    }
    document.addEventListener('click', unlockAudio);

    function playSound(type) {
        const soundEnabled = localStorage.getItem('chat_sound_enabled') !== 'false';
        const volume = parseFloat(localStorage.getItem('chat_sound_volume') ?? '0.8');
        if (!soundEnabled || volume <= 0) return;

        try {
            const url = (type === 'closed') ? SOUND_URLS.closed : SOUND_URLS.inChat;
            const audio = new Audio(url);
            audio.volume = volume;
            const playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.catch(err => {
                    console.warn('[Chat Audio Hatası]:', err.message);
                });
            }
        } catch (e) {}
    }

    let activeFriendId = null;
    let isDragging = false;
    let shiftX, shiftY;
    let lastLoadedMessagesCount = 0;
    let isFirstLoadForActiveFriend = false;
    let lastUnreadTotal = 0;
    let pollInterval = null;

    function getAvatarSrc(avatar) {
        return (avatar && avatar.trim() !== '') ? avatar : defaultAvatarUrl;
    }

    // SÜRÜKLE - BIRAK
    btn.addEventListener('mousedown', (e) => {
        isDragging = false;
        shiftX = e.clientX - btn.getBoundingClientRect().left;
        shiftY = e.clientY - btn.getBoundingClientRect().top;

        function moveAt(pageX, pageY) {
            btn.style.left = (pageX - shiftX) + 'px';
            btn.style.top = (pageY - shiftY) + 'px';
            btn.style.bottom = 'auto';
            btn.style.right = 'auto';
        }

        function onMouseMove(e) {
            isDragging = true;
            moveAt(e.clientX, e.clientY);
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });

    btn.addEventListener('click', () => {
        if (isDragging) return;
        const isOpen = popup.style.display === 'flex';
        popup.style.display = isOpen ? 'none' : 'flex';
        
        if (!isOpen) {
            unreadDot.style.display = 'none';
            loadFriends();
            if (window.innerWidth > 768) {
                const rect = btn.getBoundingClientRect();
                if (rect.left < window.innerWidth / 2) {
                    popup.style.left = `${Math.min(rect.left, window.innerWidth - 380)}px`;
                    popup.style.right = 'auto';
                } else {
                    popup.style.right = `${Math.max(20, window.innerWidth - rect.right)}px`;
                    popup.style.left = 'auto';
                }
                popup.style.top = `${Math.max(20, rect.top - 440)}px`;
                popup.style.bottom = 'auto';
            }
        }
        startPolling();
    });

    closeBtn.addEventListener('click', () => {
        popup.style.display = 'none';
        activeFriendId = null;
        lastLoadedMessagesCount = 0;
        document.querySelectorAll('.chat-friend-item').forEach(el => el.classList.remove('active'));
        startPolling();
        checkUnread();
    });

    async function loadFriends() {
        try {
            const res = await fetch('/messages/friends', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const friends = await res.json();
            friendsList.innerHTML = '';

            friends.forEach(friend => {
                const item = document.createElement('div');
                const isSelected = (activeFriendId === friend.id);
                item.className = `chat-friend-item ${isSelected ? 'active' : ''}`;
                
                // Seçili arkadaşta nokta asla gösterilmez
                const showBadge = (!isSelected && friend.unread_count > 0);
                const badgeHtml = showBadge ? '<span class="chat-friend-dot"></span>' : '';

                item.innerHTML = `
                    <img src="${getAvatarSrc(friend.avatar)}" class="chat-friend-avatar" alt="${escapeHtml(friend.username)}">
                    ${badgeHtml}
                `;
                item.addEventListener('click', () => selectFriend(friend));
                friendsList.appendChild(item);
            });
        } catch (e) {
            console.error('Arkadaş listesi yüklenemedi:', e);
        }
    }

    async function selectFriend(friend) {
        activeFriendId = friend.id;
        lastLoadedMessagesCount = 0;
        isFirstLoadForActiveFriend = true;
        
        headerPlaceholder.style.display = 'none';
        headerUser.style.display = 'flex';
        headerUser.href = `/profile/${friend.id}`;
        
        activeAvatar.src = getAvatarSrc(friend.avatar);
        activeName.textContent = friend.username;

        messageInput.disabled = false;
        sendBtn.disabled = false;
        messageInput.focus();

        lastUnreadTotal = 0;

        loadFriends();
        await loadMessages(true);
        startPolling();
        checkUnread();
    }

    async function loadMessages(forceScroll = false) {
        if (!activeFriendId) return;
        try {
            const res = await fetch(`/messages/${activeFriendId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const messages = await res.json();

            // İlk açılış değilse ve yeni mesaj geldiyse sohbet içi ses çal
            if (!isFirstLoadForActiveFriend && lastLoadedMessagesCount > 0 && messages.length > lastLoadedMessagesCount) {
                const newestMessage = messages[messages.length - 1];
                if (!newestMessage.is_mine) {
                    playSound('inChat');
                }
            }
            isFirstLoadForActiveFriend = false;

            if (messages.length === lastLoadedMessagesCount && !forceScroll) {
                return;
            }
            lastLoadedMessagesCount = messages.length;

            const threshold = 60;
            const isNearBottom = (messagesBody.scrollHeight - messagesBody.scrollTop - messagesBody.clientHeight) <= threshold;
            const previousScrollTop = messagesBody.scrollTop;

            messagesBody.innerHTML = '';
            if (messages.length === 0) {
                messagesBody.innerHTML = `<div class="chat-empty-state">${emptyChatText}</div>`;
                return;
            }

            messages.forEach(msg => {
                const bubble = document.createElement('div');
                bubble.className = `chat-bubble ${msg.is_mine ? 'mine' : 'theirs'}`;

                bubble.addEventListener('click', () => {
                    bubble.classList.toggle('show-time');
                });

                if (msg.message.startsWith('[sticker:') && msg.message.endsWith(']')) {
                    const stickerName = msg.message.replace('[sticker:', '').replace(']', '');
                    bubble.innerHTML = `
                        <img src="/images/${escapeHtml(stickerName)}" class="chat-bubble-sticker" alt="Sticker">
                        <div class="chat-bubble-time">${escapeHtml(msg.time)}</div>
                    `;
                } else {
                    bubble.innerHTML = `
                        <div>${escapeHtml(msg.message)}</div>
                        <div class="chat-bubble-time">${escapeHtml(msg.time)}</div>
                    `;
                }
                messagesBody.appendChild(bubble);
            });

            if (forceScroll || isNearBottom) {
                messagesBody.scrollTop = messagesBody.scrollHeight;
            } else {
                messagesBody.scrollTop = previousScrollTop;
            }
        } catch (e) {
            console.error('Mesajlar yüklenemedi:', e);
        }
    }

    async function sendMessage(text) {
        if (!activeFriendId || !text.trim()) return;
        try {
            const res = await fetch('/messages/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    receiver_id: activeFriendId,
                    message: text
                })
            });

            if (res.ok) {
                playSound('inChat'); // Gönderilince ses anında tetiklenir
                messageInput.value = '';
                stickerPicker.style.display = 'none';
                await loadMessages(true);
            }
        } catch (e) {
            console.error('Mesaj iletilemedi:', e);
        }
    }

    inputForm.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage(messageInput.value);
    });

    toggleStickerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!activeFriendId) return;
        stickerPicker.style.display = stickerPicker.style.display === 'none' ? 'flex' : 'none';
    });

    document.querySelectorAll('.sticker-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            sendMessage(`[sticker:${item.dataset.sticker}]`);
        });
    });

    async function checkUnread() {
        try {
            const res = await fetch('/messages/unread-count', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            const currentCount = parseInt(data.unread_count || 0);
            const isPopupOpen = (popup.style.display === 'flex');

            // 1. Kırmızı Nokta (Badge)
            if (isPopupOpen) {
                unreadDot.style.display = 'none';
            } else {
                unreadDot.style.display = (currentCount > 0) ? 'block' : 'none';
            }

            // 2. BİLDİRİM SESİ: Chat kapalıyken mesaj arttıysa doğrudan çal
            if (!isPopupOpen && currentCount > lastUnreadTotal) {
                playSound('closed');
            }
            lastUnreadTotal = currentCount;

            // 3. Popup açıksa listeyi ve aktif odayı tazele
            if (isPopupOpen) {
                loadFriends();
                if (activeFriendId) {
                    loadMessages(false);
                }
            }
        } catch (e) {}
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        const intervalTime = (popup.style.display === 'flex') ? 2500 : 5000;
        pollInterval = setInterval(checkUnread, intervalTime);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    checkUnread();
    startPolling();
});
</script>