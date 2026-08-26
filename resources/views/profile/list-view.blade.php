<div id="profile-list-view" class="custom-scroll" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; padding-right: 10px;">
    
    @if($isOwnProfile ?? false)
    <form id="bulkDeleteForm" action="{{ route('profile.books.bulkRemove') }}" method="POST" onsubmit="return confirm('Seçtiğin kitapları silmek istediğine emin misin?');" style="margin: 0;">
        @csrf
    @endif

    {{-- DURUM SEKMELERİ & SİLME BUTONU --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" onclick="filterStatus('all', this)" class="status-tab" style="border: none; background: #255719; color: #fff; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                All ({{ $userBooks->count() }})
            </button>
            <button type="button" onclick="filterStatus('read', this)" class="status-tab" style="border: 1px solid #737e3d; background: #eaf3e4; color: #1a3c11; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                read ({{ $userBooks->where('status', 'read')->count() }})
            </button>
            <button type="button" onclick="filterStatus('reading', this)" class="status-tab" style="border: 1px solid #737e3d; background: #eaf3e4; color: #1a3c11; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                currently reading ({{ $userBooks->where('status', 'reading')->count() }})
            </button>
            <button type="button" onclick="filterStatus('toRead', this)" class="status-tab" style="border: 1px solid #737e3d; background: #eaf3e4; color: #1a3c11; padding: 6px 14px; border-radius: 20px; font-family: 'Unkempt', cursive; font-size: 13px; cursor: pointer;">
                to read ({{ $userBooks->where('status', 'toRead')->count() }})
            </button>
        </div>

        @if($isOwnProfile ?? false)
            <button type="submit" id="btnBulkDelete" style="display: none; background: #c62828; color: #ffffff; border: none; padding: 6px 14px; border-radius: 16px; font-family: 'Unkempt', cursive; font-size: 13px; font-weight: bold; cursor: pointer; transition: transform 0.15s ease;" onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform='scale(1)'">
                🗑️ Seçilenleri Sil (<span id="selectedCount">0</span>)
            </button>
        @endif
    </div>

    {{-- KİTAP LİSTESİ --}}
    <div style="display: flex; flex-direction: column; gap: 14px;">
        @forelse($userBooks as $item)
            @include('profile.book-card', ['item' => $item])
        @empty
            <div style="text-align: center; color: #6c8c5a; padding: 40px 0; font-size: 15px;">
                Henüz eklenmiş bir kitap bulunmuyor. 🌱
            </div>
        @endforelse
    </div>

    @if($isOwnProfile ?? false)
    </form>
    @endif

</div>

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