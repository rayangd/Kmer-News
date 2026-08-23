/* ============================================================
   KMER NEWS — home.js
   ============================================================ */

function renderHeroAndAuth(session) {
    const heroActions = document.getElementById('heroActions');
    const authSection = document.getElementById('authSection');

    if (session.logged_in) {
        heroActions.innerHTML = `<a href="#rubriques" class="btn btn-primary">Découvrir les actualités</a>`;
        authSection.innerHTML = '';
    } else {
        heroActions.innerHTML = `
            <a href="register.html" class="btn btn-primary">Créer un compte gratuit</a>
            <a href="login.html" class="btn btn-outline">J'ai déjà un compte</a>`;
        authSection.innerHTML = `
        <section class="auth-section">
            <div class="container">
                <span class="pill-tag">Compte Kmer News</span>
                <h2>Rejoignez la rédaction citoyenne</h2>
                <p>Créez un compte pour personnaliser vos rubriques, recevoir l'édition du soir et commenter les articles.</p>
                <div class="auth-grid">
                    <div class="auth-card login">
                        <span class="eyebrow">Connexion</span>
                        <h3>Content de vous revoir</h3>
                        <p class="sub">Accédez à votre fil personnalisé.</p>
                        <form id="quickLoginForm">
                            <div class="field"><label>Adresse e-mail</label><input type="email" name="email" placeholder="vous@exemple.cm" required></div>
                            <div class="field"><label>Mot de passe</label><input type="password" name="password" placeholder="••••••••" required></div>
                            <div id="quickLoginError"></div>
                            <button type="submit" class="btn btn-dark btn-block">Se connecter</button>
                        </form>
                    </div>
                    <div class="auth-card register">
                        <span class="eyebrow">Inscription</span>
                        <h3>Créer votre compte</h3>
                        <p class="sub">Gratuit, en moins d'une minute.</p>
                        <form id="quickRegisterForm">
                            <div class="field field-row">
                                <div><label>Prénom</label><input type="text" name="prenom" placeholder="Aïcha" required></div>
                                <div><label>Nom</label><input type="text" name="nom" placeholder="Ngo Bell" required></div>
                            </div>
                            <div class="field"><label>Adresse e-mail</label><input type="email" name="email" placeholder="vous@exemple.cm" required></div>
                            <div class="field"><label>Mot de passe</label><input type="password" name="password" placeholder="8 caractères minimum" minlength="8" required></div>
                            <div id="quickRegisterError"></div>
                            <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>`;

        document.getElementById('quickLoginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            const r = await KmerAPI.postJson('login.php', { email: fd.get('email'), password: fd.get('password') });
            if (r.data.success) {
                window.location.href = r.data.role === 'admin' ? 'admin/index.html' : 'espace/profil.html';
            } else {
                document.getElementById('quickLoginError').innerHTML = `<div class="alert alert-error">${escapeHtml(r.data.message)}</div>`;
            }
        });

        document.getElementById('quickRegisterForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            const r = await KmerAPI.postJson('register.php', {
                prenom: fd.get('prenom'), nom: fd.get('nom'), email: fd.get('email'), password: fd.get('password')
            });
            if (r.data.success) {
                window.location.href = 'espace/profil.html';
            } else {
                document.getElementById('quickRegisterError').innerHTML = `<div class="alert alert-error">${escapeHtml(r.data.message)}</div>`;
            }
        });
    }
}

