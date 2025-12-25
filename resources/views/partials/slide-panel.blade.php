{{-- Slide-out Panel Component --}}
{{-- Usage: @include('partials.slide-panel', ['id' => 'panelId', 'title' => 'Panel Title']) --}}

<div class="slide-panel-overlay" id="{{ $id }}Overlay" onclick="closeSlidePanel('{{ $id }}')"></div>

<div class="slide-panel" id="{{ $id }}">
    <div class="slide-panel-header">
        <h3 class="slide-panel-title">{{ $title ?? 'Panel' }}</h3>
        <button type="button" class="slide-panel-close" onclick="closeSlidePanel('{{ $id }}')" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="slide-panel-body">
        {{ $slot ?? '' }}
    </div>
    @if(isset($footer))
    <div class="slide-panel-footer">
        {{ $footer }}
    </div>
    @endif
</div>

@once
@push('scripts')
<script>
    function openSlidePanel(panelId) {
        document.getElementById(panelId).classList.add('show');
        document.getElementById(panelId + 'Overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSlidePanel(panelId) {
        document.getElementById(panelId).classList.remove('show');
        document.getElementById(panelId + 'Overlay').classList.remove('show');
        document.body.style.overflow = '';
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.slide-panel.show').forEach(panel => {
                closeSlidePanel(panel.id);
            });
        }
    });
</script>
@endpush
@endonce
