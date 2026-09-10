@if($isOwnProfile ?? false)
<form id="bulkDeleteForm" action="{{ route('profile.books.bulkRemove') }}" method="POST" onsubmit="return confirm(@json(__('Are you sure you want to remove the selected books?')));" style="margin: 0; display: flex; flex-direction: column; height: 100%; width: 100%; flex: 1; min-height: 0;">
    @csrf
@endif

    {{-- EKOSE TEMALI PASTEL DURUM SEKMELERİ & ARAMA ÇUBUĞU & SİLME BUTONU --}}
    <div class="list-controls-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 10px; flex-shrink: 0; width: 100%;">
        
        {{-- Sol Dörtlü Pastel Butonlar (Mobilde 2x2 grid) --}}
        <div class="pastel-status-container" style="display: flex; gap: 8px; flex-shrink: 0;">
            {{-- Tümü (Pastel Nane Yeşili) --}}
            <button type="button" 
                    onclick="filterStatus('all', this)" 
                    class="status-tab" 
                    data-type="all"
                    style="border: 1.5px solid #9ccb86; background: #b8dfa4; color: #27521e; padding: 6px 14px; border-radius: 18px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 6px rgba(39, 82, 30, 0.15); transition: all 0.15s ease; white-space: nowrap;">
                {{ __('All') }} ({{ $userBooks->count() }})
            </button>

            {{-- Okundu (Pastel Çilek Pembesi) --}}
            <button type="button" 
                    onclick="filterStatus('read', this)" 
                    class="status-tab" 
                    data-type="read"
                    style="border: 1.5px solid #f7b1c0; background: #fee2e8; color: #8e2b42; padding: 6px 14px; border-radius: 18px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.15s ease; white-space: nowrap;">
                {{ __('read') }} ({{ $userBooks->where('status', 'read')->count() }})
            </button>

            {{-- Şu An Okuyor (Pastel Gök Mavisi) --}}
            <button type="button" 
                    onclick="filterStatus('reading', this)" 
                    class="status-tab" 
                    data-type="reading"
                    style="border: 1.5px solid #a8d3f5; background: #e2f0fc; color: #1e5579; padding: 6px 14px; border-radius: 18px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.15s ease; white-space: nowrap;">
                {{ __('currently reading') }} ({{ $userBooks->where('status', 'reading')->count() }})
            </button>

            {{-- Okunacak (Pastel Tereyağı Sarısı) --}}
            <button type="button" 
                    onclick="filterStatus('toRead', this)" 
                    class="status-tab" 
                    data-type="toRead"
                    style="border: 1.5px solid #fae087; background: #fef5d1; color: #7a5a0c; padding: 6px 14px; border-radius: 18px; font-family: 'Unkempt', cursive; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.15s ease; white-space: nowrap;">
                {{ __('to read') }} ({{ $userBooks->whereIn('status', ['toRead', 'want_to_read'])->count() }})
            </button>
        </div>

        {{-- Ortadaki Arama Çubuğu (Masaüstünde tek sırada uzanır) --}}
        <div class="search-input-wrapper" style="flex: 1; min-width: 130px;">
            <input type="text" 
                   id="profileBookSearchInput" 
                   oninput="applyCombinedFilter()" 
                   placeholder="🔍 {{ __('Search books or authors...') }}" 
                   autocomplete="off"
                   style="width: 100%; padding: 6px 12px; border-radius: 14px; border: 1.5px solid #8ec46f; background: #ffffff; font-family: 'Unkempt', cursive; font-size: 13.5px; color: #1a3c11; outline: none; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); box-sizing: border-box;">
        </div>

        {{-- Toplu Silme Butonu --}}
        @if($isOwnProfile ?? false)
            <button type="submit" id="btnBulkDelete" class="bulk-delete-btn" style="display: none; background: #d93838; color: #ffffff; border: none; padding: 6px 14px; border-radius: 16px; font-family: 'Unkempt', cursive; font-size: 13.5px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 6px rgba(217,56,56,0.25); transition: transform 0.15s ease; white-space: nowrap; flex-shrink: 0;" onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform='scale(1)'">
                🗑️ {{ __('Delete Selected') }} (<span id="selectedCount">0</span>)
            </button>
        @endif
    </div>

    {{-- KİTAP LİSTESİ: AKAN KAYDIRMA ALANI --}}
    <div class="custom-scroll book-items-scroll-area" style="flex: 1 1 0%; min-height: 0; height: 100%; overflow-y: auto; -webkit-overflow-scrolling: touch; display: flex; flex-direction: column; gap: 12px; padding-right: 4px; padding-bottom: 36px;">
        @forelse($userBooks as $item)
            @include('profile.book-card', ['item' => $item])
        @empty
            <div style="text-align: center; color: #6c8c5a; font-family: 'Unkempt', cursive; padding: 40px 0; font-size: 16.5px;">
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

