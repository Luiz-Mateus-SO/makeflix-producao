@php
    $cover = Str::startsWith($film->cover, ['http://', 'https://'])
        ? $film->cover
        : asset('storage/' . $film->cover);
    $backdrop = blank($film->backdrop) ? $cover : $film->backdrop;
    $hasLocalPreview = file_exists(public_path('videos/featured-preview.mp4'));
@endphp

@if($playing)
    <div class="fixed inset-0 z-[100] flex min-h-[100dvh] items-center justify-center bg-black p-2 sm:p-4">
        <div class="relative flex h-full max-h-[100dvh] w-full max-w-7xl items-center justify-center overflow-hidden rounded-none bg-black sm:max-h-[calc(100dvh-2rem)] sm:rounded-2xl sm:ring-1 sm:ring-white/20">
            @if($hasLocalPreview)
                <video src="{{ asset('videos/featured-preview.mp4') }}" autoplay controls playsinline class="max-h-full max-w-full object-contain"></video>
            @elseif($film->preview_video)
                <iframe src="https://www.youtube.com/embed/{{ $film->preview_video }}?autoplay=1&controls=1&rel=0&modestbranding=1" title="Assistir {{ $film->title }}" class="aspect-video h-auto max-h-full w-full max-w-7xl" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
            @else
                <img src="{{ $backdrop }}" alt="{{ $film->title }}" class="max-h-full max-w-full object-contain opacity-50">
                <p class="absolute max-w-[calc(100%-2rem)] rounded-full bg-black/70 px-5 py-3 text-center text-sm text-white sm:px-6 sm:text-base">Este filme ainda nao possui um video disponivel.</p>
            @endif

            <a href="{{ route('see', $film) }}" class="absolute right-3 top-3 rounded-full bg-black/70 px-4 py-2 text-sm font-bold text-white backdrop-blur transition hover:bg-white hover:text-black sm:right-5 sm:top-5">Fechar</a>
        </div>
    </div>
@else
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-10">
        <a href="{{ route('home') }}" class="inline-flex rounded-full px-4 py-2 text-sm font-semibold text-zinc-300 transition hover:bg-white/10 hover:text-white">Voltar para catalogo</a>

        <article class="mt-6 overflow-hidden rounded-[1.5rem] border border-white/10 bg-zinc-900 shadow-2xl shadow-black/30 sm:rounded-[2rem]">
            <div class="relative h-[22rem] overflow-hidden sm:h-[28rem]">
                <img src="{{ $backdrop }}" alt="{{ $film->title }}" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-black/20 to-transparent"></div>
                <div class="absolute bottom-6 left-5 right-5 sm:bottom-8 sm:left-8 sm:right-8">
                    @if($film->logo)
                        <img src="{{ $film->logo }}" alt="{{ $film->title }}" class="max-h-24 max-w-[75%] object-contain object-left sm:max-h-28 sm:max-w-sm">
                    @else
                        <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">{{ $film->title }}</h1>
                    @endif
                    @if($film->title_language === 'original')
                        <p class="mt-2 text-xs text-zinc-400">Titulo em portugues (Brasil): nao disponivel - titulo original: {{ $film->original_title ?: $film->title }}</p>
                    @elseif($film->title_language === 'pt-BR')
                        <p class="mt-2 text-xs text-zinc-400">Titulo em portugues (Brasil): {{ $film->title }}</p>
                    @endif
                </div>
            </div>

            <div class="p-5 sm:p-8">
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <a href="{{ route('see', ['film' => $film, 'play' => 1]) }}" class="rounded-full bg-white px-5 py-3 font-bold text-black transition hover:scale-105 hover:bg-zinc-200 sm:px-6">Assistir</a>
                    <button type="button" wire:click="toggleFavorite" class="rounded-full border border-white/20 px-5 py-3 font-bold text-white transition hover:bg-white/10 sm:px-6">{{ $film->is_favorite ? 'Favoritado' : 'Favoritar' }}</button>
                    <button type="button" wire:click="delete" wire:confirm="Tem certeza que deseja apagar este filme?" class="rounded-full border border-red-500/40 px-5 py-3 font-bold text-red-300 transition hover:bg-red-600 hover:text-white sm:px-6">Apagar filme</button>
                </div>
                <p class="mt-6 whitespace-pre-line leading-relaxed text-zinc-300">{{ $film->summary }}</p>
            </div>
        </article>
    </div>
@endif
