{{-- Kursor custom bertema HustleStore: tetap berbentuk panah, gradien ungu (default) & emas (hover klik).
     SVG data-URI = tanpa file/request tambahan. Fallback otomatis ke kursor sistem (mis. Safari lama). --}}
<style>
    :root{
        --cur-arrow: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%23a78bfa'/%3E%3Cstop offset='1' stop-color='%237c3aed'/%3E%3C/linearGradient%3E%3C/defs%3E%3Cpath d='M5.5 3.21V20.8c0 .45.54.67.85.35l4.86-4.86a.5.5 0 0 1 .35-.15h6.87c.45 0 .67-.54.35-.85L6.35 2.85a.5.5 0 0 0-.85.36Z' fill='url(%23g)' stroke='%23fff' stroke-width='1.4' stroke-linejoin='round'/%3E%3C/svg%3E") 5 3, auto;
        --cur-point: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cdefs%3E%3ClinearGradient id='h' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%23fde68a'/%3E%3Cstop offset='1' stop-color='%23f59e0b'/%3E%3C/linearGradient%3E%3C/defs%3E%3Cpath d='M5.5 3.21V20.8c0 .45.54.67.85.35l4.86-4.86a.5.5 0 0 1 .35-.15h6.87c.45 0 .67-.54.35-.85L6.35 2.85a.5.5 0 0 0-.85.36Z' fill='url(%23h)' stroke='%23fff' stroke-width='1.4' stroke-linejoin='round'/%3E%3C/svg%3E") 5 3, pointer;
    }
    html, body{cursor:var(--cur-arrow)}
    a, button, [role="button"], select, summary, .btn,
    input[type="checkbox"], input[type="radio"], input[type="submit"], label{cursor:var(--cur-point)}
    /* Input teks tetap I-beam agar nyaman mengetik */
    input[type="text"], input[type="email"], input[type="search"],
    input[type="number"], input[type="password"], input[type="tel"], textarea{cursor:text}
    button:disabled, .btn:disabled{cursor:var(--cur-arrow)}
</style>
