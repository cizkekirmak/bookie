<div id="about-postit-container" class="postit-wrapper">
    <button type="button" id="about-postit-tab" class="postit-tab" title="{{ __('About Me') }}">
        <span class="postit-pin">📌</span>
        <span class="postit-tab-text">{{ __('about bookie') }}</span>
        <span id="postit-arrow" class="postit-arrow">▲</span>
    </button>

    <div class="postit-body">
        <div class="postit-content">
            <span class="postit-tape"></span>
            
            <h4 class="postit-title">{{ __('haiiii !!') }}</h4>

            <p class="postit-desc">
                {{ __('Bookie is an independent project made by a solo developer. Thank you so much for being part of it and using it! For any bugs, ideas, or feedback, feel free to reach out anytime:') }}
            </p>

            <div class="postit-links">
                <a href="https://github.com/cizkekirmak/bookie" target="_blank" class="postit-pill" title="GitHub">
                    🐙 GitHub
                </a>
                <a href="https://linkedin.com/in/SENIN_LINKEDIN_ADRESIN" target="_blank" class="postit-pill" title="LinkedIn">
                    💼 LinkedIn
                </a>
                <a href="mailto:bookieapp.info@gmail.com" class="postit-pill" title="E-Mail">
                    ✉️ Mail
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.postit-wrapper {
    position: fixed;
    bottom: 0;
    left: 125px;
    z-index: 99998;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    transform: translateY(230px);
    transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.15);
    user-select: none;
    -webkit-tap-highlight-color: transparent !important;
}

.postit-wrapper.is-open {
    transform: translateY(0);
}

.postit-tab {
    background: #fdf3a9;
    border: 2px solid #5a7d3b;
    border-bottom: none; 
    border-radius: 12px 12px 0 0;
    padding: 6px 16px;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.08);
    font-family: 'Unkempt', cursive;
    color: #1f5117;
    font-weight: bold;
    font-size: 14.5px; /* 13px -> 14.5px */
    outline: none;
    transition: background 0.15s ease;
}

.postit-tab:hover {
    background: #faea8c;
}

.postit-pin {
    font-size: 14px;
    display: inline-block;
    transform: rotate(-15deg);
}

.postit-arrow {
    font-size: 11px;
    margin-left: 2px;
    transition: transform 0.3s ease;
}

.postit-wrapper.is-open .postit-arrow {
    transform: rotate(180deg);
}

.postit-body {
    width: 295px; /* 270px -> 295px */
    height: 230px; /* 205px -> 230px */
    background: #fdf3a9;
    border: 2px solid #5a7d3b;
    border-radius: 0 14px 0 0;
    padding: 14px 16px;
    box-shadow: 0 -5px 16px rgba(0, 0, 0, 0.14);
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
}

.postit-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
}

.postit-tape {
    position: absolute;
    top: -20px;
    right: 15px;
    width: 48px;
    height: 14px;
    background: rgba(255, 255, 255, 0.55);
    border: 1px dashed rgba(0,0,0,0.15);
    transform: rotate(4deg);
}

.postit-title {
    font-family: 'Henny Penny', cursive;
    color: #1a3c11;
    font-size: 19px; /* 17px -> 19px */
    margin: 2px 0 6px 0;
}

.postit-desc {
    font-family: 'Unkempt', cursive;
    font-size: 13.5px; /* 12px -> 13.5px */
    line-height: 1.4;
    color: #38552b;
    margin: 0 0 auto 0;
}

.postit-links {
    display: flex;
    gap: 7px;
    margin-top: 10px;
}

.postit-pill {
    flex: 1;
    text-align: center;
    background: #ffffff;
    border: 1.5px solid #5a7d3b;
    border-radius: 8px;
    padding: 6px 3px;
    font-size: 13px; /* 11.5px -> 13px */
    color: #1a3c11;
    text-decoration: none;
    font-family: 'Unkempt', cursive;
    font-weight: bold;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.postit-pill:hover {
    background: #255719;
    color: #ffffff;
    transform: translateY(-1px);
}

@media (max-width: 1024px) {
    #about-postit-container,
    #about-postit-tab,
    .about-postit-container,
    .about-postit-tab {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const postitContainer = document.getElementById('about-postit-container');
    const postitTab = document.getElementById('about-postit-tab');

    if (!postitContainer || !postitTab) return;

    postitTab.addEventListener('click', (e) => {
        e.stopPropagation();
        postitContainer.classList.toggle('is-open');
    });

    document.addEventListener('click', (e) => {
        if (!postitContainer.contains(e.target) && postitContainer.classList.contains('is-open')) {
            postitContainer.classList.remove('is-open');
        }
    });
});
</script>