<style>
    @media (max-width: 768px) {
        .list-controls-toolbar {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 8px !important;
        }

        /* 4 Butonu mobilde 2x2 grid yap */
        .pastel-status-container {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 6px !important;
            width: 100% !important;
        }

        .pastel-status-container .status-tab {
            padding: 5px 6px !important;
            font-size: 12px !important;
            text-align: center !important;
            border-radius: 12px !important;
        }

        .search-input-wrapper {
            width: 100% !important;
        }

        .search-input-wrapper input {
            font-size: 13px !important;
            padding: 6px 10px !important;
        }

        .bulk-delete-btn {
            align-self: flex-start !important;
        }
    }
</style>

<script>
let currentActiveStatus = 'all';

window.filterStatus = function(status, clickedBtn) {
    currentActiveStatus = status;

    const tabStyles = {
        'all':     { bg: '#dcedd2', activeBg: '#b8dfa4', color: '#27521e', border: '#9ccb86', shadow: 'rgba(39, 82, 30, 0.15)' },
        'read':    { bg: '#fee2e8', activeBg: '#fcc2ce', color: '#8e2b42', border: '#f7b1c0', shadow: 'rgba(142, 43, 66, 0.15)' },
        'reading': { bg: '#e2f0fc', activeBg: '#c2e1f9', color: '#1e5579', border: '#a8d3f5', shadow: 'rgba(30, 85, 121, 0.15)' },
        'toRead':  { bg: '#fef5d1', activeBg: '#fce9a5', color: '#7a5a0c', border: '#fae087', shadow: 'rgba(122, 90, 12, 0.15)' }
    };

    document.querySelectorAll('.status-tab').forEach(btn => {
        const type = btn.getAttribute('data-type');
        const style = tabStyles[type];
        if (style) {
            btn.style.background = style.bg;
            btn.style.color = style.color;
            btn.style.border = '1.5px solid ' + style.border;
            btn.style.boxShadow = 'none';
        }
    });

    const activeType = clickedBtn.getAttribute('data-type');
    const activeStyle = tabStyles[activeType];
    if (activeStyle) {
        clickedBtn.style.background = activeStyle.activeBg;
        clickedBtn.style.color = activeStyle.color;
        clickedBtn.style.border = '1.5px solid ' + activeStyle.border;
        clickedBtn.style.boxShadow = '0 2px 6px ' + activeStyle.shadow;
    }

    applyCombinedFilter();
};

function applyCombinedFilter() {
    const searchVal = (document.getElementById('profileBookSearchInput')?.value || '').toLowerCase().trim();
    const cards = document.querySelectorAll('.book-card-item');

    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        const title = (card.querySelector('h4')?.innerText || '').toLowerCase();
        const author = (card.querySelector('.book-card-item span')?.innerText || '').toLowerCase();

        let statusMatch = (currentActiveStatus === 'all');
        if (!statusMatch) {
            if (currentActiveStatus === 'toRead') {
                statusMatch = (cardStatus === 'toRead' || cardStatus === 'want_to_read');
            } else {
                statusMatch = (cardStatus === currentActiveStatus);
            }
        }

        const searchMatch = !searchVal || title.includes(searchVal) || author.includes(searchVal);

        if (statusMatch && searchMatch) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

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