
function renderHeader(basePath = '.') {
    const el = document.getElementById('site-header');
    if (!el) return;
    el.innerHTML = `
    <header class="site-header">
        <div class="header-inner">
            <a href="${basePath}/index.html" class="logo">
                <img src="${basePath}/assets/img/logo.jpg" alt="Kmer News" class="site-logo-img">
                <span class="logo-text">
                    <strong>KMER<span>NEWS</span></strong>
                    <small>L'info en temps réel</small>
                </span>
            </a>
            <nav class="main-nav" id="mainNav"></nav>
            <form class="header-search" id="headerSearchForm">
                <input type="search" id="headerSearchInput" placeholder="Rechercher…" aria-label="Rechercher un article">
                <button type="submit" aria-label="Lancer la recherche">🔍</button>
            </form>
            <div class="header-actions" id="headerActions">
                <button class="burger" id="burgerBtn" aria-label="Menu">☰</button>
            </div>
        </div>
    </header>
    <div id="flashZone" class="container" style="padding-top:0;"></div>`;

    document.getElementById('headerSearchForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const q = document.getElementById('headerSearchInput').value.trim();
        window.location.href = `${basePath}/search.html?q=${encodeURIComponent(q)}`;
    });

    const burger = document.getElementById('burgerBtn');
    const nav = document.getElementById('mainNav');
    burger.addEventListener('click', () => nav.classList.toggle('open'));
    document.addEventListener('click', (e) => {
        if (!nav.contains(e.target) && !burger.contains(e.target)) nav.classList.remove('open');
    });

    loadNavCategories(basePath);
    updateAuthUI(basePath);
}

async function loadNavCategories(basePath) {
    const nav = document.getElementById('mainNav');
    if (!nav) return;
    try {
        const data = await KmerAPI.get('categories.php');
        if (!data.success) return;
        nav.innerHTML = data.categories.map(c => {
            const cls = c.slug === 'high-tech' ? 'hightech' : c.slug;
            return `<a class="nav-pill ${cls}" href="${basePath}/category.html?slug=${encodeURIComponent(c.slug)}">${escapeHtml(c.nom)}</a>`;
        }).join('');
    } catch (e) { console.error('Erreur chargement rubriques', e); }
}

async function updateAuthUI(basePath) {
    const actions = document.getElementById('headerActions');
    if (!actions) return;
    const burgerHtml = '<button class="burger" id="burgerBtn2" aria-label="Menu">☰</button>';

    try {
        const session = await KmerAPI.session();
        if (session.logged_in) {
            const u = session.user;
            if (u.role === 'admin') {
                actions.innerHTML = `
                    <a href="${basePath}/admin/index.html" class="btn btn-outline btn-sm"><span class="txt">Administration</span> ⚙️</a>
                    <a href="#" id="logoutBtn" class="btn btn-outline btn-sm">Déconnexion</a>` + burgerHtml;
                document.getElementById('logoutBtn').addEventListener('click', async (e) => {
                    e.preventDefault();
                    const r = await KmerAPI.postJson('logout.php');
                    window.location.href = basePath + '/' + (r.data.redirect || 'index.html').replace('../', '');
                });
            } else {
                const initial = escapeHtml((u.prenom || '?').charAt(0).toUpperCase());
                actions.innerHTML = `
                    <div class="profile-widget" id="profileWidget">
                        <button class="user-chip" id="profileToggle" type="button">
                            <span class="avatar-mini">${initial}</span>
                            <span class="txt">${escapeHtml(u.prenom)}</span>
                            <span class="chevron">▾</span>
                        </button>
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="pd-head">
                                <span class="avatar-mini big">${initial}</span>
                                <div>
                                    <strong>${escapeHtml(u.prenom)} ${escapeHtml(u.nom)}</strong>
                                    <small>${escapeHtml(u.email)}</small>
                                </div>
                            </div>
                            <a href="${basePath}/espace/profil.html">👤 Mon espace</a>
                            <a href="${basePath}/espace/nouvel_article.html">✍️ Proposer un article</a>
                            <a href="#" id="logoutBtn">🚪 Déconnexion</a>
                        </div>
                    </div>` + burgerHtml;

                const widget = document.getElementById('profileWidget');
                const toggle = document.getElementById('profileToggle');
                const dropdown = document.getElementById('profileDropdown');
                toggle.addEventListener('click', (e) => { e.stopPropagation(); dropdown.classList.toggle('open'); });
                document.addEventListener('click', (e) => { if (!widget.contains(e.target)) dropdown.classList.remove('open'); });

                document.getElementById('logoutBtn').addEventListener('click', async (e) => {
                    e.preventDefault();
                    const r = await KmerAPI.postJson('logout.php');
                    window.location.href = basePath + '/' + (r.data.redirect || 'index.html').replace('../', '');
                });
            }
        } else {
            actions.innerHTML = `
                <a href="${basePath}/login.html" class="btn btn-outline btn-sm">Connexion</a>
                <a href="${basePath}/register.html" class="btn btn-primary btn-sm">S'inscrire</a>` + burgerHtml;
        }
        // Ré-attache le burger (recréé dans le innerHTML)
        const burger2 = document.getElementById('burgerBtn2');
        const nav = document.getElementById('mainNav');
        if (burger2 && nav) {
            burger2.addEventListener('click', () => nav.classList.toggle('open'));
        }
        window.KMER_SESSION = session;
        document.dispatchEvent(new CustomEvent('kmer:session-ready', { detail: session }));
    } catch (e) {
        console.error('Erreur session', e);
    }
}

function renderFooter(basePath = '.') {
    const el = document.getElementById('site-footer');
    if (!el) return;
    el.innerHTML = `
    <footer class="site-footer-full">
        <div class="container footer-grid">
            <div class="footer-col">
                <div class="logo" style="color:#fff; margin-bottom:12px;">
                    <img src="${basePath}/assets/img/logo.jpg" alt="Kmer News" class="site-logo-img">
                    <span class="logo-text"><strong>KMER<span>NEWS</span></strong><small>L'info en temps réel</small></span>
                </div>
                <p class="footer-desc">Le journal numérique interactif qui raconte le Cameroun d'aujourd'hui : culture, musique, sport et high-tech.</p>
            </div>
            <div class="footer-col">
                <h5>Rubriques</h5>
                <a href="${basePath}/category.html?slug=culture">Culture</a>
                <a href="${basePath}/category.html?slug=musique">Musique</a>
                <a href="${basePath}/category.html?slug=sport">Sport</a>
                <a href="${basePath}/category.html?slug=high-tech">High-Tech</a>
            </div>
            <div class="footer-col">
                <h5>Le site</h5>
                <a href="${basePath}/index.html">Accueil</a>
                <a href="${basePath}/apropos.html">À propos</a>
                <a href="${basePath}/search.html">Recherche</a>
                <a href="${basePath}/contact.html">Contact</a>
            </div>
            <div class="footer-col">
                <h5>Mon compte</h5>
                <a href="${basePath}/login.html">Connexion</a>
                <a href="${basePath}/register.html">Créer un compte</a>
                <a href="${basePath}/espace/profil.html">Mon espace</a>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© ${new Date().getFullYear()} Kmer News. Tous droits réservés.</span>
            <span>Fiabilité · Rapidité · Transparence · Accessibilité · Innovation · Proximité</span>
        </div>
    </footer>`;
}

function showFlash(type, message) {
    const zone = document.getElementById('flashZone');
    if (!zone) return;
    zone.innerHTML = `<div class="alert alert-${type}">${escapeHtml(message)}</div>`;
    zone.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
