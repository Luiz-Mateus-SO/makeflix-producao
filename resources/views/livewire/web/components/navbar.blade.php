<nav class="sticky top-0 z-50 border-b border-white/5 bg-[#141414]/95 backdrop-blur">
    <div class="mx-auto flex h-[72px] max-w-[1400px] items-center px-7">
        <a href="{{ route('home') }}" class="mr-10 text-2xl font-black tracking-tighter text-red-600">
            MADEFLIX
        </a>

        <div class="flex items-center gap-7 text-sm font-semibold text-zinc-300">
            <a
                href="{{ route('home') }}"
            class="rounded-full bg-zinc-700 px-5 py-2 text-white transition duration-300 hover:bg-zinc-600 hover:shadow-lg hover:shadow-black/20"
            >
                Início
            </a>

            <a
                href="{{ route('create') }}"
                class="rounded-full px-2 py-2 transition duration-300 hover:bg-white/10 hover:text-white"
            >
                Cadastrar
            </a>
        </div>

        <div class="ml-auto flex items-center gap-5 text-zinc-300">
            <form action="{{ route('home') }}" method="GET" class="hidden items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 sm:flex">
                <span class="text-lg leading-none">⌕</span>
                <input name="search" value="{{ request('search') }}" type="search" placeholder="Buscar filmes" class="w-32 bg-transparent text-sm text-white outline-none placeholder:text-zinc-500 focus:w-48">
            </form>
            <a href="{{ route('create') }}" aria-label="Cadastrar filme" class="rounded-full px-2 text-xl transition duration-300 hover:bg-white/10 hover:text-white">＋</a>
        </div>
    </div>
</nav>
