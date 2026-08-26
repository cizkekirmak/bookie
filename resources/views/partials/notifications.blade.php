@php
    $unreadNotifications = auth()->check() ? auth()->user()->unreadNotifications : collect([]);
    $pendingList = $pendingRequests ?? collect([]);
    $totalCount = $pendingList->count() + $unreadNotifications->count();
@endphp

<div class="notification-container" onmouseenter="openNotificationPanel()" onmouseleave="closeNotificationPanel()">
    
    <button type="button" class="notification-trigger-btn" onclick="toggleNotificationPanel()">
        <img src="{{ asset('images/bildirim.jpg') }}" alt="bildirimler" class="notification-icon-img"
             onmouseenter="this.style.transform='scale(1.1)'; this.style.filter='drop-shadow(0px 6px 8px rgba(0, 0, 0, 0.4))';"
             onmouseleave="this.style.transform='scale(1)'; this.style.filter='none';">
        @if($totalCount > 0)
            <span class="notification-badge">{{ $totalCount }}</span>
        @endif
    </button>

    <div id="notificationPanel" class="notification-dropdown-panel" style="display: none;">
        <div class="notification-header">
            <div>
                <span>Notifications</span>
                @if($totalCount > 0)
                    <span class="notification-subtext">{{ $totalCount }} new</span>
                @endif
            </div>

            @if(auth()->user()->notifications->count() > 0)
                <button type="button" onclick="clearAllNotifications(this)" style="background: none; border: none; font-size: 11px; color: #888; cursor: pointer; text-decoration: underline; padding: 0;">
                    Clear all
                </button>
            @endif
        </div>
        <div class="notification-list">
            @include("partials.notifications-items", ['pendingList' => $pendingList, "notifications" => auth()->user()->notifications])    
        </div>
    </div>
</div>

<style>
.notification-container {
    position: relative;
    display: inline-flex;
    align-items: center;
    line-height: 0;
}

.notification-trigger-btn {
    background: none;
    border: none;
    cursor: pointer;
    position: relative;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 0;
}

.notification-icon-img {
    width: 64px;
    height: 64px;
    object-fit: contain;
    margin: 0;
    display: block;
    border: 1.5px solid #4b813b;
    box-sizing: border-box;
}

.notification-badge {
    position: absolute;
    top: -6px;
    right: -10px;
    background: #d32f2f;
    color: #fff;
    font-size: 11px;
    font-weight: bold;
    border-radius: 50%;
    padding: 2px 6px;
    line-height: 1;
}

.notification-dropdown-panel {
    position: absolute;
    right: 0;
    top: 100%;
    width: 320px;
    max-height: 420px;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 1001;
    overflow: hidden;
    text-align: left;
    line-height: normal;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    font-weight: bold;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
    font-size: 14px;
    color: #333;
}

.notification-subtext {
    font-size: 11px;
    color: #666;
    background: #eee;
    padding: 2px 6px;
    border-radius: 8px;
}

.notification-list {
    overflow-y: auto;
    max-height: 320px;
    scrollbar-width: thin;
}
.notification-list::-webkit-scrollbar {
    width: 5px;
}
.notification-list::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}

.notification-item {
    display: flex;
    gap: 10px;
    padding: 10px 14px;
    border-bottom: 1px solid #f5f5f5;
}

.notification-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid #4c7237;
    background: #badfa0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
}

.notification-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.sender-name {
    font-size: 13px;
    font-weight: 600;
    color: #1a3c11;
    text-decoration: none;
}

.sender-name:hover {
    text-decoration: underline;
}

.notification-desc {
    font-size: 12px;
    color: #555;
}

.notification-actions {
    display: flex;
    gap: 6px;
    margin-top: 6px;
}

.btn-action-accept {
    background: #1a3c11;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 11px;
    cursor: pointer;
}

.btn-action-accept:hover {
    background: #255719;
}

.btn-action-reject {
    background: #eee;
    color: #444;
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 11px;
    cursor: pointer;
}

