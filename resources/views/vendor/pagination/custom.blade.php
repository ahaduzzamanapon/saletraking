@if ($paginator->hasPages())
    <div style="display:flex;align-items:center;gap:4px">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="disabled" style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;font-size:13px;color:var(--text-muted);opacity:.4">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;font-size:13px;color:var(--text-secondary);text-decoration:none;border:1px solid var(--border);transition:all .15s" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='transparent'">‹</a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;color:var(--text-muted)">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;font-size:13px;background:var(--accent);color:#fff;font-weight:600">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;font-size:13px;color:var(--text-secondary);text-decoration:none;border:1px solid transparent;transition:all .15s" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='transparent'">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;font-size:13px;color:var(--text-secondary);text-decoration:none;border:1px solid var(--border);transition:all .15s" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='transparent'">›</a>
        @else
            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;font-size:13px;color:var(--text-muted);opacity:.4">›</span>
        @endif

        <span style="margin-left:8px;font-size:12px;color:var(--text-muted)">{{ $paginator->total() }} total</span>
    </div>
@endif
