<div class="lang-switch-wrapper" style="
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #2d5a27;
    border: 2px solid #1a3c11;
    padding: 5px 8px;
    border-radius: 24px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.22);
    font-family: 'Unkempt', cursive;
    font-size: 16px;
    user-select: none;
">
    <a href="{{ route('lang.switch', 'tr') }}" style="
        text-decoration: none;
        padding: 5px 14px;
        border-radius: 16px;
        color: {{ app()->getLocale() == 'tr' ? '#1f5117' : '#eaf3e4' }};
        background: {{ app()->getLocale() == 'tr' ? '#cae28c' : 'transparent' }};
        font-weight: {{ app()->getLocale() == 'tr' ? 'bold' : 'normal' }};
        transition: all 0.15s ease;
        display: inline-block;
        line-height: 1.2;
        cursor: pointer;
    ">TR</a>

    <a href="{{ route('lang.switch', 'en') }}" style="
        text-decoration: none;
        padding: 5px 14px;
        border-radius: 16px;
        color: {{ app()->getLocale() == 'en' ? '#1f5117' : '#eaf3e4' }};
        background: {{ app()->getLocale() == 'en' ? '#cae28c' : 'transparent' }};
        font-weight: {{ app()->getLocale() == 'en' ? 'bold' : 'normal' }};
        transition: all 0.15s ease;
        display: inline-block;
        line-height: 1.2;
        cursor: pointer;
    ">EN</a>
</div>