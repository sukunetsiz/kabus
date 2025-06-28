@if ($paginator->hasPages())
    <nav class="pagination-container">
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-disabled">
                    <span class="pagination-prev">Previous</span>
                </li>
            @else
                <li class="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-prev">Previous</a>
                </li>
            @endif

            @php
                $totalPages = $paginator->lastPage();
                $currentPage = $paginator->currentPage();
                $showDropdown = $totalPages > 6;
                $firstPages = collect(range(1, min(2, $totalPages)));
                $lastPages = $totalPages > 2 ? collect(range(max(3, $totalPages - 1), $totalPages)) : collect();
                $middlePages = $showDropdown ? collect(range(3, $totalPages - 2))->filter(function($page) use ($firstPages, $lastPages) {
                    return !$firstPages->contains($page) && !$lastPages->contains($page);
                }) : collect();
            @endphp

            {{-- First Pages --}}
            @foreach ($firstPages as $page)
                @if ($page == $currentPage)
                    <li class="pagination-active">
                        <span class="pagination-number">{{ $page }}</span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a href="{{ $paginator->url($page) }}" class="pagination-link">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Dropdown for Middle Pages --}}
            @if ($showDropdown && $middlePages->isNotEmpty())
                <li class="pagination-item pagination-dropdown-container">
                    <div class="pagination-dropdown">
                        <span class="pagination-dropdown-trigger {{ $middlePages->contains($currentPage) ? 'pagination-dropdown-trigger-active' : '' }}">
                            <span class="pagination-dropdown-arrow">▼</span>
                        </span>
                        <div class="pagination-dropdown-menu">
                            @foreach ($middlePages as $page)
                                <a href="{{ $paginator->url($page) }}" 
                                   class="pagination-dropdown-item {{ $page == $currentPage ? 'pagination-dropdown-active' : '' }}">
                                    {{ $page }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </li>
            @elseif (!$showDropdown && $totalPages > 4)
                {{-- Show ellipsis for smaller ranges --}}
                @foreach (range(3, $totalPages - 2) as $page)
                    @if ($page == $currentPage)
                        <li class="pagination-active">
                            <span class="pagination-number">{{ $page }}</span>
                        </li>
                    @else
                        <li class="pagination-item">
                            <a href="{{ $paginator->url($page) }}" class="pagination-link">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif

            {{-- Last Pages --}}
            @foreach ($lastPages as $page)
                @if ($page == $currentPage)
                    <li class="pagination-active">
                        <span class="pagination-number">{{ $page }}</span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a href="{{ $paginator->url($page) }}" class="pagination-link">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-next">Next</a>
                </li>
            @else
                <li class="pagination-disabled">
                    <span class="pagination-next">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