.btn-action-reject:hover {
    background: #ffdada;
    color: #c00;
}

.notification-empty {
    padding: 24px;
    text-align: center;
    color: #888;
    font-size: 13px;
}
</style>

<script>
let notifTimeout;

function openNotificationPanel() {
    clearTimeout(notifTimeout);
    const panel = document.getElementById('notificationPanel');

    if (panel) {
        panel.style.display = 'block';

        const badge = document.querySelector(".notification-badge") || document.querySelector(".badge") || document.getElementById("notifCount");

        if (badge && badge.style.display !== "none") {
            badge.style.transition = "opacity 0.3s ease, transform 0.3s ease";
            badge.style.opacity = "0";
            badge.style.transform = "scale(0.5)";
            setTimeout(() => {
                badge.style.display = "none";
            }, 300);

            const csrfToken = document.querySelector("meta[name='csrf-token']")?.getAttribute('content') || document.querySelector("input[name='_token']")?.value || '{{ csrf_token() }}';

            fetch("/notifications/mark-as-read", {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => console.log("Okundu sonucu:", data))
            .catch(err => console.error("Hata:", err));
        }
    }
}

function toggleNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    if (panel) {
        panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
    }
}

function closeNotificationPanel() {
    notifTimeout = setTimeout(function() {
        const panel = document.getElementById('notificationPanel');
        if (panel) {
            panel.style.display = 'none';
        }
    }, 200);
}

function handleFriendAction(event, formElement) {
    event.preventDefault();

    const formData = new FormData(formElement);
    const actionUrl = formElement.action;
    const notificationItem = formElement.closest(".notification-item");
    const isReject = actionUrl.includes('reject');

    fetch(actionUrl, {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    })
    .then(response => {
        if (response.ok) {
            const actionsDiv = notificationItem.querySelector(".notification-actions");
            if (actionsDiv) {
                actionsDiv.remove();
            }

            const descSpan = notificationItem.querySelector(".notification-desc");
            if (descSpan) {
                descSpan.innerText = isReject 
                    ? "'s friend request was declined." 
                    : "'s friend request was accepted !";
            }

            const badge = document.querySelector('.notification-badge') || document.getElementById("notifCount");
            if (badge) {
                let count = parseInt(badge.innerText, 10) - 1;
                if (!isNaN(count) && count > 0) {
                    badge.innerText = count;
                } else {
                    badge.remove();
                }
            }
        }
    })
    .catch(error => console.error("error:", error));
}

function clearAllNotifications(button) {
    const csrfToken = document.querySelector("meta[name='csrf-token']")?.getAttribute('content') 
                      || document.querySelector("input[name='_token']")?.value 
                      || '{{ csrf_token() }}';

    fetch('/notifications/clear-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const list = document.querySelector('.notification-list');
                if (list) {
                   list.innerHTML = '<div class="notification-empty">No notifications yet </div>';
                }
            if (button) button.remove();
        }
    })
    .catch(err => console.error('Clear error:', err));
}

function checkNewNotifications() {
    fetch("{{ route('notifications.unreadCount') }}", {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        const triggerBtn = document.querySelector('.notification-trigger-btn');
        let badge = document.querySelector('.notification-badge');

        if (data.has_unread && data.total > 0) {
            if (badge) {
                badge.innerText = data.total;
            } else if (triggerBtn) {
                const newBadge = document.createElement('span');
                newBadge.className = 'notification-badge';
                newBadge.innerText = data.total;
                triggerBtn.appendChild(newBadge);
            }

            fetch("{{ route('notifications.unreadCount') }}?render_list=1", {
                headers: {'X-Requested-With' : 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(htmlData => {
                if (htmlData.html) {
                    const list = document.querySelector('.notification-list');
                    if (list) list.innerHTML = htmlData.html;
                }
            }).catch(() => {});
        } else if (badge && data.total === 0) {
            badge.remove();
        }
    })
    .catch(err => console.error('Bildirim kontrol hatası:', err));
}

setInterval(checkNewNotifications, 15000);
</script>