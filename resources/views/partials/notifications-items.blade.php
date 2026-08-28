<div class="notification-list">
    @foreach($pendingList as $req)
        @php
            $sender = $req->sender ?? null;
            $defaultAvatar = asset('images/profile.jpg');
            
            $avatarSrc = $defaultAvatar;
            if (!empty($sender?->avatar)) {
                $avatarSrc = str_starts_with($sender->avatar, 'http') 
                    ? $sender->avatar 
                    : asset('storage/' . $sender->avatar);
            }
        @endphp

        <div class="notification-item" style="display: flex; gap: 10px; padding: 10px 12px; border-bottom: 1px solid #f0f4ec; align-items: flex-start;">
            {{-- Profil Fotoğrafı Linki --}}
            <a href="{{ route('profile', $sender?->id ?? '#') }}" 
               class="notification-avatar" 
               style="width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid #4c7237; overflow: hidden; flex-shrink: 0; background: #badfa0; display: flex; align-items: center; justify-content: center; text-decoration: none; cursor: pointer;">
                <img src="{{ $avatarSrc }}" 
                     alt="{{ $sender?->username ?? 'Profil' }}" 
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;" 
                     onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
            </a>

            <div class="notification-info" style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px;">
                <a href="{{ route('profile', $sender?->id ?? '#') }}" class="sender-name" style="font-size: 13px; font-weight: bold; color: #1a3c11; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $sender?->name ?? ($sender?->username ?? 'User') }}
                </a>
                <span class="notification-desc" style="font-size: 12px; color: #555;">sent you a friend request.</span>

                <div class="notification-actions" style="display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap;">
                    <form action="{{ route('friends.accept', $sender?->id) }}" method="POST" onsubmit="handleFriendAction(event, this)" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn-action-accept" style="background: #1a3c11; color: #fff; border: none; border-radius: 6px; padding: 4px 10px; font-size: 11px; cursor: pointer; font-family: 'Unkempt', cursive;">Accept ^^</button>
                    </form>
                    <form action="{{ route('friends.reject', $sender?->id) }}" method="POST" onsubmit="handleFriendAction(event, this)" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn-action-reject" style="background: #eee; color: #444; border: none; border-radius: 6px; padding: 4px 10px; font-size: 11px; cursor: pointer; font-family: 'Unkempt', cursive;">Reject :<</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach(auth()->user()->notifications as $notification)
        @php 
            $notifData = $notification->data ?? [];
            $senderId = $notifData['sender_id'] ?? ($notifData['user_id'] ?? null);
            $notifUser = $senderId ? \App\Models\User::find($senderId) : null;
            $defaultAvatar = asset('images/profile.jpg');
            
            $notifAvatarSrc = $defaultAvatar;
            if (!empty($notifUser?->avatar)) {
                $notifAvatarSrc = str_starts_with($notifUser->avatar, 'http')
                    ? $notifUser->avatar
                    : asset('storage/' . $notifUser->avatar);
            }

            $bookKey = $notifData['google_book_id'] 
                ?? $notifData['open_library_key'] 
                ?? $notifData['book_key'] 
                ?? $notifData['book_id'] 
                ?? null;
        @endphp

        {{-- A) BEĞENİ BİLDİRİMİ --}}
        @if(($notifData['type'] ?? '') === 'review_liked')
            <div class="notification-item" 
                 @if($bookKey) onclick="window.location='{{ route('show', $bookKey) }}#review-{{ $notifData['review_id'] ?? '' }}'" @endif
                 style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f0f4ec; transition: background 0.15s ease;"
                 onmouseenter="this.style.background='#f7fbf4'"
                 onmouseleave="this.style.background='transparent'">
                
                {{-- Kalp Rozetli Profil Fotoğrafı --}}
                <div style="position: relative; width: 36px; height: 36px; flex-shrink: 0;">
                    <a href="{{ route('profile', $senderId ?? '#') }}" 
                       onclick="event.stopPropagation();" 
                       style="display: block; width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid #4c7237; overflow: hidden; background: #badfa0; cursor: pointer; text-decoration: none;">
                        <img src="{{ $notifAvatarSrc }}" 
                             alt="{{ $notifData['sender_name'] ?? 'Profil' }}" 
                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;" 
                             onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                    </a>
                    <span style="position: absolute; bottom: -2px; right: -2px; background: #ffffff; border-radius: 50%; font-size: 11px; line-height: 1; padding: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); pointer-events: none;">❤️</span>
                </div>
                
                <div class="notification-info" style="flex: 1; min-width: 0;">
                    <div>
                        <a href="{{ route('profile', $senderId ?? '#') }}" 
                           onclick="event.stopPropagation();"
                           class="sender-name" 
                           style="color: #1a3c11; font-weight: bold; text-decoration: none; font-size: 13px;">
                            {{ $notifData['sender_name'] ?? ($notifUser?->username ?? 'User') }}
                        </a>
                        <span class="notification-desc" style="font-size: 12px; color: #555;">{{ $notifData['message'] ?? 'liked your review.' }}</span>
                    </div>
                    
                    <small style="display:block; font-size:11px; color:#888; margin-top:2px;">
                        {{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}
                    </small>
                </div>
            </div>

        {{-- B) DİĞER BİLDİRİMLER (Arkadaşlık Kabul vs.) --}}
        @else
            <div class="notification-item" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-bottom: 1px solid #f0f4ec;">
                {{-- Tıklanabilir Profil Fotoğrafı --}}
                <a href="{{ route('profile', $senderId ?? '#') }}" 
                   style="display: flex; width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid #4c7237; overflow: hidden; flex-shrink: 0; background: #badfa0; align-items: center; justify-content: center; text-decoration: none; cursor: pointer;">
                    <img src="{{ $notifAvatarSrc }}" 
                         alt="{{ $notifData['sender_name'] ?? 'Profil' }}" 
                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;" 
                         onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                </a>

                <div class="notification-info" style="flex: 1; min-width: 0;">
                    <a href="{{ route('profile', $senderId ?? '#') }}" class="sender-name" style="color: #1a3c11; font-weight: bold; text-decoration: none; font-size: 13px;">
                        {{ $notifData['sender_name'] ?? ($notifUser?->username ?? 'User') }}
                    </a>
                    @if(($notifData['type'] ?? '') === 'accepted_self')
                        <span class="notification-desc" style="font-size: 12px; color: #555;">'s friend request was accepted !</span>
                    @else
                        <span class="notification-desc" style="font-size: 12px; color: #555;">{{ $notifData['message'] ?? 'accepted your friend request.' }}</span>
                    @endif
                    <small style="display:block; font-size:11px; color:#888; margin-top:2px;">
                        {{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}
                    </small>
                </div>
            </div>
        @endif
    @endforeach

    @if(count($pendingList ?? []) === 0 && auth()->user()->notifications->count() === 0)
        <div class="notification-empty" style="text-align: center; padding: 18px 10px; color: #888; font-size: 13px; font-family: 'Unkempt', cursive;">
            No notifications yet ^^
        </div>
    @endif
</div>