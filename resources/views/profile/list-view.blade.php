@if($isOwnProfile ?? false)
<form id="bulkDeleteForm" action="{{ route('profile.books.bulkRemove') }}" method="POST" onsubmit="return confirm(@json(__('Are you sure you want to remove the selected books?')));" style="margin: 0; display: flex; flex-direction: column; height: 100%; width: 100%; flex: 1; min-height: 0;">
    @csrf
    {{-- Toplu Silme Butonu --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 6px;">
        <button type="submit" id="btnBulkDelete" style="display: none; background: #d93838; color: #ffffff; border: none; padding: 5px 12px; border-radius: 12px; font-family: 'Unkempt', cursive; font-size: 13px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 6px rgba(217,56,56,0.25);">
            🗑️ {{ __('Delete Selected') }} (<span id="selectedCount">0</span>)
        </button>
    </div>
@endif

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