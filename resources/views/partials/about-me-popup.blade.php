<!-- YUKARI ÇEKİLEN BİRLEŞİK POST-IT KARTI -->
<div id="about-postit-container" class="postit-wrapper">
    <!-- Birleşik tırnak (Açma / Kapama sekmesi) -->
    <button type="button" id="about-postit-tab" class="postit-tab" title="{{ __('About Me') }}">
        <span class="postit-pin">📌</span>
        <span class="postit-tab-text">{{ __('about bookie') }}</span>
        <span id="postit-arrow" class="postit-arrow">▲</span>
    </button>

    <!-- Post-it Gövdesi -->
    <div class="postit-body">
        <div class="postit-content">
            <span class="postit-tape"></span>
            
            <h4 class="postit-title">merhaba! ✨</h4>

            <p class="postit-desc">
                {{ __('Bookie, tek bir kişi tarafından geliştirilmiş bağımsız bir projedir. Burada olduğunuz ve kitaplığınıza kattığınız her an için çok teşekkür ederim! Herhangi bir hata bildirimi, fikir veya öneriniz için bana dilediğiniz zaman ulaşabilirsiniz:') }}
            </p>

            <!-- Sosyal / İletişim Butonları -->
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
/* KAPSAYICI - Ekranın altına kilitlenir */
.postit-wrapper {
    position: fixed;
    bottom: 0;
    left: 125px; /* Chat simgesinin hemen sağı */
    z-index: 99998;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    /* Kartın boyu kadar aşağı kaydırır, sadece tırnak görünür */
    transform: translateY(205px);
    transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.15);
    user-select: none;
    -webkit-tap-highlight-color: transparent !important;
}

/* AÇIK HALİ */
.postit-wrapper.is-open {
    transform: translateY(0);
}

/* BİRLEŞİK TIRNAK KISMI */
.postit-tab {
    background: #fdf3a9;
    border: 2px solid #5a7d3b;
    border-bottom: none; /* Kartla tek parça hissi verir */
    border-radius: 12px 12px 0 0;
    padding: 5px 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.08);
    font-family: 'Unkempt', cursive;
    color: #1f5117;
    font-weight: bold;
    font-size: 13px;
    outline: none;
    transition: background 0.15s ease;
}

.postit-tab:hover {
    background: #faea8c;
}

.postit-pin {
    font-size: 13px;
    display: inline-block;
    transform: rotate(-15deg);
}

.postit-arrow {
    font-size: 10px;
    margin-left: 2px;
    transition: transform 0.3s ease;
}

.postit-wrapper.is-open .postit-arrow {
    transform: rotate(180deg);
}

/* KART GÖVDESİ */
.postit-body {
    width: 270px;
    height: 205px;
    background: #fdf3a9;
    border: 2px solid #5a7d3b;
    border-radius: 0 14px 0 0;
    padding: 12px 14px 14px 14px;
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

/* Not kağıdı bandı efekti */
.postit-tape {
    position: absolute;
    top: -18px;
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
    font-size: 17px;
    margin: 2px 0 4px 0;
}

.postit-desc {
    font-family: 'Unkempt', cursive;
    font-size: 12px;
    line-height: 1.35;
    color: #38552b;
    margin: 0 0 auto 0;
}

/* LİNKLER - 3 BUTON YAN YANA */
.postit-links {
    display: flex;
    gap: 6px;
    margin-top: 8px;
}

.postit-pill {
    flex: 1;
    text-align: center;
    background: #ffffff;
    border: 1.5px solid #5a7d3b;
    border-radius: 8px;
    padding: 5px 2px;
    font-size: 11.5px;
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

/* MOBİL & TABLET UYARLAMA */
@media (max-width: 1024px) {
    .postit-wrapper {
        left: 105px;
    }
    .postit-body {
        width: 250px;
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