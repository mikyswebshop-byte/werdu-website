<?php
/**
 * Werdu.de WhatsApp Chat Bubble v2.0
 * Position: fixed, bottom-right ABOVE AI chat
 * Unieke class names: .wd-wa-* (geen conflict met chatbot)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_footer', 'werdu_whatsapp_chat', 999 );

function werdu_whatsapp_chat() {
    if ( is_admin() || wp_doing_ajax() ) { return; }

    // VERVANG DIT MET JOUW ECHTE NUMMER
    $phone = '4915120229842';
    $message = urlencode( 'Hallo, ich habe eine Frage zu Ihren Heimspeichern.' );
    $wa_url = 'https://wa.me/' . $phone . '?text=' . $message;
    ?>

    <!-- WhatsApp Chat Bubble -->
    <div class="wd-wa-float" id="wd-wa-bubble" onclick="wdWaToggle()" aria-label="WhatsApp Chat öffnen" role="button" tabindex="0">
        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 2C8.268 2 2 8.268 2 16c0 2.585.66 5.017 1.82 7.153L2 30l6.847-1.82A13.936 13.936 0 0016 30c7.732 0 14-6.268 14-14S23.732 2 16 2zm0 25.2a11.16 11.16 0 01-5.7-1.56l-.41-.24-4.09 1.09 1.09-4.09-.24-.41A11.2 11.2 0 1116 27.2zm6.16-8.4c-.34-.17-2.01-.99-2.32-1.1-.31-.11-.54-.17-.76.17-.22.34-.87 1.1-1.07 1.33-.2.22-.4.25-.74.08-.34-.17-1.43-.53-2.72-1.68-1.01-.9-1.69-2.01-1.89-2.35-.2-.34-.02-.53.15-.7.15-.15.34-.4.51-.59.17-.2.23-.34.34-.56.11-.22.06-.42-.03-.59-.08-.17-.76-1.83-1.04-2.5-.27-.66-.55-.57-.76-.58l-.65-.01c-.22 0-.59.08-.9.42-.31.34-1.18 1.15-1.18 2.81 0 1.66 1.21 3.26 1.38 3.49.17.22 2.38 3.64 5.77 5.1.8.35 1.43.56 1.92.71.81.26 1.54.22 2.12.14.65-.1 2.01-.82 2.29-1.61.28-.8.28-1.48.2-1.61-.08-.14-.31-.22-.65-.37z" fill="#ffffff"/>
        </svg>
    </div>

    <!-- Chat Box -->
    <div class="wd-wa-box" id="wd-wa-box">
        <div class="wd-wa-header">
            <span class="wd-wa-title">&#9889; ACC Heimspeicher</span>
            <span class="wd-wa-close" onclick="wdWaToggle()" aria-label="Schließen">&#10005;</span>
        </div>
        <div class="wd-wa-body">
            <p class="wd-wa-welcome">Hallo! &#128075;<br>Wie können wir Ihnen helfen?</p>
            <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener" class="wd-wa-action">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.7.15-.15.34-.4.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0017.472.72C15.647.257 13.84.02 12.01.02 5.423.02.001 5.442.001 12.03c0 2.117.553 4.182 1.604 6.003L.025 24l6.115-1.607a11.93 11.93 0 005.718 1.46h.005c6.585 0 12.008-5.42 12.008-12.007 0-3.206-1.25-6.22-3.523-8.49"/>
                </svg>
                WhatsApp Nachricht senden
            </a>
            <p class="wd-wa-note">Oder schreiben Sie uns an:<br><a href="mailto:service@werdu.de">service@werdu.de</a></p>
        </div>
    </div>

    <style>
    .wd-wa-float {
        position: fixed !important;
        bottom: 140px !important;
        right: 24px !important;
        left: auto !important;
        width: 56px !important;
        height: 56px !important;
        background: linear-gradient(135deg, #25D366, #128C7E) !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4) !important;
        z-index: 99999 !important;
        transition: all 0.3s ease !important;
        animation: wd-wa-bounce 2s ease-in-out infinite !important;
    }
    .wd-wa-float:hover {
        transform: scale(1.1) !important;
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5) !important;
    }
    .wd-wa-float svg {
        width: 32px !important;
        height: 32px !important;
    }
    @keyframes wd-wa-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .wd-wa-box {
        position: fixed !important;
        bottom: 210px !important;
        right: 24px !important;
        left: auto !important;
        width: 300px !important;
        background: #fff !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
        z-index: 99998 !important;
        overflow: hidden !important;
        display: none !important;
        opacity: 0 !important;
        transform: translateY(20px) !important;
        transition: all 0.3s ease !important;
    }
    .wd-wa-box.wd-wa-open {
        display: block !important;
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
    .wd-wa-header {
        background: linear-gradient(135deg, #25D366, #128C7E) !important;
        color: #fff !important;
        padding: 14px 18px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }
    .wd-wa-title { font-weight: 700 !important; font-size: 0.95rem !important; }
    .wd-wa-close {
        cursor: pointer !important;
        font-size: 1.2rem !important;
        opacity: 0.8 !important;
        transition: opacity 0.2s !important;
    }
    .wd-wa-close:hover { opacity: 1 !important; }
    .wd-wa-body { padding: 20px !important; text-align: center !important; }
    .wd-wa-welcome {
        font-size: 0.95rem !important;
        color: #333 !important;
        margin-bottom: 16px !important;
        line-height: 1.5 !important;
    }
    .wd-wa-action {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        background: #25D366 !important;
        color: #fff !important;
        padding: 12px 20px !important;
        border-radius: 50px !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        text-decoration: none !important;
        transition: all 0.2s !important;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3) !important;
    }
    .wd-wa-action:hover {
        background: #128C7E !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4) !important;
        color: #fff !important;
        text-decoration: none !important;
    }
    .wd-wa-note {
        font-size: 0.8rem !important;
        color: #666 !important;
        margin-top: 14px !important;
        line-height: 1.5 !important;
    }
    .wd-wa-note a { color: #128C7E !important; font-weight: 600 !important; }

    @media (max-width: 768px) {
        .wd-wa-float {
            width: 50px !important;
            height: 50px !important;
            bottom: 125px !important;
            right: 15px !important;
        }
        .wd-wa-float svg { width: 28px !important; height: 28px !important; }
        .wd-wa-box {
            width: calc(100vw - 30px) !important;
            right: 15px !important;
            left: 15px !important;
            bottom: 185px !important;
        }
    }
    @media (max-width: 480px) {
        .wd-wa-float {
            width: 48px !important;
            height: 48px !important;
            bottom: 115px !important;
            right: 10px !important;
        }
        .wd-wa-float svg { width: 26px !important; height: 26px !important; }
        .wd-wa-box {
            width: calc(100vw - 20px) !important;
            right: 10px !important;
            left: 10px !important;
            bottom: 170px !important;
        }
    }
    </style>

    <script>
    function wdWaToggle() {
        var box = document.getElementById('wd-wa-box');
        box.classList.toggle('wd-wa-open');
    }
    document.addEventListener('click', function(e) {
        var bubble = document.getElementById('wd-wa-bubble');
        var box = document.getElementById('wd-wa-box');
        if (!bubble.contains(e.target) && !box.contains(e.target)) {
            box.classList.remove('wd-wa-open');
        }
    });
    document.getElementById('wd-wa-bubble').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            wdWaToggle();
        }
    });
    </script>
    <?php
}