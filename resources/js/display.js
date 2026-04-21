import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import lottie from 'lottie-web';

window.Pusher = Pusher;

// ── Animation logo Lottie (joue 1x, pause 5s, recommence) ─
const logoAnim = lottie.loadAnimation({
    container:  document.getElementById('lottie-logo'),
    renderer:   'svg',
    loop:       false,
    autoplay:   true,
    path:       '/animations/logo-dark.json',
});
logoAnim.addEventListener('complete', () => {
    setTimeout(() => logoAnim.goToAndPlay(0), 5000);
});

// ── Données serveur via data-* ────────────────────────────
const root = document.getElementById('displayRoot');
if (!root) throw new Error('display.js chargé hors de la vue display.');

// ── Camembert (conic-gradient) ────────────────────────────
function renderPie(bronze, silver, gold) {
    const total = bronze + silver + gold || 1;
    const goldPct   = gold   / total * 100;
    const silverPct = silver / total * 100;
    const bronzePct = bronze / total * 100;

    const goldEnd   = goldPct.toFixed(1) + '%';
    const silverEnd = (goldPct + silverPct).toFixed(1) + '%';

    const pie = document.getElementById('pie-chart');
    if (pie) {
        pie.style.setProperty('--gold-end',   goldEnd);
        pie.style.setProperty('--silver-end', silverEnd);
    }

    const el = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
    el('pct-gold',   Math.round(goldPct)   + '%');
    el('pct-silver', Math.round(silverPct) + '%');
    el('pct-bronze', Math.round(bronzePct) + '%');
    el('gold-pct-kpi', Math.round(goldPct) + '%');
}

function updateStats(stats) {
    renderPie(stats.bronze ?? 0, stats.silver ?? 0, stats.gold ?? 0);
    const el = (id, val) => { const e = document.getElementById(id); if (e) e.textContent = val; };
    el('today-count',  stats.today_count  ?? 0);
    el('today-points', stats.today_points ?? 0);
    el('week-count',   stats.week_count   ?? 0);
}

// Init au chargement depuis data-* du DOM
const statsCol = document.getElementById('stats-col');
if (statsCol) {
    renderPie(
        parseInt(statsCol.dataset.bronze ?? 0),
        parseInt(statsCol.dataset.silver ?? 0),
        parseInt(statsCol.dataset.gold   ?? 0),
    );
}

// ── Son ───────────────────────────────────────────────────
let audioCtx     = null;
let audioEnabled = false;

window.enableAudio = function () {
    audioCtx     = new (window.AudioContext || window.webkitAudioContext)();
    audioEnabled = true;
    const btn    = document.getElementById('soundBtn');
    btn.textContent = '🔊 Son activé';
    btn.classList.add('display__sound-btn--active');
    playFanfare(); // son de test
};

function playFanfare() {
    if (!audioEnabled || !audioCtx) return;

    const notes = [
        { freq: 523.25, t: 0.00 },
        { freq: 659.25, t: 0.15 },
        { freq: 783.99, t: 0.30 },
        { freq: 1046.5, t: 0.45 },
        { freq: 1046.5, t: 0.60 },
        { freq: 1318.5, t: 0.70 },
    ];

    notes.forEach(({ freq, t }) => {
        const osc  = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type            = 'triangle';
        osc.frequency.value = freq;
        const s = audioCtx.currentTime + t;
        gain.gain.setValueAtTime(0, s);
        gain.gain.linearRampToValueAtTime(0.35, s + 0.04);
        gain.gain.linearRampToValueAtTime(0, s + 0.25);
        osc.start(s);
        osc.stop(s + 0.3);
    });
}

// ── Horloge ───────────────────────────────────────────────
function updateClock() {
    const el = document.getElementById('clock');
    if (el) el.textContent = new Date().toLocaleTimeString('fr-FR', {
        hour: '2-digit', minute: '2-digit', second: '2-digit',
    });
}
updateClock();
setInterval(updateClock, 1000);

// ── Confettis ─────────────────────────────────────────────
const COLORS = ['#6366f1', '#a855f7', '#fbbf24', '#34d399', '#f87171', '#60a5fa'];

