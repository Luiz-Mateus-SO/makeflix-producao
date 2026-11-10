<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-12">
    <div class="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.25em] text-red-500">Meu catálogo</p>
            <h1 class="text-3xl font-black tracking-tight text-white md:text-4xl">Cadastrar filme</h1>
            <p class="mt-2 text-sm text-zinc-400">Adicione um filme manualmente ou informe apenas o título para buscar no TMDB.</p>
        </div>

        <a href="{{ route('home') }}" class="shrink-0 text-sm font-semibold text-zinc-400 transition hover:text-white">
            ← Voltar aos filmes
        </a>
    </div>

    <form wire:submit="save" class="mx-auto max-w-3xl space-y-6 rounded-2xl border border-zinc-800 bg-zinc-900/70 p-5 shadow-2xl shadow-black/20 sm:p-6 md:p-8" enctype="multipart/form-data">
        <div>
            <label for="title" class="mb-2 block text-sm font-semibold text-zinc-200">Título</label>
            <input id="title" type="text" wire:model="title" class="w-full rounded border border-zinc-700 bg-zinc-900 p-3 text-white placeholder-zinc-500 outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            @error('title') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="summary" class="mb-2 block text-sm font-semibold text-zinc-200">Sinopse <span class="font-normal text-zinc-500">(opcional com TMDB)</span></label>
            <textarea id="summary" wire:model="summary" rows="6" class="w-full rounded border border-zinc-700 bg-zinc-900 p-3 text-white placeholder-zinc-500 outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
            @error('summary') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="cover" class="mb-2 block text-sm font-semibold text-zinc-200">Capa <span class="font-normal text-zinc-500">(opcional com TMDB)</span></label>
            <input id="cover" type="file" wire:model="cover" accept="image/*" class="w-full rounded border border-zinc-700 bg-zinc-900 p-3 text-white placeholder-zinc-500 outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            <p class="mt-2 text-xs text-zinc-500">Escolha uma imagem para representar o filme.</p>
            <p wire:loading wire:target="cover" class="mt-2 text-sm text-zinc-300">Enviando imagem...</p>
            @error('cover') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end border-t border-zinc-800 pt-6">
            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="rounded-full bg-red-600 px-7 py-3 font-semibold text-white transition duration-300 hover:scale-105 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Salvar filme</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </button>
        </div>
    </form>
    <section class="mx-auto mt-10 max-w-3xl">
        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">Gerenciamento</p>
                <h2 class="mt-1 text-2xl font-black text-white">Filmes cadastrados</h2>
            </div>
            <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs font-semibold text-zinc-300">{{ $films->count() }} {{ $films->count() === 1 ? 'filme' : 'filmes' }}</span>
        </div>

        @if($films->isEmpty())
            <div class="rounded-2xl border border-dashed border-zinc-800 bg-zinc-900/40 p-8 text-center text-sm text-zinc-500">
                Nenhum filme foi cadastrado ainda.
            </div>
        @else
            <div class="space-y-3">
                @foreach($films as $film)
                    @php
                        $filmCover = Str::startsWith($film->cover, ['http://', 'https://'])
                            ? $film->cover
                            : asset('storage/' . $film->cover);
                    @endphp
                    <article wire:key="create-film-{{ $film->id }}" class="flex flex-col gap-4 rounded-2xl border border-zinc-800 bg-zinc-900/70 p-3 transition hover:border-zinc-700 sm:flex-row sm:items-center">
                        <img src="{{ $filmCover }}" alt="{{ $film->title }}" class="h-28 w-full rounded-xl object-cover sm:h-20 sm:w-32">
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-bold text-white">{{ $film->title }}</h3>
                            <p class="mt-1 line-clamp-2 text-sm text-zinc-400">{{ $film->summary }}</p>
                        </div>
                        <div class="flex shrink-0 gap-2 sm:flex-col">
                            <a href="{{ route('see', $film) }}" class="flex-1 rounded-full border border-zinc-700 px-4 py-2 text-center text-sm font-semibold text-zinc-200 transition hover:bg-white/10 sm:flex-none">Ver</a>
                            <button type="button" wire:click="deleteFilm({{ $film->id }})" wire:confirm="Tem certeza que deseja apagar este filme? Essa ação não poderá ser desfeita." class="flex-1 rounded-full border border-red-500/40 px-4 py-2 text-sm font-semibold text-red-300 transition hover:bg-red-600 hover:text-white sm:flex-none">Excluir</button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
