(function() {
    'use strict';

    var config = window.werduChatbotConfig || {};
    var ajaxUrl = config.ajaxUrl || '';
    var nonce = config.nonce || '';
    var action = config.action || 'werdu_chatbot_v3';

    var chatOffen = false;
    var beraterModus = 'normal';
    var beraterSchritt = 0;
    var nachrichtenVerlauf = [];

    function init() {
        var floatBtn = document.querySelector('.werdu-chatbot__float');
        var closeBtn = document.querySelector('.werdu-chatbot__close');
        var sendBtn = document.getElementById('werdu-chatbot-send');
        var input = document.getElementById('werdu-chatbot-input');
        var root = document.getElementById('werdu-chatbot-root');

        if (!floatBtn || !root) return;

        ladeVerlauf();

        floatBtn.addEventListener('click', function() {
            toggleChat();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                toggleChat();
            });
        }

        if (sendBtn) {
            sendBtn.addEventListener('click', function() {
                sendeNachricht();
            });
        }

        if (input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendeNachricht();
                }
            });
        }

        if (nachrichtenVerlauf.length === 0) {
            zeigeBotNachricht('Willkommen bei Werdu.de! Ich bin Ihr digitaler Berater fuer Heimspeicher und Solarbatterien. Wie kann ich Ihnen helfen?');
        } else {
            nachrichtenVerlauf.forEach(function(msg) {
                if (msg.typ === 'user') {
                    zeigeUserNachricht(msg.text, false);
                } else {
                    zeigeBotNachricht(msg.text, false);
                }
            });
        }
    }

    function toggleChat() {
        var root = document.getElementById('werdu-chatbot-root');
        if (!root) return;

        chatOffen = !chatOffen;

        if (chatOffen) {
            root.classList.remove('werdu-chatbot--hidden');
            root.classList.add('werdu-chatbot--open');
            var input = document.getElementById('werdu-chatbot-input');
            if (input) {
                setTimeout(function() {
                    input.focus();
                }, 100);
            }
        } else {
            root.classList.remove('werdu-chatbot--open');
            root.classList.add('werdu-chatbot--hidden');
        }
    }

    function sendeNachricht() {
        var input = document.getElementById('werdu-chatbot-input');
        if (!input) return;

        var text = input.value.trim();
        if (!text) return;

        zeigeUserNachricht(text, true);
        input.value = '';

        zeigeTyping();

        var schnelleAntwort = pruefeSchnelleAntwort(text);
        if (schnelleAntwort) {
            setTimeout(function() {
                entferneTyping();
                zeigeBotNachricht(schnelleAntwort, true);
            }, 600);
            return;
        }

        var formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', nonce);
        formData.append('question', text);
        formData.append('mode', beraterModus);
        formData.append('step', beraterSchritt);

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            entferneTyping();

            if (data.success && data.data) {
                var antwort = data.data.antwort || '';
                beraterModus = data.data.modus || 'normal';
                beraterSchritt = data.data.schritt || 0;

                zeigeBotNachricht(antwort, true);

                if (data.data.optionen && data.data.optionen.length > 0) {
                    zeigeOptionen(data.data.optionen);
                }
            } else {
                zeigeBotNachricht('Entschuldigung, ich konnte Ihre Anfrage nicht verarbeiten. Bitte versuchen Sie es erneut oder kontaktieren Sie uns per E-Mail an service@werdu.de.', true);
            }
        })
        .catch(function(error) {
            entferneTyping();
            zeigeBotNachricht('Es ist ein Verbindungsfehler aufgetreten. Bitte pruefen Sie Ihre Internetverbindung.', true);
        });
    }

    function pruefeSchnelleAntwort(text) {
        var lower = text.toLowerCase();

        if (lower.indexOf('hallo') !== -1 || lower.indexOf('hi') !== -1 || lower.indexOf('guten tag') !== -1) {
            return 'Hallo! Wie kann ich Ihnen heute helfen? Fragen Sie mich zu unseren Heimspeichern, Preisen oder Technologien.';
        }

        if (lower.indexOf('danke') !== -1) {
            return 'Gerne! Haben Sie noch weitere Fragen zu Heimspeichern oder Solarbatterien?';
        }

        if (lower.indexOf('tschuess') !== -1 || lower.indexOf('auf wiedersehen') !== -1 || lower.indexOf('bye') !== -1) {
            return 'Auf Wiedersehen! Bei weiteren Fragen stehe ich Ihnen jederzeit zur Verfuegung.';
        }

        return null;
    }

    function zeigeUserNachricht(text, speichern) {
        var container = document.getElementById('werdu-chatbot-messages');
        if (!container) return;

        var msg = document.createElement('div');
        msg.className = 'werdu-chatbot__message werdu-chatbot__message--user';
        msg.textContent = text;
        container.appendChild(msg);
        scrollToBottom();

        if (speichern) {
            nachrichtenVerlauf.push({typ: 'user', text: text});
            speichereVerlauf();
        }
    }

    function zeigeBotNachricht(text, speichern) {
        var container = document.getElementById('werdu-chatbot-messages');
        if (!container) return;

        var msg = document.createElement('div');
        msg.className = 'werdu-chatbot__message werdu-chatbot__message--bot';
        msg.innerHTML = text.replace(/\n/g, '<br>');
        container.appendChild(msg);
        scrollToBottom();

        if (speichern) {
            nachrichtenVerlauf.push({typ: 'bot', text: text});
            speichereVerlauf();
        }
    }

    function zeigeOptionen(optionen) {
        var container = document.getElementById('werdu-chatbot-messages');
        if (!container || !optionen || optionen.length === 0) return;

        var wrapper = document.createElement('div');
        wrapper.className = 'werdu-chatbot__options';

        optionen.forEach(function(opt) {
            var btn = document.createElement('button');
            btn.className = 'werdu-chatbot__option-btn';
            btn.textContent = opt;
            btn.addEventListener('click', function() {
                zeigeUserNachricht(opt, true);
                zeigeTyping();

                var formData = new FormData();
                formData.append('action', action);
                formData.append('nonce', nonce);
                formData.append('question', opt);
                formData.append('mode', beraterModus);
                formData.append('step', beraterSchritt);

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    entferneTyping();
                    if (data.success && data.data) {
                        beraterModus = data.data.modus || 'normal';
                        beraterSchritt = data.data.schritt || 0;
                        zeigeBotNachricht(data.data.antwort, true);
                        if (data.data.optionen && data.data.optionen.length > 0) {
                            zeigeOptionen(data.data.optionen);
                        }
                    }
                })
                .catch(function() {
                    entferneTyping();
                });
            });
            wrapper.appendChild(btn);
        });

        container.appendChild(wrapper);
        scrollToBottom();
    }

    function zeigeTyping() {
        var container = document.getElementById('werdu-chatbot-messages');
        if (!container) return;

        var typing = document.createElement('div');
        typing.id = 'werdu-chatbot-typing';
        typing.className = 'werdu-chatbot__typing';
        typing.innerHTML = '<span></span><span></span><span></span>';
        container.appendChild(typing);
        scrollToBottom();
    }

    function entferneTyping() {
        var typing = document.getElementById('werdu-chatbot-typing');
        if (typing) {
            typing.remove();
        }
    }

    function scrollToBottom() {
        var container = document.getElementById('werdu-chatbot-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function speichereVerlauf() {
        try {
            localStorage.setItem('werdu_chatbot_verlauf', JSON.stringify(nachrichtenVerlauf.slice(-20)));
        } catch(e) {}
    }

    function ladeVerlauf() {
        try {
            var gespeichert = localStorage.getItem('werdu_chatbot_verlauf');
            if (gespeichert) {
                nachrichtenVerlauf = JSON.parse(gespeichert);
            }
        } catch(e) {
            nachrichtenVerlauf = [];
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();