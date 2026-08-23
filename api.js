/* ============================================================
   KMER NEWS — api.js
   Petite couche d'appel à l'API PHP (fetch + gestion CSRF/session)
   ============================================================ */

const API_BASE = (window.KMER_API_BASE || '/kmernews/api');

const KmerAPI = {
    _csrfToken: null,

    /** Récupère la session courante (utilisateur connecté + jeton CSRF) */
    async session() {
        const res = await fetch(`${API_BASE}/session.php`, { credentials: 'include' });
        const data = await res.json();
        if (data.csrf_token) this._csrfToken = data.csrf_token;
        return data;
    },

    /** Appel GET générique, retourne le JSON */
    async get(path) {
        const res = await fetch(`${API_BASE}/${path}`, { credentials: 'include' });
        return res.json();
    },

    /** Appel POST en JSON (login, register, contact, like, comment...) */
    async postJson(path, payload = {}) {
        const headers = { 'Content-Type': 'application/json' };
        if (this._csrfToken) headers['X-CSRF-Token'] = this._csrfToken;
        const res = await fetch(`${API_BASE}/${path}`, {
            method: 'POST',
            credentials: 'include',
            headers,
            body: JSON.stringify(payload),
        });
        return { ok: res.ok, status: res.status, data: await res.json() };
    },

    /** Appel POST en FormData (upload d'image, articles admin) */
    async postForm(path, formData) {
        if (this._csrfToken) formData.append('csrf_token', this._csrfToken);
        const res = await fetch(`${API_BASE}/${path}`, {
            method: 'POST',
            credentials: 'include',
            body: formData,
        });
        return { ok: res.ok, status: res.status, data: await res.json() };
    },

    /** Appel DELETE en JSON */
    async del(path, payload = {}) {
        const headers = { 'Content-Type': 'application/json' };
        if (this._csrfToken) headers['X-CSRF-Token'] = this._csrfToken;
        const res = await fetch(`${API_BASE}/${path}`, {
            method: 'DELETE',
            credentials: 'include',
            headers,
            body: JSON.stringify(payload),
        });
        return { ok: res.ok, status: res.status, data: await res.json() };
    },
};

/** Échappement HTML simple pour éviter les injections lors de l'insertion dynamique */
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

/** Formate une date ISO en français lisible */
function formatDateFr(iso) {
    if (!iso) return '';
    const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    const d = new Date(iso.replace(' ', 'T'));
    return `${d.getDate()} ${mois[d.getMonth()]} ${d.getFullYear()}`;
}

/** Temps écoulé relatif */
function timeAgo(iso) {
    if (!iso) return '';
    const diffSec = Math.floor((Date.now() - new Date(iso.replace(' ', 'T'))) / 1000);
    if (diffSec < 60) return "à l'instant";
    if (diffSec < 3600) return `il y a ${Math.floor(diffSec / 60)} min`;
    if (diffSec < 86400) return `il y a ${Math.floor(diffSec / 3600)} h`;
    if (diffSec < 604800) return `il y a ${Math.floor(diffSec / 86400)} j`;
    return formatDateFr(iso);
}

function excerpt(text, length = 90) {
    if (!text) return '';
    const clean = text.replace(/<[^>]*>/g, '').trim();
    return clean.length <= length ? clean : clean.slice(0, length) + '…';
}

/** Icône par rubrique (utilisée dans l'admin et le site public) */
const RUBRIQUE_ICONS = { culture: '🏛️', musique: '🎵', sport: '⚽', 'high-tech': '💻', societe: '🤝' };
function rubriqueIcon(slug) { return RUBRIQUE_ICONS[slug] || '📰'; }

/**
 * Vignette générée localement (dégradé aux couleurs de la rubrique + icône).
 * Aucune dépendance réseau externe : fonctionne même sans connexion internet,
 * essentiel pour une présentation fiable.
 */
function placeholderThumb(color = '#0048D9', icon = '📰', w = 400, h = 200) {
    const dark = shadeColor(color, -35);
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${w}" height="${h}" viewBox="0 0 ${w} ${h}">
        <defs>
            <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="${color}"/>
                <stop offset="100%" stop-color="${dark}"/>
            </linearGradient>
            <pattern id="p" width="46" height="46" patternUnits="userSpaceOnUse">
                <circle cx="23" cy="23" r="1.4" fill="rgba(255,255,255,0.16)"/>
            </pattern>
        </defs>
        <rect width="${w}" height="${h}" fill="url(#g)"/>
        <rect width="${w}" height="${h}" fill="url(#p)"/>
        <text x="50%" y="54%" font-size="${Math.round(Math.min(w, h) * 0.34)}" text-anchor="middle" dominant-baseline="middle" opacity="0.9">${icon}</text>
    </svg>`;
    return 'data:image/svg+xml;utf8,' + encodeURIComponent(svg);
}

function shadeColor(hex, percent) {
    const c = hex.replace('#', '');
    let r = parseInt(c.substr(0, 2), 16), g = parseInt(c.substr(2, 2), 16), b = parseInt(c.substr(4, 2), 16);
    r = Math.min(255, Math.max(0, Math.round(r + (percent / 100) * 255)));
    g = Math.min(255, Math.max(0, Math.round(g + (percent / 100) * 255)));
    b = Math.min(255, Math.max(0, Math.round(b + (percent / 100) * 255)));
    return `rgb(${r},${g},${b})`;
}

const RUBRIQUE_COLORS = { culture: '#7C3AED', musique: '#DB1E5B', sport: '#0EA88A', 'high-tech': '#F5A623', societe: '#DC2626' };

/** Bannières photo réelles fournies pour chaque rubrique (societe utilise un dégradé généré, pas de photo dédiée) */
const RUBRIQUE_BANNERS = { culture: 'culture.jpg', musique: 'musique.jpg', sport: 'sport.jpg', 'high-tech': 'hightech.jpg' };
function rubriqueBannerUrl(slug, basePath = '.') {
    const file = RUBRIQUE_BANNERS[slug];
    return file ? `${basePath}/assets/img/rubriques/${file}` : null;
}

/** Image de secours (locale, sans réseau) quand l'article n'a pas de photo */
function articleImage(article, w = 400, h = 200) {
    if (article && article.image) return article.image;
    const color = (article && (article.categorie_couleur || article.cat_couleur)) || RUBRIQUE_COLORS[article && article.categorie_slug] || '#0048D9';
    const icon = rubriqueIcon((article && article.categorie_slug) || '');
    return placeholderThumb(color, icon, w, h);
}