async function loadHomePage() {
    const container = document.getElementById('rubriques');
    try {
        const [data, popularData] = await Promise.all([
            KmerAPI.get('articles.php'),
            KmerAPI.get('articles.php?mode=popular&limit=5'),
        ]);
        if (!data.success) throw new Error('Erreur API');

        let html = '';

        // --- Section tendances (articles les plus vus) ---
        if (popularData.success && popularData.articles.length > 0) {
            html += `<section class="trending-section"><div class="container">
                <div class="rubrique-head"><div class="left"><div class="rubrique-title">
                    <h2>🔥 Tendances du moment</h2><p>Les articles les plus lus cette semaine</p>
                </div></div></div>
                <div class="trending-grid">` +
                popularData.articles.map((a, i) => `
                    <a class="trending-item" href="article.html?slug=${encodeURIComponent(a.slug)}">
                        <span class="trending-rank">${i + 1}</span>
                        <img class="trending-thumb" src="${articleImage(a, 100, 100)}" alt="" loading="lazy">
                        <div>
                            <span class="trending-cat" style="color:${a.categorie_couleur}">${escapeHtml(a.categorie_nom)}</span>
                            <h4>${escapeHtml(excerpt(a.titre, 60))}</h4>
                            <span class="trending-views">👁 ${a.vues} vues</span>
                        </div>
                    </a>`).join('') +
                `</div></div></section>`;
        }

        data.grouped.forEach((group, index) => {
            const cat = group.category;
            const cls = cat.slug === 'high-tech' ? 'hightech' : cat.slug;
            const num = String(index + 1).padStart(2, '0');

            html += `<section class="rubrique-section ${cls}">`;

            const bannerUrl = rubriqueBannerUrl(cat.slug, '.');
            if (bannerUrl) {
                html += `<div class="rubrique-banner-strip" style="background-image:url('${bannerUrl}');">
                    <div class="strip-overlay" style="background:linear-gradient(90deg, ${cat.couleur}F2 0%, ${cat.couleur}CC 30%, rgba(20,16,58,0.65) 100%);"></div>
                    <div class="strip-content">
                        <span class="strip-icon">${rubriqueIcon(cat.slug)}</span>
                        <div><h2 style="color:#fff;">${escapeHtml(cat.nom)}</h2><p>${escapeHtml(cat.description || '')}</p></div>
                    </div>
                </div>`;
            }

            html += `<div class="container">`;
            if (!bannerUrl) {
                html += `<div class="rubrique-head">
                    <div class="left">
                        <span class="rubrique-num" style="color:${cat.couleur}">${num}</span>
                        <div class="rubrique-title"><h2>${escapeHtml(cat.nom)}</h2><p>${escapeHtml(cat.description || '')}</p></div>
                    </div>
                    <a class="voir-tout" href="category.html?slug=${encodeURIComponent(cat.slug)}">Tout voir →</a>
                </div>`;
            } else {
                html += `<div class="rubrique-head" style="padding-top:22px;">
                    <div class="left"></div>
                    <a class="voir-tout" href="category.html?slug=${encodeURIComponent(cat.slug)}">Tout voir →</a>
                </div>`;
            }

            if (group.articles.length === 0) {
                html += `<div class="empty-state">Aucun article publié pour le moment dans cette rubrique.</div>`;
            } else {
                html += `<div class="card-grid">` + group.articles.map(a => `
                    <a class="article-card card-${cls}" href="article.html?slug=${encodeURIComponent(a.slug)}">
                        <img class="card-thumb" src="${articleImage(a, 400, 200)}" alt="" loading="lazy">
                        <div>
                            <div class="tag">${escapeHtml(a.tag || cat.nom)}</div>
                            <h3>${escapeHtml(a.titre)}</h3>
                            <p class="excerpt">${escapeHtml(excerpt(a.chapo || a.contenu, 90))}</p>
                        </div>
                        <div class="meta"><span>${timeAgo(a.published_at)}</span><span>👁 ${a.vues}</span></div>
                    </a>`).join('') + `</div>`;
            }
            html += `</div>`;
            if (index < data.grouped.length - 1) html += `<hr class="rubrique-sep">`;
            html += `</section>`;
        });

        container.innerHTML = html;
    } catch (e) {
        container.innerHTML = `<div class="container" style="padding:40px 24px;"><div class="empty-state">Impossible de charger les actualités. Vérifiez que le serveur et la base de données sont bien démarrés.</div></div>`;
        console.error(e);
    }
}
