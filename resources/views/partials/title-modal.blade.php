@php
    $userFinishedCount = $user->finishedBooksCount();
    $titlesList = \App\Models\User::allTitles();
@endphp

{{-- MODAL BACKDROP --}}
<div id="titlesModalBackdrop" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.45);
    z-index: 999999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
" onclick="closeTitlesModal(event)">

    {{-- MODAL KUTUSU --}}
    <div style="
        background: #eef7ea;
        border: 2px solid #4c7237;
        border-radius: 16px;
        width: 480px;
        max-width: 90vw;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        overflow: hidden;
        font-family: 'Unkempt', cursive;
        box-sizing: border-box;
    " onclick="event.stopPropagation()">

        {{-- BAŞLIK --}}
        <div style="
            background: #badfa0;
            padding: 12px 18px;
            border-bottom: 2px solid #82b564;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        ">
            <div>
                <span style="font-size: 18px; font-weight: bold; color: #1a3c11;">Reading Titles</span>
                <div style="font-size: 12px; color: #355e28;">
                    read: <strong>{{ $userFinishedCount }}</strong>
                </div>
            </div>
            <button type="button" onclick="closeTitlesModal()" style="
                background: transparent;
                border: none;
                font-size: 20px;
                color: #1a3c11;
                cursor: pointer;
                font-weight: bold;
                padding: 0 4px;
            ">✕</button>
        </div>

        {{-- UNVAN LİSTESİ --}}
        <div class="custom-scroll" style="
            padding: 14px; 
            overflow-y: auto; 
            display: flex; 
            flex-direction: column; 
            gap: 10px;
            box-sizing: border-box;
        ">
            @foreach($titlesList as $t)
                @php
                    $unlocked = $userFinishedCount >= $t['req'];
                    $needed = $t['req'] - $userFinishedCount;
                @endphp

                <div style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 10px 14px;
                    border-radius: 10px;
                    border: 1.5px solid {{ $unlocked ? '#4c7237' : '#c5d6bc' }};
                    background: {{ $unlocked ? '#ffffff' : '#e3eedc' }};
                    opacity: {{ $unlocked ? '1' : '0.65' }};
                    box-sizing: border-box;
                    width: 100%;
                ">
                    {{-- SOL TARAF: İKON + İSİM + AÇIKLAMA --}}
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if($unlocked)
                            <span style="font-size: 22px; width: 26px; text-align: center; display: inline-block;">{{ $t['icon'] }}</span>
                        @else
                            <img src="{{ asset('images/lock.png') }}" alt="Locked" style="width: 30px; height: 30px; object-fit: contain; display: block;" onerror="this.outerHTML='🔒'">
                        @endif

                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 14px; font-weight: bold; color: {{ $unlocked ? '#1a3c11' : '#444' }};">
                                {{ $t['name'] }}
                            </span>
                            <span style="font-size: 11px; color: #666;">
                                {{ $t['desc'] }}
                            </span>
                        </div>
                    </div>

                    {{-- SAĞ TARAF: DURUM ETİKETİ --}}
                    <div style="font-size: 11px; font-weight: bold; flex-shrink: 0; margin-left: 10px;">
                        @if($unlocked)
                            <span style="color: #2e7d32; background: #e8f5df; border: 1px solid #7bb35c; padding: 2px 8px; border-radius: 8px;">
                                ✓ Unlocked
                            </span>
                        @else
                            <span style="color: #666; background: #d7e4d0; padding: 2px 8px; border-radius: 8px;">
                                {{ $t['req'] }} kitap (kalan: {{ $needed }})
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<script>
function openTitlesModal() {
    const m = document.getElementById('titlesModalBackdrop');
    if (m) m.style.display = 'flex';
}

function closeTitlesModal(e) {
    if (e && e.target !== e.currentTarget) return;
    const m = document.getElementById('titlesModalBackdrop');
    if (m) m.style.display = 'none';
}
</script>