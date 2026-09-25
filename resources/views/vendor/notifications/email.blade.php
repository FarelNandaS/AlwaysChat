<x-mail::message>
{{-- Header Branding AlwaysChat (Tanpa Logo Gambar) --}}
<x-slot:header>
    <x-mail::header :url="config('app.url')">
        <!-- Teks Branding AlwaysChat -->
        <div style="font-size: 22px; font-weight: 800; color: #4f46e5; letter-spacing: -0.025em; text-decoration: none;">
            AlwaysChat
        </div>
    </x-mail::header>
</x-slot:header>

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# Halo!
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Salam hangat,<br>
**Tim AlwaysChat**
@endif

{{-- Subcopy (Bahasa Indonesia) --}}
@isset($actionText)
<x-slot:subcopy>
Jika Anda mengalami kendala saat menekan tombol "{{ $actionText }}", salin dan tempel URL di bawah ini langsung ke peramban (browser) web Anda:

<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>