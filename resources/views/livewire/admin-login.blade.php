<div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-md items-center px-4 py-10">
    <form wire:submit="authenticate" class="w-full rounded-2xl border border-zinc-800 bg-zinc-900/80 p-6 shadow-2xl shadow-black/30 sm:p-8">
        <p class="text-xs font-bold uppercase tracking-[0.25em] text-red-500">Área restrita</p>
        <h1 class="mt-2 text-3xl font-black text-white">Acesso administrativo</h1>
        <p class="mt-3 text-sm leading-relaxed text-zinc-400">Digite a senha definida no arquivo <code class="text-zinc-200">.env</code> para cadastrar e gerenciar filmes.</p>

        <div class="mt-6">
            <label for="admin-password" class="mb-2 block text-sm font-semibold text-zinc-200">Senha</label>
            <input id="admin-password" type="password" wire:model="password" autocomplete="current-password" class="w-full rounded-xl border border-zinc-700 bg-zinc-950 p-3 text-white outline-none transition focus:border-red-500 focus:ring-1 focus:ring-red-500">
            @error('password') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" class="mt-6 w-full rounded-full bg-red-600 px-5 py-3 font-bold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
            <span wire:loading.remove>Entrar</span>
            <span wire:loading>Verificando...</span>
        </button>

        <a href="{{ route('home') }}" class="mt-4 block text-center text-sm font-semibold text-zinc-400 transition hover:text-white">Voltar para a home</a>
    </form>
</div>
