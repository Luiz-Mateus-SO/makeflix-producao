<div>
    <div class="px-4 py-8 mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-8">
        @for ($i = 0; $i < 1; $i++)
            <a href="{{ route('see') }}" class="relative w-full aspect-video bg-cover bg-center bg-no-repeat"
                style="background-image: url('https://picsum.photos/640/480')">
                <div
                    class="absolute flex items-end p-4 top-0 left-0 w-full h-full bg-gradient-to-t from-black to-black/20">
                    <div>
                        <h1 class="text-2xl font-bold">
                            Título
                        </h1>
                        <h2 class="font-medium">
                            Diretor
                        </h2>
                    </div>
                </div>
            </a>
        @endfor
    </div>
</div>
