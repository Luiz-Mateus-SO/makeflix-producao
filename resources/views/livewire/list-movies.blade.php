@if($films->isEmpty())
    <main class="mx-auto max-w-[1400px] px-7 py-12">
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-10 text-center">
            <h1 class="text-3xl font-black text-white">Seu catálogo está vazio</h1>
            <p class="mt-3 text-zinc-400">Cadastre o primeiro filme para começar sua coleção.</p>
            <a href="{{ route('create') }}" class="mt-6 inline-flex rounded-full bg-red-600 px-5 py-3 font-bold text-white transition hover:bg-red-700">Cadastrar filme</a>
        </div>
    </main>
@else
    @php
        $featuredFilm = $films->first();
        $featuredCover = Str::startsWith($featuredFilm->cover, ['http://', 'https://']) ? $featuredFilm->cover : asset('storage/' . $featuredFilm->cover);
        $featuredBackdrop = blank($featuredFilm->backdrop) ? $featuredCover : $featuredFilm->backdrop;
        $hasLocalPreview = file_exists(public_path('videos/featured-preview.mp4'));
        $hasTmdbPreview = ! blank($featuredFilm->preview_video);
        $hasPreview = $hasLocalPreview || $hasTmdbPreview;
    @endphp

    <main x-data="{ detailsOpen: false }" class="mx-auto max-w-[1400px] px-4 pb-12 sm:px-7">
        <div x-data="{ genresOpen: false }" class="relative z-40 mt-4 flex flex-wrap items-center justify-between gap-4 sm:mt-6">
            <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl">Filmes</h1>
            <div class="relative">
                <button type="button" @click="genresOpen = !genresOpen" class="flex items-center justify-center gap-2 rounded-full border border-white/30 bg-[#171717] px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:border-white hover:bg-zinc-800">
                    {{ $selectedGenre ?: 'Gêneros' }}
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 shrink-0 transition-transform duration-200" :class="genresOpen ? 'rotate-180' : ''" aria-hidden="true">
                        <path d="m5 7.5 5 5 5-5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <div x-cloak x-show="genresOpen" @click.outside="genresOpen = false" x-transition class="absolute right-0 mt-2 max-h-80 w-64 overflow-y-auto rounded-xl border border-white/10 bg-[#242424] p-2 shadow-2xl">
                    <a href="{{ route('home', array_filter(['search' => $search])) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-white transition hover:bg-white/10">Todos os gêneros</a>
                    @foreach($genres as $genre)
                        <a href="{{ route('home', array_filter(['search' => $search, 'genre' => $genre])) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold transition hover:bg-white/10 {{ $selectedGenre === $genre ? 'bg-white/15 text-white' : 'text-zinc-300' }}">{{ $genre }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        <section x-data="{ muted: localStorage.getItem('madeflixMuted') === '1' }" x-effect="localStorage.setItem('madeflixMuted', muted ? '1' : '0')" class="motion-safe:animate-fade-in relative mt-4 h-[460px] overflow-hidden rounded-[1.5rem] border border-white/10 bg-zinc-900 shadow-2xl shadow-black/40 sm:mt-5 sm:h-[520px] sm:rounded-[2rem] lg:h-[560px]">
            <img src="{{ $featuredBackdrop }}" alt="{{ $featuredFilm->title }}" class="absolute inset-0 h-full w-full object-cover">

            @if($hasLocalPreview)
                <video x-ref="preview" x-init="$nextTick(() => { $refs.preview.muted = muted; $refs.preview.play().catch(() => {}); })" src="{{ asset('videos/featured-preview.mp4') }}" autoplay loop playsinline class="absolute inset-0 h-full w-full object-cover"></video>
            @elseif($hasTmdbPreview)
                <iframe x-ref="preview" x-init="$el.addEventListener('load', () => { if (muted) $refs.preview.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'mute', args: [] }), '*'); })" src="https://www.youtube.com/embed/{{ $featuredFilm->preview_video }}?autoplay=1&mute=0&controls=0&disablekb=1&fs=0&modestbranding=1&iv_load_policy=3&playsinline=1&loop=1&playlist={{ $featuredFilm->preview_video }}&enablejsapi=1" title="Preview de {{ $featuredFilm->title }}" class="pointer-events-none absolute inset-0 h-full w-full scale-[1.35]" allow="autoplay; fullscreen"></iframe>
            @endif

            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/75 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#141414] via-transparent to-black/10"></div>

            @if($hasPreview)
                <button type="button" @click="muted = !muted; if ($refs.preview.tagName === 'VIDEO') { $refs.preview.muted = muted; if (!muted) $refs.preview.play(); } else { $refs.preview.contentWindow.postMessage(JSON.stringify({ event: 'command', func: muted ? 'mute' : 'unMute', args: [] }), '*'); }" class="absolute right-7 top-7 z-10 flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-black/40 text-sm text-white backdrop-blur transition hover:bg-white/20" :aria-label="muted ? 'Ativar som' : 'Mutar som'"><span x-text="muted ? '🔇' : '🔊'"></span></button>
            @endif

            <div class="motion-safe:animate-slide-up relative z-10 flex h-full max-w-2xl flex-col justify-end px-5 pb-8 sm:px-10 sm:pb-12 md:px-12 lg:pb-16">
                @if($featuredFilm->logo)
                    <img src="{{ $featuredFilm->logo }}" alt="{{ $featuredFilm->title }}" class="max-h-24 max-w-[75%] object-contain object-left sm:max-h-32 sm:max-w-md">
                @else
                    <h1 class="text-4xl font-black tracking-tight text-white sm:text-5xl md:text-7xl">{{ $featuredFilm->title }}</h1>
                @endif

                @if($featuredFilm->title_language === 'original')
                    <p class="mt-3 text-xs text-zinc-400">Título em português (Brasil): não disponível · título original: {{ $featuredFilm->original_title ?: $featuredFilm->title }}</p>
                @elseif($featuredFilm->title_language === 'pt-BR')
                    <p class="mt-3 text-xs text-zinc-400">Título em português (Brasil): {{ $featuredFilm->title }}</p>
                @endif

                <p class="mt-4 line-clamp-3 max-w-xl text-sm leading-relaxed text-zinc-200 sm:mt-5 sm:text-base md:text-lg">{{ $featuredFilm->summary }}</p>

                <div class="mt-5 flex flex-wrap gap-2 sm:mt-7 sm:gap-3">
                    <a href="{{ route('see', ['film' => $featuredFilm, 'play' => 1]) }}" class="rounded-full bg-white px-7 py-3 font-bold text-black transition duration-300 hover:scale-105 hover:bg-zinc-200">▶ Assistir</a>
                    <button type="button" @click="detailsOpen = true" class="rounded-full bg-zinc-600/80 px-7 py-3 font-bold text-white transition duration-300 hover:scale-105 hover:bg-zinc-500">Mais informações</button>
                    <button type="button" wire:click="toggleFavorite({{ $featuredFilm->id }})" class="rounded-full border border-white/20 bg-black/30 px-5 py-3 font-bold text-white transition hover:bg-white/20">{{ $featuredFilm->is_favorite ? '★ Favoritado' : '☆ Favoritar' }}</button>
                </div>
            </div>
        </section>

        @if($favoriteFilms->isNotEmpty())
            <section class="mt-9">
                <h2 class="mb-3 text-xl font-bold text-white">Minha lista</h2>
                <div class="group/carousel relative">
                    <button type="button" @click="$refs.track.scrollBy({ left: -520, behavior: 'smooth' })" aria-label="Filmes anteriores" class="absolute left-0 top-1/2 z-30 hidden h-16 w-10 -translate-y-1/2 items-center justify-center rounded-r-xl bg-black/75 text-3xl text-white opacity-0 shadow-xl transition duration-300 hover:bg-black/95 group-hover/carousel:flex group-hover/carousel:opacity-100">‹</button>
                    <div x-data="{ dragging: false, moved: false, startX: 0, scrollLeft: 0, beginDrag(event) { if (event.button !== undefined && event.button !== 0) return; this.dragging = true; this.moved = false; this.startX = event.clientX; this.scrollLeft = this.$refs.track.scrollLeft; event.currentTarget.setPointerCapture?.(event.pointerId); }, moveDrag(event) { if (!this.dragging) return; const distance = event.clientX - this.startX; if (Math.abs(distance) > 6) this.moved = true; this.$refs.track.scrollLeft = this.scrollLeft - (distance * 1.25); }, endDrag() { this.dragging = false; }, cancelClick(event) { if (this.moved) { event.preventDefault(); event.stopPropagation(); this.moved = false; } } }" x-ref="track" @pointerdown="beginDrag($event)" @pointermove="moveDrag($event)" @pointerup="endDrag()" @pointercancel="endDrag()" @scroll.debounce.200ms="if ($el.scrollLeft + $el.clientWidth >= $el.scrollWidth - 120) $wire.loadMore()" @click.capture="cancelClick($event)" class="flex cursor-grab select-none gap-3 overflow-x-auto pb-5 active:cursor-grabbing [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" style="touch-action: pan-y">
                    @foreach($favoriteFilms as $film)
                        @php
                            $cover = Str::startsWith($film->cover, ['http://', 'https://']) ? $film->cover : asset('storage/' . $film->cover);
                            $cardBackground = blank($film->backdrop) ? $cover : $film->backdrop;
                        @endphp
                        <div x-data="{ hovering: false, hoverTimer: null, panelStyle: '', positionPanel() { const rect = this.$el.getBoundingClientRect(); const width = 352; const height = 390; const left = Math.min(Math.max(rect.left + (rect.width / 2) - (width / 2), 12), window.innerWidth - width - 12); const top = Math.min(Math.max(rect.top + (rect.height / 2) - (height / 2), 76), window.innerHeight - height - 12); this.panelStyle = `left: ${left}px; top: ${top}px;`; } }" @mouseenter="clearTimeout(hoverTimer); hoverTimer = setTimeout(() => { hovering = true; positionPanel(); }, 1000)" @mouseleave="clearTimeout(hoverTimer); hovering = false" @scroll.window="if (hovering) positionPanel()" class="group relative w-64 shrink-0 overflow-visible rounded-xl bg-zinc-900 transition duration-500 hover:z-50 hover:shadow-2xl hover:shadow-black/70">
                            <div x-data="{ detailsOpen: false }">
                            <a href="{{ route('see', $film) }}" @click.prevent="detailsOpen = true" draggable="false" class="relative block overflow-hidden rounded-xl" :class="hovering ? 'ring-1 ring-white/30' : ''">
                                <img src="{{ $cardBackground }}" alt="{{ $film->title }}" draggable="false" class="aspect-video w-full object-cover transition duration-500 group-hover:scale-110 group-hover:brightness-50">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/80 to-transparent px-4 pb-3 pt-10"><h3 class="truncate font-bold text-white">{{ $film->title }}</h3><p class="mt-1 line-clamp-2 text-xs text-zinc-300">{{ $film->summary }}</p></div>
                            </a>
                            <div x-cloak x-show="hovering" @click="detailsOpen = true" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0" :style="panelStyle" class="fixed z-[100] w-[22rem] overflow-hidden rounded-xl bg-[#181818] shadow-2xl shadow-black/80 ring-1 ring-white/20">
                                <div class="relative aspect-video bg-black">
                                    <img src="{{ $cardBackground }}" alt="{{ $film->title }}" class="absolute inset-0 h-full w-full object-cover">
                                    @if($film->preview_video)
                                        <iframe x-ref="cardPreview" x-init="$watch('hovering', value => { if (value && $el.src === 'about:blank') $el.src = $el.dataset.src; })" src="about:blank" data-src="https://www.youtube.com/embed/{{ $film->preview_video }}?autoplay=1&mute=1&controls=0&disablekb=1&fs=0&modestbranding=1&iv_load_policy=3&playsinline=1&loop=1&playlist={{ $film->preview_video }}&rel=0" title="Preview de {{ $film->title }}" class="pointer-events-none absolute inset-0 h-full w-full scale-[1.25]" allow="autoplay; fullscreen"></iframe>
                                    @endif
                                    <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-12 bg-gradient-to-t from-[#181818] to-transparent"></div>
                                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#181818] via-transparent to-black/10"></div>
                                </div>
                                <div class="space-y-3 p-4">
                                    <div class="flex items-center gap-2">
                                        <a @pointerdown.stop @click.stop href="{{ route('see', ['film' => $film, 'play' => 1]) }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-lg text-black transition hover:scale-110">▶</a>
                                        <button @pointerdown.stop @click.stop type="button" wire:click="toggleFavorite({{ $film->id }})" class="flex h-9 w-9 items-center justify-center rounded-full border border-zinc-400 text-lg text-white transition hover:border-white hover:bg-white/20">{{ $film->is_favorite ? '★' : '+' }}</button>
                                        <button @pointerdown.stop @click.stop type="button" wire:click="deleteFilm({{ $film->id }})" wire:confirm="Tem certeza que deseja apagar este filme?" class="ml-auto rounded-full border border-red-400/60 px-3 py-1 text-xs text-red-300 transition hover:bg-red-600 hover:text-white">Excluir</button>
                                    </div>
                                    <h3 class="truncate text-lg font-bold text-white">{{ $film->title }}</h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-300">
                                        @if($film->release_date)<span>{{ substr($film->release_date, 0, 4) }}</span>@endif
                                        <span class="rounded border border-zinc-500 px-1.5 py-0.5">HD</span>
                                        @if($film->genre)<span>{{ Str::limit($film->genre, 28) }}</span>@endif
                                    </div>
                                    <p class="line-clamp-2 text-sm leading-relaxed text-zinc-300">{{ $film->summary }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="toggleFavorite({{ $film->id }})" class="absolute right-3 top-3 rounded-full bg-black/70 px-3 py-2 text-sm text-white transition hover:bg-red-600">★</button>
                            <button type="button" wire:click="deleteFilm({{ $film->id }})" wire:confirm="Tem certeza que deseja apagar este filme?" class="absolute left-3 top-3 rounded-full bg-black/70 px-3 py-2 text-xs text-red-300 transition hover:bg-red-600 hover:text-white">Excluir</button>
                            <div x-cloak x-show="detailsOpen" x-transition.opacity @keydown.escape.window="detailsOpen = false" @click.self="detailsOpen = false" class="fixed inset-0 z-[110] overflow-y-auto bg-black/80 px-3 py-6 backdrop-blur-sm sm:px-4 sm:py-8">
                                <article class="mx-auto max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-[#181818] shadow-2xl shadow-black/70">
                                    <div class="relative aspect-video bg-black">
                                        @if($film->preview_video)
                                            <iframe x-init="$watch('detailsOpen', value => { if (value && $el.src === 'about:blank') $el.src = $el.dataset.src; })" src="about:blank" data-src="https://www.youtube.com/embed/{{ $film->preview_video }}?autoplay=1&mute=1&controls=1&rel=0" title="Preview de {{ $film->title }}" class="h-full w-full" allow="autoplay; fullscreen" allowfullscreen></iframe>
                                        @else
                                            <img src="{{ $cardBackground }}" alt="{{ $film->title }}" class="h-full w-full object-cover">
                                        @endif
                                        <button type="button" @click="detailsOpen = false" class="absolute right-3 top-3 rounded-full bg-black/70 px-3 py-2 text-white transition hover:bg-white hover:text-black">X</button>
                                    </div>
                                    <div class="p-5 sm:p-7">
                                        @if($film->logo)
                                            <img src="{{ $film->logo }}" alt="{{ $film->title }}" class="max-h-20 max-w-[75%] object-contain object-left">
                                        @else
                                            <h2 class="text-2xl font-black text-white sm:text-3xl">{{ $film->title }}</h2>
                                        @endif
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <a href="{{ route('see', ['film' => $film, 'play' => 1]) }}" class="rounded-full bg-white px-5 py-2 font-bold text-black">Assistir</a>
                                            <button type="button" wire:click="toggleFavorite({{ $film->id }})" class="rounded-full border border-white/20 px-5 py-2 font-semibold text-white">{{ $film->is_favorite ? 'Favoritado' : 'Favoritar' }}</button>
                                            <button type="button" wire:click="deleteFilm({{ $film->id }})" wire:confirm="Tem certeza que deseja apagar este filme?" class="rounded-full border border-red-500/40 px-5 py-2 font-semibold text-red-300 transition hover:bg-red-600 hover:text-white">Excluir filme</button>
                                        </div>
                                        <div class="mt-5 flex flex-wrap gap-2 text-sm text-zinc-400">
                                            @if($film->release_date)<span>{{ substr($film->release_date, 0, 4) }}</span>@endif
                                            <span class="rounded border border-zinc-600 px-2 py-0.5">HD</span>
                                            @if($film->genre)<span>{{ $film->genre }}</span>@endif
                                        </div>
                                        <p class="mt-5 whitespace-pre-line leading-relaxed text-zinc-300">{{ $film->summary }}</p>
                                    </div>
                                </article>
                            </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-9">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">{{ $selectedGenre ?: 'Filmes do catálogo' }}</h2>
                <a href="{{ route('create') }}" class="rounded-full px-3 py-1 text-sm font-semibold text-zinc-400 transition hover:bg-white/10 hover:text-white">+ Adicionar</a>
            </div>

            <div class="group/carousel relative">
                <button type="button" @click="$refs.track.scrollBy({ left: -520, behavior: 'smooth' })" aria-label="Filmes anteriores" class="absolute left-0 top-1/2 z-30 hidden h-16 w-10 -translate-y-1/2 items-center justify-center rounded-r-xl bg-black/75 text-3xl text-white opacity-0 shadow-xl transition duration-300 hover:bg-black/95 group-hover/carousel:flex group-hover/carousel:opacity-100">‹</button>
                <div x-data="{ dragging: false, moved: false, startX: 0, scrollLeft: 0, beginDrag(event) { if (event.button !== undefined && event.button !== 0) return; this.dragging = true; this.moved = false; this.startX = event.clientX; this.scrollLeft = this.$refs.track.scrollLeft; event.currentTarget.setPointerCapture?.(event.pointerId); }, moveDrag(event) { if (!this.dragging) return; const distance = event.clientX - this.startX; if (Math.abs(distance) > 6) this.moved = true; this.$refs.track.scrollLeft = this.scrollLeft - (distance * 1.25); }, endDrag() { this.dragging = false; }, cancelClick(event) { if (this.moved) { event.preventDefault(); event.stopPropagation(); this.moved = false; } } }" x-ref="track" @pointerdown="beginDrag($event)" @pointermove="moveDrag($event)" @pointerup="endDrag()" @pointercancel="endDrag()" @scroll.debounce.200ms="if ($el.scrollLeft + $el.clientWidth >= $el.scrollWidth - 120) $wire.loadMore()" @click.capture="cancelClick($event)" class="flex cursor-grab select-none gap-3 overflow-x-auto pb-5 active:cursor-grabbing [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" style="touch-action: pan-y">
                @foreach($films as $film)
                    @php
                        $cover = Str::startsWith($film->cover, ['http://', 'https://']) ? $film->cover : asset('storage/' . $film->cover);
                        $cardBackground = blank($film->backdrop) ? $cover : $film->backdrop;
                    @endphp
                    <div x-data="{ hovering: false, hoverTimer: null, panelStyle: '', positionPanel() { const rect = this.$el.getBoundingClientRect(); const width = 352; const height = 390; const left = Math.min(Math.max(rect.left + (rect.width / 2) - (width / 2), 12), window.innerWidth - width - 12); const top = Math.min(Math.max(rect.top + (rect.height / 2) - (height / 2), 76), window.innerHeight - height - 12); this.panelStyle = `left: ${left}px; top: ${top}px;`; } }" @mouseenter="clearTimeout(hoverTimer); hoverTimer = setTimeout(() => { hovering = true; positionPanel(); }, 1000)" @mouseleave="clearTimeout(hoverTimer); hovering = false" @scroll.window="if (hovering) positionPanel()" class="group relative w-64 shrink-0 overflow-visible rounded-xl bg-zinc-900 transition duration-500 hover:z-50 hover:shadow-2xl hover:shadow-black/70">
                        <div x-data="{ detailsOpen: false }">
                        <a href="{{ route('see', $film) }}" @click.prevent="detailsOpen = true" draggable="false" class="relative block overflow-hidden rounded-xl" :class="hovering ? 'ring-1 ring-white/30' : ''">
                            <img src="{{ $cardBackground }}" alt="{{ $film->title }}" draggable="false" class="aspect-video w-full object-cover transition duration-500 group-hover:scale-110 group-hover:brightness-50">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/80 to-transparent px-4 pb-3 pt-10"><h3 class="truncate font-bold text-white">{{ $film->title }}</h3><p class="mt-1 line-clamp-2 text-xs text-zinc-300">{{ $film->summary }}</p></div>
                        </a>
                        <div x-cloak x-show="hovering" @click="detailsOpen = true" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0" :style="panelStyle" class="fixed z-[100] w-[22rem] overflow-hidden rounded-xl bg-[#181818] shadow-2xl shadow-black/80 ring-1 ring-white/20">
                            <div class="relative aspect-video bg-black">
                                <img src="{{ $cardBackground }}" alt="{{ $film->title }}" class="absolute inset-0 h-full w-full object-cover">
                                @if($film->preview_video)
                                    <iframe x-ref="cardPreview" x-init="$watch('hovering', value => { if (value && $el.src === 'about:blank') $el.src = $el.dataset.src; })" src="about:blank" data-src="https://www.youtube.com/embed/{{ $film->preview_video }}?autoplay=1&mute=1&controls=0&disablekb=1&fs=0&modestbranding=1&iv_load_policy=3&playsinline=1&loop=1&playlist={{ $film->preview_video }}&rel=0" title="Preview de {{ $film->title }}" class="pointer-events-none absolute inset-0 h-full w-full scale-[1.25]" allow="autoplay; fullscreen"></iframe>
                                @endif
                                <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-12 bg-gradient-to-t from-[#181818] to-transparent"></div>
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#181818] via-transparent to-black/10"></div>
                            </div>
                            <div class="space-y-3 p-4">
                                <div class="flex items-center gap-2">
                                    <a @pointerdown.stop @click.stop href="{{ route('see', ['film' => $film, 'play' => 1]) }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-lg text-black transition hover:scale-110">▶</a>
                                    <button @pointerdown.stop @click.stop type="button" wire:click="toggleFavorite({{ $film->id }})" class="flex h-9 w-9 items-center justify-center rounded-full border border-zinc-400 text-lg text-white transition hover:border-white hover:bg-white/20">{{ $film->is_favorite ? '★' : '+' }}</button>
                                    <button @pointerdown.stop @click.stop type="button" wire:click="deleteFilm({{ $film->id }})" wire:confirm="Tem certeza que deseja apagar este filme?" class="ml-auto rounded-full border border-red-400/60 px-3 py-1 text-xs text-red-300 transition hover:bg-red-600 hover:text-white">Excluir</button>
                                </div>
                                <h3 class="truncate text-lg font-bold text-white">{{ $film->title }}</h3>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-300">
                                    @if($film->release_date)<span>{{ substr($film->release_date, 0, 4) }}</span>@endif
                                    <span class="rounded border border-zinc-500 px-1.5 py-0.5">HD</span>
                                    @if($film->genre)<span>{{ Str::limit($film->genre, 28) }}</span>@endif
                                </div>
                                <p class="line-clamp-2 text-sm leading-relaxed text-zinc-300">{{ $film->summary }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="toggleFavorite({{ $film->id }})" class="absolute right-3 top-3 rounded-full bg-black/70 px-3 py-2 text-sm text-white transition hover:bg-red-600">{{ $film->is_favorite ? '★' : '☆' }}</button>
                        <button type="button" wire:click="deleteFilm({{ $film->id }})" wire:confirm="Tem certeza que deseja apagar este filme?" class="absolute left-3 top-3 rounded-full bg-black/70 px-3 py-2 text-xs text-red-300 transition hover:bg-red-600 hover:text-white">Excluir</button>
                        <div x-cloak x-show="detailsOpen" x-transition.opacity @keydown.escape.window="detailsOpen = false" @click.self="detailsOpen = false" class="fixed inset-0 z-[110] overflow-y-auto bg-black/80 px-3 py-6 backdrop-blur-sm sm:px-4 sm:py-8">
                            <article class="mx-auto max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-[#181818] shadow-2xl shadow-black/70">
                                <div class="relative aspect-video bg-black">
                                    @if($film->preview_video)
                                        <iframe x-init="$watch('detailsOpen', value => { if (value && $el.src === 'about:blank') $el.src = $el.dataset.src; })" src="about:blank" data-src="https://www.youtube.com/embed/{{ $film->preview_video }}?autoplay=1&mute=1&controls=1&rel=0" title="Preview de {{ $film->title }}" class="h-full w-full" allow="autoplay; fullscreen" allowfullscreen></iframe>
                                    @else
                                        <img src="{{ $cardBackground }}" alt="{{ $film->title }}" class="h-full w-full object-cover">
                                    @endif
                                    <button type="button" @click="detailsOpen = false" class="absolute right-3 top-3 rounded-full bg-black/70 px-3 py-2 text-white transition hover:bg-white hover:text-black">X</button>
                                </div>
                                <div class="p-5 sm:p-7">
                                    @if($film->logo)
                                        <img src="{{ $film->logo }}" alt="{{ $film->title }}" class="max-h-20 max-w-[75%] object-contain object-left">
                                    @else
                                        <h2 class="text-2xl font-black text-white sm:text-3xl">{{ $film->title }}</h2>
                                    @endif
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <a href="{{ route('see', ['film' => $film, 'play' => 1]) }}" class="rounded-full bg-white px-5 py-2 font-bold text-black">Assistir</a>
                                        <button type="button" wire:click="toggleFavorite({{ $film->id }})" class="rounded-full border border-white/20 px-5 py-2 font-semibold text-white">{{ $film->is_favorite ? 'Favoritado' : 'Favoritar' }}</button>
                                        <button type="button" wire:click="deleteFilm({{ $film->id }})" wire:confirm="Tem certeza que deseja apagar este filme?" class="rounded-full border border-red-500/40 px-5 py-2 font-semibold text-red-300 transition hover:bg-red-600 hover:text-white">Excluir filme</button>
                                    </div>
                                    <div class="mt-5 flex flex-wrap gap-2 text-sm text-zinc-400">
                                        @if($film->release_date)<span>{{ substr($film->release_date, 0, 4) }}</span>@endif
                                        <span class="rounded border border-zinc-600 px-2 py-0.5">HD</span>
                                        @if($film->genre)<span>{{ $film->genre }}</span>@endif
                                    </div>
                                    <p class="mt-5 whitespace-pre-line leading-relaxed text-zinc-300">{{ $film->summary }}</p>
                                </div>
                            </article>
                        </div>
                        </div>
                    </div>
                @endforeach
                </div>
                <button type="button" @click="$refs.track.scrollBy({ left: 520, behavior: 'smooth' })" aria-label="Próximos filmes" class="absolute right-0 top-1/2 z-30 hidden h-16 w-10 -translate-y-1/2 items-center justify-center rounded-l-xl bg-black/75 text-3xl text-white opacity-0 shadow-xl transition duration-300 hover:bg-black/95 group-hover/carousel:flex group-hover/carousel:opacity-100">›</button>
            </div>
        </section>

        <div x-cloak x-show="detailsOpen" x-transition.opacity @keydown.escape.window="detailsOpen = false" class="fixed inset-0 z-[80] overflow-y-auto bg-black/80 px-4 py-8 backdrop-blur-sm">
            <div @click.self="detailsOpen = false" class="mx-auto min-h-full max-w-3xl">
                <article class="overflow-hidden rounded-2xl border border-white/10 bg-[#181818] shadow-2xl shadow-black/60">
                    <div class="relative aspect-video bg-black">
                        @if($hasLocalPreview)
                            <video src="{{ asset('videos/featured-preview.mp4') }}" autoplay muted loop playsinline controls class="h-full w-full object-cover"></video>
                        @elseif($featuredFilm->preview_video)
                            <iframe src="https://www.youtube.com/embed/{{ $featuredFilm->preview_video }}?autoplay=1&mute=1&controls=1&rel=0" title="Preview de {{ $featuredFilm->title }}" class="h-full w-full" allow="autoplay; fullscreen" allowfullscreen></iframe>
                        @else
                            <img src="{{ $featuredBackdrop }}" alt="{{ $featuredFilm->title }}" class="h-full w-full object-cover">
                        @endif
                        <button type="button" @click="detailsOpen = false" class="absolute right-4 top-4 rounded-full bg-black/70 px-3 py-2 text-white transition hover:bg-white hover:text-black">✕</button>
                    </div>

                    <div class="p-7">
                        @if($featuredFilm->logo)
                            <img src="{{ $featuredFilm->logo }}" alt="{{ $featuredFilm->title }}" class="max-h-24 max-w-sm object-contain object-left">
                        @else
                            <h2 class="text-3xl font-black text-white">{{ $featuredFilm->title }}</h2>
                        @endif
                        @if($featuredFilm->title_language === 'original')
                            <p class="mt-2 text-xs text-zinc-500">Título em português (Brasil): não disponível · título original: {{ $featuredFilm->original_title ?: $featuredFilm->title }}</p>
                        @elseif($featuredFilm->title_language === 'pt-BR')
                            <p class="mt-2 text-xs text-zinc-500">Título em português (Brasil): {{ $featuredFilm->title }}</p>
                        @endif

                        <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-zinc-300">
                            @if($featuredFilm->release_date)<span>{{ substr($featuredFilm->release_date, 0, 4) }}</span>@endif
                            <span class="rounded border border-zinc-600 px-2 py-0.5">HD</span>
                            <a href="{{ route('see', ['film' => $featuredFilm, 'play' => 1]) }}" class="rounded-full bg-white px-5 py-2 font-bold text-black transition hover:bg-zinc-200">▶ Assistir</a>
                            <button type="button" wire:click="toggleFavorite({{ $featuredFilm->id }})" class="rounded-full border border-white/20 px-5 py-2 font-semibold text-white transition hover:bg-white/10">{{ $featuredFilm->is_favorite ? '★ Favoritado' : '☆ Favoritar' }}</button>
                        </div>

                        <p class="mt-6 leading-relaxed text-zinc-300">{{ $featuredFilm->summary }}</p>

                        <h3 class="mt-8 text-xl font-bold text-white">Filmes relacionados</h3>
                        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                            @foreach($relatedFilms as $relatedFilm)
                                @php
                                    $relatedCover = Str::startsWith($relatedFilm->cover, ['http://', 'https://']) ? $relatedFilm->cover : asset('storage/' . $relatedFilm->cover);
                                    $relatedBackground = blank($relatedFilm->backdrop) ? $relatedCover : $relatedFilm->backdrop;
                                @endphp
                                <div class="relative overflow-hidden rounded-lg bg-zinc-800 transition hover:scale-105">
                                    <a href="{{ route('see', $relatedFilm) }}" class="block"><img src="{{ $relatedBackground }}" alt="{{ $relatedFilm->title }}" class="aspect-video w-full object-cover"><p class="truncate px-3 py-2 text-sm font-semibold text-white">{{ $relatedFilm->title }}</p></a>
                                    <button type="button" wire:click="toggleFavorite({{ $relatedFilm->id }})" class="absolute right-2 top-2 rounded-full bg-black/70 px-2 py-1 text-xs text-white">{{ $relatedFilm->is_favorite ? '★' : '☆' }}</button>
                                    <button type="button" wire:click="deleteFilm({{ $relatedFilm->id }})" wire:confirm="Tem certeza que deseja apagar este filme?" class="absolute left-2 top-2 rounded-full bg-black/70 px-2 py-1 text-[10px] text-red-300">Excluir</button>
                                </div>
                            @endforeach
                    </div>
                    <button type="button" @click="$refs.track.scrollBy({ left: 520, behavior: 'smooth' })" aria-label="Próximos filmes" class="absolute right-0 top-1/2 z-30 hidden h-16 w-10 -translate-y-1/2 items-center justify-center rounded-l-xl bg-black/75 text-3xl text-white opacity-0 shadow-xl transition duration-300 hover:bg-black/95 group-hover/carousel:flex group-hover/carousel:opacity-100">›</button>
                    </div>
                </article>
            </div>
        </div>
    </main>
@endif
