<div class="lang-switch-wrapper" style="
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #2d5a27;
    border: 1.5px solid #1a3c11;
    padding: 3px 6px;
    border-radius: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.18);
    font-family: 'Unkempt', cursive;
    font-size: 13px;
    user-select: none;
">
    <a href="{{ route('lang.switch', 'tr') }}" style="
        text-decoration: none;
        padding: 2px 8px;
        border-radius: 12px;
        color: {{ app()->getLocale() == 'tr' ? '#1f5117' : '#eaf3e4' }};
        background: {{ app()->getLocale() == 'tr' ? '#cae28c' : 'transparent' }};
        font-weight: {{ app()->getLocale() == 'tr' ? 'bold' : 'normal' }};
        transition: all 0.15s ease;
        display: inline-block;
        line-height: 1.2;
    ">tr</a>

    <a href="{{ route('lang.switch', 'en') }}" style="
        text-decoration: none;
        padding: 2px 8px;
        border-radius: 12px;
        color: {{ app()->getLocale() == 'en' ? '#1f5117' : '#eaf3e4' }};
        background: {{ app()->getLocale() == 'en' ? '#cae28c' : 'transparent' }};
        font-weight: {{ app()->getLocale() == 'en' ? 'bold' : 'normal' }};
        transition: all 0.15s ease;
        display: inline-block;
        line-height: 1.2;
    ">en</a>
</div>