function launchConfetti() {
    const container = document.getElementById('confettiContainer');
    if (!container) return;
    container.innerHTML = '';
    for (let i = 0; i < 90; i++) {
        const el = document.createElement('div');
        el.className   = 'confetti-piece';
        el.style.cssText = [
            `left:${Math.random() * 100}vw`,
            `background:${COLORS[Math.floor(Math.random() * COLORS.length)]}`,
            `animation-delay:${Math.random() * 1.5}s`,
            `animation-duration:${2 + Math.random() * 1.5}s`,
            `width:${8 + Math.random() * 8}px`,
            `height:${10 + Math.random() * 10}px`,
            `transform:rotate(${Math.random() * 360}deg)`,
        ].join(';');
        container.appendChild(el);
    }
    setTimeout(() => { container.innerHTML = ''; }, 5000);
}

// ── Popup ─────────────────────────────────────────────────
const LEVEL_CFG = {
    bronze: { emoji: '🥉', label: '🥉 Découverte', cls: 'popup__level--bronze', ptsCls: 'popup__points--brand' },
    silver: { emoji: '🥈', label: '🥈 Qualifié',   cls: 'popup__level--silver', ptsCls: 'popup__points--brand' },
    gold:   { emoji: '🔥', label: '🥇 Pret à signer',   cls: 'popup__level--gold',   ptsCls: 'popup__points--gold'  },
};

let popupTimeout = null;

function showPopup(appt) {
    const cfg = LEVEL_CFG[appt.level] ?? LEVEL_CFG.bronze;

    document.getElementById('popupEmoji').textContent  = cfg.emoji;
    document.getElementById('popupUser').textContent   = appt.user_name;
    document.getElementById('popupClient').textContent = appt.client_name;

    const lvl       = document.getElementById('popupLevel');
    lvl.textContent = cfg.label;
    lvl.className   = `popup__level ${cfg.cls}`;

    const pts       = document.getElementById('popupPoints');
    pts.textContent = `+${appt.points_value} pts`;
    pts.className   = `popup__points ${cfg.ptsCls}`;

    const popup   = document.getElementById('popup');
    const overlay = document.getElementById('popupOverlay');
    popup.classList.remove('is-hiding');
    popup.style.display   = 'block';
    overlay.style.display = 'block';

    // Barre de timer
    const bar        = document.getElementById('popupTimer');
    bar.style.transition = 'none';
    bar.style.width      = '100%';
    requestAnimationFrame(() => {
        bar.style.transition = 'width 6s linear';
        bar.style.width      = '0%';
    });

    clearTimeout(popupTimeout);
    popupTimeout = setTimeout(hidePopup, 6000);

    playFanfare();
    launchConfetti();
}

window.hidePopup = function () {
    const popup = document.getElementById('popup');
    popup.classList.add('is-hiding');
    setTimeout(() => {
        popup.style.display = 'none';
        document.getElementById('popupOverlay').style.display = 'none';
    }, 350);
};

// Fermer popup en cliquant l'overlay
document.getElementById('popupOverlay')?.addEventListener('click', window.hidePopup);

// ── Mise à jour du leaderboard ────────────────────────────
function updateLeaderboard(data) {
    const maxPts = Math.max(...data.map(u => u.points), 1);
    const list   = document.getElementById('leaderboard-list');
    if (!list) return;

    // Reconstruction complète : tri + nouveaux utilisateurs inclus
    list.innerHTML = data.map((user, rank) => {
        const rankClass = rank === 0 ? '1' : rank === 1 ? '2' : rank === 2 ? '3' : 'n';
        const pct       = Math.round(user.points / maxPts * 100);
        const badges    = Array.isArray(user.badges) ? user.badges.join(' ') : '';
        const goldCls   = rank === 0 ? ' display__avatar--gold' : '';
        const barGold   = rank === 0 ? ' leaderboard__bar-fill--gold' : '';
        const initials  = user.name.substring(0, 2).toUpperCase();
        return `
        <div class="display__row" data-user-id="${user.id}">
            <div class="display__rank display__rank--${rankClass}">${rank + 1}</div>
            <div class="display__avatar${goldCls}">${initials}</div>
            <div class="display__info">
                <div class="display__name">${user.name}</div>
                <div class="leaderboard__bar-wrap">
                    <div class="leaderboard__bar-fill${barGold}" style="width:${pct}%"></div>
                </div>
                <div class="display__sub">${user.appointments_count} RDV${badges ? ' · ' + badges : ''}</div>
            </div>
            <div class="display__score">
                <div class="display__score-value">${user.points}</div>
                <div class="display__score-label">pts</div>
            </div>
        </div>`;
    }).join('');

    updatePodium(data);
}

