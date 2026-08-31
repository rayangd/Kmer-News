
async function guardAdminPage() {
    const session = await KmerAPI.session();
    if (!session.logged_in) {
        window.location.href = '../login.html';
        return null;
    }
    if (session.user.role !== 'admin') {
        window.location.href = '../index.html';
        return null;
    }
    return session.user;
}

function renderAdminLayout(admin, activeMenu, pageTitle) {
    document.title = pageTitle + ' — Kmer News Admin';
    document.getElementById('admin-root').innerHTML = `
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="brand">
                <img src="../assets/img/logo.jpg" alt="Kmer News" class="site-logo-img">
                <strong>KMER NEWS<br><small style="font-weight:400;opacity:.7;">Administration</small></strong>
            </div>
            <nav>
                <a href="index.html" class="${activeMenu==='dashboard'?'active':''}">📊 Tableau de bord</a>
                <span class="section-label">Contenu</span>
                <a href="articles.html" class="${activeMenu==='articles'?'active':''}">📰 Articles</a>
                <a href="rubriques.html" class="${activeMenu==='rubriques'?'active':''}">🗂️ Rubriques</a>
                <a href="categories.html" class="${activeMenu==='categories'?'active':''}">🏷️ Catégories</a>
                <a href="comments.html" class="${activeMenu==='comments'?'active':''}">💬 Commentaires</a>
                <span class="section-label">Communauté</span>
                <a href="users.html" class="${activeMenu==='users'?'active':''}">👥 Utilisateurs</a>
                <a href="messages.html" class="${activeMenu==='messages'?'active':''}">✉️ Messages</a>
                <span class="section-label">Compte</span>
                <a href="../index.html">🌐 Voir le site</a>
                <a href="#" id="adminLogoutBtn">🚪 Déconnexion</a>
            </nav>
            <div class="sidebar-foot" style="font-size:0.78rem; color:#8893C4;">
                Connecté en tant que<br><strong style="color:#fff;">${escapeHtml(admin.prenom)} ${escapeHtml(admin.nom)}</strong>
            </div>
        </aside>
        <main class="admin-main">
            <div class="admin-topbar">
                <h1>${escapeHtml(pageTitle)}</h1>
                <span style="color:var(--text-muted); font-size:0.85rem;">${formatDateFr(new Date().toISOString())}</span>
            </div>
            <div class="admin-content">
                <div id="adminFlash"></div>
                <div id="adminBody"></div>
            </div>
        </main>
    </div>`;

    document.getElementById('adminLogoutBtn').addEventListener('click', async (e) => {
        e.preventDefault();
        await KmerAPI.postJson('logout.php');
        window.location.href = '../index.html';
    });
}

function adminFlash(type, message) {
    document.getElementById('adminFlash').innerHTML = `<div class="alert alert-${type}">${escapeHtml(message)}</div>`;
}
