@props([
    'items'  => [],
    'active' => '',
    'route'  => false,
])

<div
    @if (!$route)
    x-data="{ activeTab: '{{ $active }}' }"
    @endif
    class="pq-tabs-wrapper"
>
    <nav class="pq-tabs-bar" role="tablist">
        @foreach ($items as $item)
            @php
                $tabId    = $item['id'];
                $tabLabel = $item['label'];
                $tabIcon  = $item['icon']  ?? null;
                $tabBadge = $item['badge'] ?? null;
                $tabHref  = $item['href']  ?? '#';
            @endphp

            @if ($route)
                <a
                    href="{{ $tabHref }}"
                    role="tab"
                    @class(['pq-tab', 'pq-tab-active' => $active === $tabId])
                    @if ($active === $tabId) aria-selected="true" @endif
                >
                    @if ($tabIcon)
                        <i data-lucide="{{ $tabIcon }}" style="width:16px;height:16px;flex-shrink:0;"></i>
                    @endif
                    {{ $tabLabel }}
                    @if ($tabBadge !== null)
                        <x-badge variant="info" size="sm">{{ $tabBadge }}</x-badge>
                    @endif
                </a>
            @else
                <button
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === '{{ $tabId }}'"
                    @click="activeTab = '{{ $tabId }}'; $dispatch('tab-changed', { tab: '{{ $tabId }}' })"
                    :class="{ 'pq-tab-active': activeTab === '{{ $tabId }}' }"
                    class="pq-tab"
                >
                    @if ($tabIcon)
                        <i data-lucide="{{ $tabIcon }}" style="width:16px;height:16px;flex-shrink:0;"></i>
                    @endif
                    {{ $tabLabel }}
                    @if ($tabBadge !== null)
                        <x-badge variant="info" size="sm">{{ $tabBadge }}</x-badge>
                    @endif
                </button>
            @endif
        @endforeach
    </nav>

    <div class="pq-tabs-panels">
        {{ $slot }}
    </div>
</div>
