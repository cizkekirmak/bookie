<!-- SÜRÜKLENEBİLİR ANA BUTON (SOL ALTA ALINDI VE BÜYÜTÜLDÜ) -->
<div id="chat-draggable-btn" class="chat-bubble-btn">
    <img src="{{ asset('images/chat-icon.png') }}" alt="Mesajlar" class="chat-main-icon" draggable="false">
    <span id="chat-unread-dot" class="chat-unread-badge" style="display: none;"></span>
</div>

<!-- MESAJLAŞMA POP-UP PENCERESİ (KOMPAKT & KÜÇÜLTÜLMÜŞ) -->
<div id="chat-popup-container" class="chat-popup" style="display: none;">
    <!-- SOL PANEL: PROFİL FOTOĞRAFLARI -->
    <div class="chat-friends-sidebar" id="chat-friends-list"></div>

    <!-- SAĞ PANEL: SOHBET ALANI -->
    <div class="chat-main-area">
        <!-- ÜST BAŞLIK (PROFİLE GİDEN LİNK) -->
        <div class="chat-header">
            <a href="#" id="chat-header-user" class="chat-header-user" style="display: none; text-decoration: none; cursor: pointer;">
                <img id="chat-active-avatar" src="" alt="Avatar" class="chat-header-avatar">
                <span id="chat-active-name" class="chat-header-name"></span>
            </a>
            <div id="chat-header-placeholder" class="chat-header-placeholder">
                Choose a friend to start chatting.
            </div>
            <button type="button" class="chat-close-btn" id="chat-close-btn">&times;</button>
        </div>

        <!-- MESAJ AKIŞI -->
        <div class="chat-messages-body" id="chat-messages-body">
            <div class="chat-empty-state" id="chat-empty-state">
                You didn't pick anyone yet :<
            </div>
        </div>

        <!-- 6 ADET STICKER PANELİ -->
        <div class="chat-sticker-picker" id="chat-sticker-picker" style="display: none;">
            <img src="{{ asset('images/sticker1.png') }}" class="sticker-item" data-sticker="sticker1.png" alt="Sticker 1" draggable="false">
            <img src="{{ asset('images/sticker2.png') }}" class="sticker-item" data-sticker="sticker2.png" alt="Sticker 2" draggable="false">
            <img src="{{ asset('images/sticker3.png') }}" class="sticker-item" data-sticker="sticker3.png" alt="Sticker 3" draggable="false">
            <img src="{{ asset('images/sticker4.png') }}" class="sticker-item" data-sticker="sticker4.png" alt="Sticker 4" draggable="false">
            <img src="{{ asset('images/sticker5.png') }}" class="sticker-item" data-sticker="sticker5.png" alt="Sticker 5" draggable="false">
            <img src="{{ asset('images/sticker6.png') }}" class="sticker-item" data-sticker="sticker6.png" alt="Sticker 6" draggable="false">
        </div>

        <!-- GİRDİ FORMU (DAHA BÜYÜK BUTONLARLA) -->
        <form id="chat-input-form" class="chat-input-area">
            <button type="button" id="chat-toggle-sticker" class="chat-action-btn" title="Sticker Seç">
                <img src="{{ asset('images/sticker-icon.png') }}" alt="Sticker" class="chat-btn-icon sticker-btn-icon" draggable="false">
            </button>
            <input type="text" id="chat-message-input" placeholder="Write a message..." autocomplete="off" disabled>
            <button type="submit" id="chat-send-btn" class="chat-action-btn" title="Gönder" disabled>
                <img src="{{ asset('images/send-icon.png') }}" alt="Gönder" class="chat-btn-icon" draggable="false">
            </button>
        </form>
    </div>
</div>

<style>
/* SÜRÜKLENEBİLİR BUTON: SOL ALTTA & BÜYÜK (95px) */
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
.chat-friend-item {
    position: relative;
    cursor: pointer;
}

/* Arkadaşın avatarı üstündeki kırmızı nokta */
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
/* POP-UP: KÜÇÜK VE KOMPAKT (360px x 430px) */
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
.chat-friend-item {
    cursor: pointer;
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

/* SAĞ PANEL */
.chat-main-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    position: relative;
    background: #fff;
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
    color: #ffeaf9;
}

