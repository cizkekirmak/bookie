@if($isOwnProfile ?? false)
<form id="bulkDeleteForm" action="{{ route('profile.books.bulkRemove') }}" method="POST" onsubmit="return confirm(@json(__('Are you sure you want to remove the selected books?')));" style="margin: 0; display: flex; flex-direction: column; height: 100%; width: 100%; flex: 1; min-height: 0;">
    @csrf
@endif

    {{-- EKOSE TEMALI RENKLİ DURUM SEKMELERİ & SİLME BUTONU --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; flex-shrink: 0;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            {{-- Tümü (Mercan / Şeftali) --}}
            <button type="button" 
                    onclick="filterStatus('all', this)" 
                    class="status-tab" 
                    data-type="all"
                    style="border: 2px solid #e06350; background: #ff7d6b; color: #ffffff; padding: 6px 16px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 14.5px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 5px rgba(224,99,80,0.25); transition: all 0.15s ease;">
                {{ __('All') }} ({{ $userBooks->count() }})
            </button>

            {{-- Okundu (Çilek Pembesi) --}}
            <button type="button" 
                    onclick="filterStatus('read', this)" 
                    class="status-tab" 
                    data-type="read"
                    style="border: 1.5px solid #f8b4c2; background: #ffe5eb; color: #b32d4e; padding: 6px 16px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 14.5px; font-weight: bold; cursor: pointer; transition: all 0.15s ease;">
                {{ __('read') }} ({{ $userBooks->where('status', 'read')->count() }})
            </button>

            {{-- Şu An Okuyor (Gök Mavisi) --}}
            <button type="button" 
                    onclick="filterStatus('reading', this)" 
                    class="status-tab" 
                    data-type="reading"
                    style="border: 1.5px solid #b9dcf7; background: #e2f2fc; color: #21638a; padding: 6px 16px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 14.5px; font-weight: bold; cursor: pointer; transition: all 0.15s ease;">
                {{ __('currently reading') }} ({{ $userBooks->where('status', 'reading')->count() }})
            </button>

            {{-- Okunacak (Tereyağı Sarısı) --}}
            <button type="button" 
                    onclick="filterStatus('toRead', this)" 
                    class="status-tab" 
                    data-type="toRead"
                    style="border: 1.5px solid #fae18c; background: #fff4cc; color: #8f680a; padding: 6px 16px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 14.5px; font-weight: bold; cursor: pointer; transition: all 0.15s ease;">
                {{ __('to read') }} ({{ $userBooks->where('status', 'toRead')->count() }})
            </button>
        </div>

        @if($isOwnProfile ?? false)
            <button type="submit" id="btnBulkDelete" style="display: none; background: #d93838; color: #ffffff; border: none; padding: 6px 16px; border-radius: 16px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 6px rgba(217,56,56,0.25); transition: transform 0.15s ease;" onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform='scale(1)'">
                🗑️ {{ __('Delete Selected') }} (<span id="selectedCount">0</span>)
            </button>
        @endif
    </div>

    {{-- KİTAP LİSTESİ: AKAN KAYDIRMA ALANI --}}
    <div class="custom-scroll book-items-scroll-area" style="flex: 1 1 0%; min-height: 0; height: 100%; overflow-y: auto; -webkit-overflow-scrolling: touch; display: flex; flex-direction: column; gap: 12px; padding-right: 4px; padding-bottom: 36px;">
        @forelse($userBooks as $item)
            @include('profile.book-card', ['item' => $item])
        @empty
            <div style="text-align: center; color: #6c8c5a; font-family: 'Unkempt', cursive; padding: 40px 0; font-size: 16px;">
                @if($isOwnProfile ?? false)
                    {{ __("You didn't save any books yet, you should start somewhere") }}
                @else
                    {{ __("This user hasn't saved any books yet.") }}
                @endif
            </div>
        @endforelse
    </div>

@if($isOwnProfile ?? false)
</form>
@endif

<script>
// Sekmeler arası geçişte her butonun kendi pastel rengini korumasını sağlar
window.filterStatus = function(status, clickedBtn) {
    const tabStyles = {
        'all': { bg: '#dd92dc', activeBg: '#ae4f90', color: '#fff', border: '#a7559d', shadow: 'rgba(72, 23, 69, 0.3)' },
        'read': { bg: '#ffe5eb', activeBg: '#d64b6f', color: '#b32d4e', border: '#f8b4c2', shadow: 'rgba(214,75,111,0.3)' },
        'reading': { bg: '#e2f2fc', activeBg: '#21638a', color: '#21638a', border: '#b9dcf7', shadow: 'rgba(33,99,138,0.3)' },
        'toRead': { bg: '#fff4cc', activeBg: '#e2ac24', color: '#cda036', border: '#fae18c', shadow: 'rgba(170, 152, 34, 0.3)' }
    };

    document.querySelectorAll('.status-tab').forEach(btn => {
        const type = btn.getAttribute('data-type');
        const style = tabStyles[type];
        if (style) {
            btn.style.background = style.bg;
            btn.style.color = (type === 'all') ? '#fff' : style.color;
            btn.style.border = (type === 'all') ? '2px solid ' + style.border : '1.5px solid ' + style.border;
            btn.style.boxShadow = 'none';
        }
    });

    const activeType = clickedBtn.getAttribute('data-type');
    const activeStyle = tabStyles[activeType];
    if (activeStyle) {
        clickedBtn.style.background = activeStyle.activeBg;
        clickedBtn.style.color = '#ffffff';
        clickedBtn.style.border = '2px solid ' + activeStyle.activeBg;
        clickedBtn.style.boxShadow = '0 3px 8px ' + activeStyle.shadow;
    }

    const cards = document.querySelectorAll('.book-card-item');
    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        if (status === 'all' || cardStatus === status) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
};

function updateDeleteButtonState() {
    const checkboxes = document.querySelectorAll('.book-select-checkbox:checked');
    const deleteBtn = document.getElementById('btnBulkDelete');
    const countSpan = document.getElementById('selectedCount');

    if (deleteBtn && countSpan) {
        if (checkboxes.length > 0) {
            countSpan.innerText = checkboxes.length;
            deleteBtn.style.display = 'inline-block';
        } else {
            deleteBtn.style.display = 'none';
        }
    }
}
</script>