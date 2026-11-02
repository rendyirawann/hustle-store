@php $s = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">'; $e = '</svg>'; @endphp
@switch($name)
    @case('phone') {!! $s !!}<rect x="6" y="2" width="12" height="20" rx="3"/><path d="M11 18h2"/>{!! $e !!} @break
    @case('wifi') {!! $s !!}<path d="M5 12a10 10 0 0 1 14 0M8.5 15.5a5 5 0 0 1 7 0"/><circle cx="12" cy="19" r="1" fill="currentColor"/>{!! $e !!} @break
    @case('game') {!! $s !!}<rect x="2" y="6" width="20" height="12" rx="4"/><path d="M7 12h3M8.5 10.5v3"/><circle cx="16" cy="11" r="1" fill="currentColor"/><circle cx="18" cy="14" r="1" fill="currentColor"/>{!! $e !!} @break
    @case('wallet') {!! $s !!}<rect x="3" y="6" width="18" height="13" rx="3"/><path d="M3 10h18M16 14h2"/>{!! $e !!} @break
    @case('play') {!! $s !!}<rect x="3" y="4" width="18" height="16" rx="4"/><path d="M10 9l5 3-5 3z" fill="currentColor"/>{!! $e !!} @break
    @case('ticket') {!! $s !!}<path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2 2 2 0 0 0 0 4 2 2 0 0 1-2 2H6a2 2 0 0 1-2-2 2 2 0 0 0 0-4z"/><path d="M14 6v12" stroke-dasharray="2 2"/>{!! $e !!} @break
    @case('bolt') {!! $s !!}<path d="M13 2 4 14h6l-1 8 9-12h-6z"/>{!! $e !!} @break
    @case('grid') {!! $s !!}<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>{!! $e !!} @break
    @case('tag') {!! $s !!}<path d="M3 12 12 3h7v7l-9 9z"/><circle cx="15.5" cy="7.5" r="1.3"/>{!! $e !!} @break
    @case('shield') {!! $s !!}<path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/>{!! $e !!} @break
    @case('headset') {!! $s !!}<path d="M4 13v-1a8 8 0 0 1 16 0v1"/><rect x="3" y="13" width="4" height="6" rx="2"/><rect x="17" y="13" width="4" height="6" rx="2"/><path d="M20 19a4 4 0 0 1-4 3h-2"/>{!! $e !!} @break
    @case('bag') {!! $s !!}<path d="M6 8h12l-1 12H7z"/><path d="M9 8a3 3 0 0 1 6 0"/>{!! $e !!} @break
    @case('card') {!! $s !!}<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18M7 15h4"/>{!! $e !!} @break
    @case('check') {!! $s !!}<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>{!! $e !!} @break
    @default {!! $s !!}<circle cx="12" cy="12" r="9"/>{!! $e !!}
@endswitch
