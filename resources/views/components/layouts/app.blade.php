<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                        fontFamily: {
                            sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                        keyframes: {
                            'fade-in': {
                                '0%': { opacity: '0' },
                                '100%': { opacity: '1' },
                            },
                            'slide-up': {
                                '0%': { opacity: '0', transform: 'translateY(18px)' },
                                '100%': { opacity: '1', transform: 'translateY(0)' },
                            },
                        },
                        animation: {
                            'fade-in': 'fade-in 700ms ease-out both',
                            'slide-up': 'slide-up 700ms ease-out 120ms both',
                        },
                },
            },
        };
    </script>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;;
        }

        ::-webkit-scrollbar-track {
            background: #18181b
        }

        ::-webkit-scrollbar-thumb {
            background: #dc2626
        }
    </style>
</head>

<body class="min-h-screen bg-[radial-gradient(circle_at_50%_-20%,_#292929_0%,_#141414_42%,_#0f0f0f_100%)] font-sans text-zinc-50 antialiased">
    <livewire:web.components.navbar />
    {{ $slot }}
</body>

</html>
