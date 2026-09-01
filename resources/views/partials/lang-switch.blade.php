<div class="lang-switch-wrapper" style="
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: #255719;
    border: 1px solid #1a3c11;
    padding: 3px 4px;
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
    font-family: 'Unkempt', cursive;
    font-size: 13px;
    user-select: none;
    line-height: 1;
">
    <a href="{{ route('lang.switch', 'tr') }}" style="
        text-decoration: none;
        padding: 3px 8px;
        border-radius: 10px;
        color: {{ app()->getLocale() == 'tr' ? '#1f5117' : '#badfa0' }};
        background: {{ app()->getLocale() == 'tr' ? '#cae28c' : 'transparent' }};
        font-weight: {{ app()->getLocale() == 'tr' ? 'bold' : 'normal' }};
        transition: all 0.15s ease;
        display: inline-block;
        cursor: pointer;
    ">TR</a>

    <a href="{{ route('lang.switch', 'en') }}" style="
        text-decoration: none;
        padding: 3px 8px;
        border-radius: 10px;
        color: {{ app()->getLocale() == 'en' ? '#1f5117' : '#badfa0' }};
        background: {{ app()->getLocale() == 'en' ? '#cae28c' : 'transparent' }};
        font-weight: {{ app()->getLocale() == 'en' ? 'bold' : 'normal' }};
        transition: all 0.15s ease;
        display: inline-block;
        cursor: pointer;
    ">EN</a>
</div>