/* MESAJ ALANI & WALLPAPER */
.chat-messages-body {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: url('{{ asset("images/chat-bg.jpg") }}') repeat center center;
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

/* DAKİKA */
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

/* ALT GİRDİ ÇUBUĞU */
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
.chat-messages-body::-webkit-scrollbar-track {
    background: transparent;
}
.chat-messages-body::-webkit-scrollbar-thumb {
    background: rgb(255, 198, 106);
    border-radius: 10px;
}
.chat-messages-body::-webkit-scrollbar-thumb:hover {
    background: rgb(221, 153, 94);
}
.chat-messages-body {
    scrollbar-width: normal;
    scrollbar-color: rgb(221, 243, 205) transparent;
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

    const csrfToken = "{{ csrf_token() }}";
    let activeFriendId = null;
    let isDragging = false;
    let shiftX, shiftY;
    let lastLoadedMessagesJson = '';

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
            loadFriends();
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
    });

    closeBtn.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    async function loadFriends() {
        try {
            const res = await fetch('/messages/friends');
            const friends = await res.json();
            friendsList.innerHTML = '';

            friends.forEach(friend => {
                const item = document.createElement('div');
                item.className = `chat-friend-item ${activeFriendId === friend.id ? 'active' : ''}`;
                
                const badgeHtml = friend.unread_count > 0 
                    ? '<span class="chat-friend-dot"></span>' 
                    : '';

                item.innerHTML = `
                    <img src="${friend.avatar}" class="chat-friend-avatar" alt="${friend.username}">
                    ${badgeHtml}
                `;
                item.addEventListener('click', () => selectFriend(friend));
                friendsList.appendChild(item);
            });
        } catch (e) {
            console.error(e);
        }
    }

    async function selectFriend(friend) {
        activeFriendId = friend.id;
        lastLoadedMessagesJson = ''; // Yeni arkadaş seçilince sıfırla
        document.querySelectorAll('.chat-friend-item').forEach(el => el.classList.remove('active'));
        loadFriends();

        headerPlaceholder.style.display = 'none';
        headerUser.style.display = 'flex';
        headerUser.href = `/profile/${friend.id}`;
        
        activeAvatar.src = friend.avatar;
        activeName.textContent = friend.username;

        messageInput.disabled = false;
        sendBtn.disabled = false;
        messageInput.focus();

        await loadMessages(true); // İlk açılışta zorla en alta kaydır
        checkUnread();
    }

    async function loadMessages(forceScroll = false) {
        if (!activeFriendId) return;
        try {
            const res = await fetch(`/messages/${activeFriendId}`);
            const messages = await res.json();
            
            // Eğer yeni bir mesaj gelmemişse ve zorunlu kaydırma istenmiyorsa DOM'u baştan çizme (böylece scroll bozulmaz)
            const stringified = JSON.stringify(messages);
            if (stringified === lastLoadedMessagesJson && !forceScroll) {
                return;
            }
            lastLoadedMessagesJson = stringified;

            // Kullanıcı zaten en altta mı yoksa yukarıda eski mesajları mı okuyor kontrol et
            const threshold = 60; // piksel toleransı
            const isNearBottom = (messagesBody.scrollHeight - messagesBody.scrollTop - messagesBody.clientHeight) <= threshold;
            const previousScrollTop = messagesBody.scrollTop;

            messagesBody.innerHTML = '';
            if (messages.length === 0) {
                messagesBody.innerHTML = '<div class="chat-empty-state">Henüz mesaj yok. İlk mesajı sen at!</div>';
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
                        <img src="/images/${stickerName}" class="chat-bubble-sticker" alt="Sticker">
                        <div class="chat-bubble-time">${msg.time}</div>
                    `;
                } else {
                    bubble.innerHTML = `
                        <div>${escapeHtml(msg.message)}</div>
                        <div class="chat-bubble-time">${msg.time}</div>
                    `;
                }
                messagesBody.appendChild(bubble);
            });

            // Sadece zorunluysa (mesaj atınca / ilk açılışta) veya kullanıcı zaten en alttaysa aşağı kaydır
            if (forceScroll || isNearBottom) {
                messagesBody.scrollTop = messagesBody.scrollHeight;
            } else {
                messagesBody.scrollTop = previousScrollTop; // Kullanıcının kaldığı yeri koru
            }
        } catch (e) {
            console.error(e);
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
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    receiver_id: activeFriendId,
                    message: text
                })
            });

            if (res.ok) {
                messageInput.value = '';
                stickerPicker.style.display = 'none';
                await loadMessages(true); // Gönderdiğinde en alta insin
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
            const stickerCode = `[sticker:${item.dataset.sticker}]`;
            sendMessage(stickerCode);
        });
    });

    async function checkUnread() {
        try {
            const res = await fetch('/messages/unread-count');
            const data = await res.json();
            unreadDot.style.display = data.unread_count > 0 ? 'block' : 'none';
            
            if (popup.style.display === 'flex') {
                loadFriends();
                if (activeFriendId) {
                    loadMessages(false); // Otomatik yenilemede scroll'u zorlama
                }
            }
        } catch (e) {}
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    checkUnread();
    setInterval(checkUnread, 4000);
});
</script>