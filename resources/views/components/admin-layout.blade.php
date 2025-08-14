<!DOCTYPE html>
<html lang="en" x-data="mainApp()" :class="{ 'dark': $store.theme.current === 'dark' }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.description', 'Dasbor Admin Desa Orakeri') }}">
    <script>
        // Check local storage for theme preference immediately
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <meta name="description" content="{{ $villageName->content ?? 'Website resmi Desa Orakeri.' }}">
    <title>{{ $villageName->content ?? config('app.name', 'Satu Desa') }}</title>
    @php
        $logoContent = $siteLogo->content ?? 'images/logo.jpg';
    @endphp
    @if (Str::contains($logoContent, 'images'))
        <link rel="shortcut icon" href="{{ asset($logoContent) }}" type="image/jpg/x-icon/png">
    @else
        <link rel="shortcut icon" href="{{ Storage::url($logoContent) }}" type="image/jpg/x-icon/png">
    @endif
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tiny.cloud/1/algt269vr4aq8vf2pokvkxyplcwaofury8xlyeekzrg85v42/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        function initializeTinyMCE(selector) {
            tinymce.init({
                selector: selector,
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount nonbreaking hr pagebreak emoticons template codesample directionality print autoresize',
                toolbar: 'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | hr removeformat | preview fullscreen code | help',
                height: 500,
                menubar: 'file edit view insert format tools table help',
                statusbar: true,
                relative_urls: false,
                remove_script_host: false,
                convert_urls: false,
                images_upload_url: '/admin/upload-editor-image', // Contoh endpoint upload Anda di Laravel
                automatic_uploads: true,
                file_picker_types: 'image',
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function() {
                        var file = this.files[0];
                        var reader = new FileReader();
                        reader.onload = function() {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                },
                content_style: 'body { font-family: Poppins, sans-serif; font-size:16px }'
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            initializeTinyMCE('textarea#content');
            initializeTinyMCE('textarea#steps_requirements');
            initializeTinyMCE('textarea#description_editor');
            initializeTinyMCE('textarea#description');
        });
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <style>
        :root {
            /* Warna Dasar dari DB (HEX, dihitung di sini ke HSL) */
            @php // Helper function untuk konversi HEX ke HSL di Blade

            $hexToHsl =function($hex) {
                if ( !$hex || !preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $hex)) {
                    return [0,
                    '0%',
                    '0%'];
                }

                $r =hexdec(substr($hex, 1, 2));
                $g =hexdec(substr($hex, 3, 2));
                $b =hexdec(substr($hex, 5, 2));

                $r /=255;
                $g /=255;
                $b /=255;
                $max =max($r, $g, $b);
                $min =min($r, $g, $b);
                $h =$s =$l =($max + $min) / 2;

                if ($max ===$min) {
                    $h =$s =0;
                }

                else {
                    $d =$max - $min;
                    $s =$l >0.5 ? $d / (2 - $max - $min): $d / ($max + $min);

                    switch ($max) {
                        case $r: $h =($g - $b) / $d + ($g < $b ? 6 : 0);
                        break;
                        case $g: $h =($b - $r) / $d + 2;
                        break;
                        case $b: $h =($r - $g) / $d + 4;
                        break;
                    }

                    $h /=6;
                }

                return [round($h * 360),
                round($s * 100) . '%',
                round($l * 100) . '%'];
            }

            ;

            // Dapatkan nilai HEX dari ProfileContent
            $primaryHex =$brandPrimaryColor->content ?? '#4CAF50';
            $secondaryHex =$brandSecondaryColor->content ?? '#2196F3';
            $accentHex =$brandAccentColor->content ?? '#795548';
            $softGrayHex = '#F8F8F8';
            $darkTextHex = '#333333';
            // Konversi ke HSL
            list($h1, $s1, $l1)=$hexToHsl($primaryHex);
            list($h2, $s2, $l2)=$hexToHsl($secondaryHex);
            list($h3, $s3, $l3)=$hexToHsl($accentHex);
            list($h_sg, $s_sg, $l_sg)=$hexToHsl($softGrayHex);
            list($h_dt, $s_dt, $l_dt)=$hexToHsl($darkTextHex);
        @endphp

        --primary-h: {{ $h1 }};
        --primary-s: {{ $s1 }};
        --primary-l: {{ $l1 }};
        --secondary-h: {{ $h2 }};
        --secondary-s: {{ $s2 }};
        --secondary-l: {{ $l2 }};
        --accent-h: {{ $h3 }};
        --accent-s: {{ $s3 }};
        --accent-l: {{ $l3 }};

        /* Varian Warna (Untuk Hover, Background, dll.) */
        --color-primary: hsl(var(--primary-h), var(--primary-s), var(--primary-l));
        --color-primary-dark: hsl(var(--primary-h), var(--primary-s), clamp(0%, calc(var(--primary-l) - 10%), 100%));
        /* Untuk hover/aktif */
        --color-primary-darker: hsl(var(--primary-h), var(--primary-s), calc(var(--primary-l) - 20%));
        /* Untuk bg mobile nav */
        --color-primary-light: hsl(var(--primary-h), var(--primary-s), calc(var(--primary-l) + 10%));
        /* Untuk bg-xx-100 */

        --color-secondary: hsl(var(--secondary-h), var(--secondary-s), var(--secondary-l));
        --color-secondary-dark: hsl(var(--secondary-h), var(--secondary-s), calc(var(--secondary-l) - 10%));
        --color-secondary-light: hsl(var(--secondary-h), var(--secondary-s), calc(var(--secondary-l) + 10%));

        --color-accent: hsl(var(--accent-h), var(--accent-s), var(--accent-l));
        --color-accent-dark: hsl(var(--accent-h), var(--accent-s), calc(var(--accent-l) - 10%));
        /* Warna Netral Dinamis dari HSL Konversi */
        --color-soft-gray: hsl(var(--primary-h), 10%, 95%);
        /* Bisa terkait primary atau statis */
        --color-dark-text: hsl(0, 0%, 20%);
        /* Tetap relatif gelap */
        }
    </style>
</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors">
    <heade
        class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 h-16 w-full flex items-center px-4 sm:px-6 lg:px-8 fixed top-0 inset-x-0 z-40">
        <div class="flex justify-between w-full">
            <div class="flex items-center space-x-2">
                <button @click="$store.ui.toggleSidebar()" class="md:hidden focus:outline-none">
                    <svg class="w-6 h-6 text-gray-700 dark:text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span
                    class="transition-colors text-lg font-bold text-gray-900 dark:text-white">{{ $villageName->content ?? config('app.name', 'Satu Desa') }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative mr-4">
                    <a href="#"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none focus:text-gray-700 dark:focus:text-gray-200 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <span id="unread-notifications-count"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2 hidden">
                        </span>
                    </a>
                </div>
                <button @click="$store.theme.toggle()"
                    class="h-12 w-12 rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="fill-violet-700 block dark:hidden" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg class="fill-yellow-500 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 transition-colors">
                            @if (Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}"
                                    class="h-8 w-8 rounded-full object-cover mr-2">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF"
                                    alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover mr-2">
                            @endif
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('home')">
                            {{ __('Beranda') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </heade>
    <div class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" x-show="$store.ui.sidebarOpen"
        @click="$store.ui.closeSidebar()" x-transition.opacity></div>
    <aside :class="{ '-translate-x-full': !$store.ui.sidebarOpen }"
        x-show="$store.ui.sidebarOpen || window.innerWidth >= 1024"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
        @click.outside="$store.ui.closeSidebar()"
        class="fixed top-0 left-0 z-40 w-64 h-screen overflow-y-auto bg-white dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 h-16 flex items-center transition-colors">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $villageName->content }}</h2>
            <button @click="$store.ui.toggleSidebar()"
                class="lg:hidden ml-auto text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none transition-colors">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <nav class="mt-2 space-y-2 px-4">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center p-2 rounded-lg
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700
                               group transition-colors duration-200 ease-in-out
                               {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' }}">
                        <span class="ms-3 font-medium">Dashboard</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.hero-sliders.*', 'admin.news.*', 'admin.galleries.*', 'admin.potentials.*', 'admin.products.*', 'admin.documents.*', 'admin.comments.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full p-2 rounded-lg
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                               transition-colors duration-200 ease-in-out">
                        <span class="ms-3 font-medium">Manajemen Konten</span>
                        <svg :class="{ 'rotate-90': open }"
                            class="w-4 h-4 transform transition-transform duration-200 text-gray-500 dark:text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>

                    </button>
                    <ul x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="mt-1 space-y-1 rounded-lg py-1 px-3
                               bg-gray-50 dark:bg-gray-700">

                        <li>
                            <a href="{{ route('admin.hero-sliders.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.hero-sliders.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Hero Slider
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.news.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.news.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Berita
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.galleries.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.galleries.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Galeri
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.potentials.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.potentials.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Potensi Sekolah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.products.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Produk Sekolah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.documents.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.documents.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Dokumen Publik
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.comments.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.comments.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Moderasi Komentar
                            </a>
                        </li>
                    </ul>
                </li>

                <li x-data="{ open: {{ request()->routeIs('admin.profile-contents.edit') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full p-2 rounded-lg
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                               transition-colors duration-200 ease-in-out">
                        <span class="ms-3 font-medium">Profil Sekolah</span>
                        <svg :class="{ 'rotate-90': open }"
                            class="w-4 h-4 transform transition-transform duration-200 text-gray-500 dark:text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="mt-1 space-y-1 rounded-lg py-1 px-3
                               bg-gray-50 dark:bg-gray-700">
                        <li>
                            <a href="{{ route('admin.profile-contents.edit', 'visi') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->route('key') == 'visi' ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Visi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.profile-contents.edit', 'misi') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->route('key') == 'misi' ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Misi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.profile-contents.edit', 'sejarah') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->route('key') == 'sejarah' ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Sejarah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.profile-contents.edit', 'struktur_pemerintahan') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->route('key') == 'struktur_pemerintahan' ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Struktur Sekolah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.profile-contents.edit', 'sekilas_desa') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->route('key') == 'sekilas_desa' ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Sekilas Sekolah
                            </a>
                        </li>
                    </ul>
                </li>

                <li x-data="{ open: {{ request()->routeIs('admin.service-procedures.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full p-2 rounded-lg
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                               transition-colors duration-200 ease-in-out">
                        <span class="ms-3 font-medium">Layanan Sekolah</span>
                        <svg :class="{ 'rotate-90': open }"
                            class="w-4 h-4 transform transition-transform duration-200 text-gray-500 dark:text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="mt-1 space-y-1 rounded-lg py-1 px-3
                               bg-gray-50 dark:bg-gray-700">
                        <li>
                            <a href="{{ route('admin.service-procedures.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.service-procedures.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Prosedur Layanan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.letter-generator.create') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.letter-generator.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Generator Surat
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('admin.institutions.index') }}"
                        class="flex items-center p-2 rounded-lg
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700
                               group transition-colors duration-200 ease-in-out
                               {{ request()->routeIs('admin.institutions.*') ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' }}">
                        <span class="ms-3 font-medium">Ekskul Sekolah</span>
                    </a>
                </li>

                <hr class="border-gray-300 dark:border-gray-600 my-2">

                <li x-data="{ open: {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full p-2 rounded-lg
                               text-gray-700 dark:text-gray-200
                               hover:bg-gray-100 dark:hover:bg-gray-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                               transition-colors duration-200 ease-in-out">
                        <span class="ms-3 font-medium">Pengaturan</span>
                        <svg :class="{ 'rotate-90': open }"
                            class="w-4 h-4 transform transition-transform duration-200 text-gray-500 dark:text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="mt-1 space-y-1 rounded-lg py-1 px-3
                               bg-gray-50 dark:bg-gray-700">
                        <li>
                            <a href="{{ route('admin.settings.edit-general-info') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.settings.edit-general-info') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Info Sekolah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}"
                                class="block w-full p-2 rounded-md text-sm
                                      text-gray-700 dark:text-gray-200
                                      hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition-colors duration-150 ease-in-out
                                      {{ request()->routeIs('admin.users.*') ? 'bg-gray-200 dark:bg-gray-600 font-semibold' : '' }}">
                                Manajemen Pengguna
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>
    <div class="pt-14 lg:ml-64 transition-all duration-200">
        @if (isset($header))
            <div class="bg-white dark:bg-gray-800 px-4 py-4 shadow">
                <h1 class="text-xl font-semibold">{{ $header }}</h1>
            </div>
        @endif
        <main>
            {{ $slot }}
        </main>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            // Alpine Store untuk mengelola tema (Dark Mode)
            Alpine.store('theme', {
                current: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)')
                    .matches ? 'dark' : 'light'),
                init() {
                    document.documentElement.classList.toggle('dark', this.current === 'dark');
                },
                toggle() {
                    this.current = this.current === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('theme', this.current);
                    document.documentElement.classList.toggle('dark', this.current === 'dark');
                }
            });

            // Alpine Store untuk mengelola UI (misalnya Sidebar)
            Alpine.store('ui', {
                sidebarOpen: window.innerWidth >= 1024, // Inisialisasi awal: terbuka jika layar lebar
                init() {
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) {
                            this.sidebarOpen = true;
                        } else {
                            // Opsional: Tutup sidebar jika layar menyusut ke mobile
                            // this.sidebarOpen = false;
                        }
                    });
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                closeSidebar() {
                    if (window.innerWidth < 1024) { // Hanya tutup secara manual jika di mode mobile
                        this.sidebarOpen = false;
                    }
                },
            });

            // Pastikan theme diinisialisasi saat Alpine berjalan
            Alpine.store('theme').init();
            Alpine.store('ui').init();
        });

        // Pastikan mainApp() masih ada untuk x-data di <html>
        function mainApp() {
            return {
                // Semua state yang dipegang oleh store sudah tidak perlu di sini lagi
                // Hanya perlu menyediakan objek kosong atau properti yang belum di-store
            }
        }
    </script>
</body>

</html>
