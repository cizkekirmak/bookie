@if($isOwnProfile ?? false)
<form id="bulkDeleteForm" action="{{ route('profile.books.bulkRemove') }}" method="POST" onsubmit="return confirm(@json(__('Are you sure you want to remove the selected books?')));" style="margin: 0; display: flex; flex-direction: column; height: 100%; width: 100%; flex: 1; min-height: 0;">
    @csrf
@endif

    {{-- DURUM SEKMELERİ & SİLME BUTONU --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px; flex-shrink: 0;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" onclick="filterStatus('all', this)" class="status-tab" style="border: none; background: #255719; color: #fff; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                {{ __('All') }} ({{ $userBooks->count() }})
            </button>
            <button type="button" onclick="filterStatus('read', this)" class="status-tab" style="border: 1px solid #737e3d; background: #eaf3e4; color: #1a3c11; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                {{ __('read') }} ({{ $userBooks->where('status', 'read')->count() }})
            </button>
            <button type="button" onclick="filterStatus('reading', this)" class="status-tab" style="border: 1px solid #737e3d; background: #eaf3e4; color: #1a3c11; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                {{ __('currently reading') }} ({{ $userBooks->where('status', 'reading')->count() }})
            </button>
            <button type="button" onclick="filterStatus('toRead', this)" class="status-tab" style="border: 1px solid #737e3d; background: #eaf3e4; color: #1a3c11; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                {{ __('to read') }} ({{ $userBooks->where('status', 'toRead')->count() }})
            </button>
        </div>

        @if($isOwnProfile ?? false)
            <button type="submit" id="btnBulkDelete" style="display: none; background: #c62828; color: #ffffff; border: none; padding: 6px 14px; border-radius: 16px; font-family: 'Unkempt', cursive; font-size: 13px; font-weight: bold; cursor: pointer; transition: transform 0.15s ease;" onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform='scale(1)'">
                🗑️ {{ __('Delete Selected') }} (<span id="selectedCount">0</span>)
            </button>
        @endif
    </div>

    {{-- KİTAP LİSTESİ: BEYAZ ALANIN EN DİBİNE KADAR AKAN ASIL KAYDIRMA ALANI --}}
    <div class="custom-scroll book-items-scroll-area" style="flex: 1 1 0%; min-height: 0; height: 100%; overflow-y: auto; -webkit-overflow-scrolling: touch; display: flex; flex-direction: column; gap: 12px; padding-right: 4px; padding-bottom: 36px;">
        @forelse($userBooks as $item)
            @include('profile.book-card', ['item' => $item])
        @empty
            <div style="text-align: center; color: #6c8c5a; padding: 40px 0; font-size: 15px;">
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