function updatePodium(data) {
    // Ordre HTML du podium : slot 0 = 2e, slot 1 = 1er, slot 2 = 3e
    const slots   = document.querySelectorAll('.podium__slot');
    const mapping = [1, 0, 2];
    slots.forEach((slot, i) => {
        const user = data[mapping[i]];
        if (!user) return;
        const avatar = slot.querySelector('.podium__avatar');
        const name   = slot.querySelector('.podium__name');
        const pts    = slot.querySelector('.podium__pts');
        if (avatar) avatar.textContent = user.name.substring(0, 2).toUpperCase();
        if (name)   name.textContent   = user.name;
        if (pts)    pts.textContent    = user.points + ' pts';
    });
}

// ── WebSockets via Reverb ─────────────────────────────────
const echo = new Echo({
    broadcaster:      'reverb',
    key:              import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:           import.meta.env.VITE_REVERB_HOST      ?? 'localhost',
    wsPort:      parseInt(import.meta.env.VITE_REVERB_PORT  ?? 8080),
    wssPort:     parseInt(import.meta.env.VITE_REVERB_PORT  ?? 8080),
    forceTLS:        (import.meta.env.VITE_REVERB_SCHEME    ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// ── Rafraîchissement de l'iframe Google Calendar ──────────
function refreshCalendar() {
    const iframe = document.querySelector('.display__center iframe');
    if (!iframe) return;
    const src = iframe.src;
    iframe.src = '';
    setTimeout(() => { iframe.src = src; }, 100);
}

// Rafraîchissement automatique toutes les 5 minutes
setInterval(refreshCalendar, 5 * 60 * 1000);

// ── Feed derniers RDV ─────────────────────────────────────
const LEVEL_EMOJI = { gold: '🥇', silver: '🥈', bronze: '🥉' };

function prependFeed(appt) {
    const list = document.getElementById('feed-list');
    if (!list) return;

    const now   = new Date();
    const time  = now.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' })
                + ' ' + now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

    const item  = document.createElement('div');
    item.className = `display__feed-item display__feed-item--new`;
    item.innerHTML = `
        <div class="display__feed-level">${LEVEL_EMOJI[appt.level] ?? '🥉'}</div>
        <div class="display__feed-info">
            <div class="display__feed-client">${appt.client_name}</div>
            <div class="display__feed-meta">${appt.user_name} · ${time}</div>
        </div>
        <div class="display__feed-pts">+${appt.points_value}</div>
    `;

    list.prepend(item);

    // Retire la surbrillance après 4s
    setTimeout(() => item.classList.remove('display__feed-item--new'), 4000);

    // Garde max 20 entrées
    while (list.children.length > 20) list.removeChild(list.lastChild);
}

echo.channel('winboard')
    .listen('.appointment.declared', (e) => {
        updateLeaderboard(e.leaderboard);
        prependFeed(e.appointment);
        showPopup(e.appointment);
        if (e.stats) updateStats(e.stats);
        // Délai court pour que Google ait le temps d'enregistrer l'événement
        setTimeout(refreshCalendar, 3000);
    });

echo.connector.pusher.connection.bind('connected',    () => console.info('✅ WinBoard connecté au canal WebSocket "winboard"'));
echo.connector.pusher.connection.bind('disconnected', () => console.warn('⚠️ WinBoard déconnecté du WebSocket'));
