# KMER NEWS — Journal numérique interactif
### Architecture découplée : HTML + CSS + JavaScript (front) / PHP + MySQL (API backend)
### 5 rubriques : Culture · Musique · Sport · High-Tech · **Société**

Le contenu de démonstration (28 articles) est basé sur des **faits réels et vérifiés** concernant
le Cameroun (inscription du Ngondo à l'UNESCO, qualification des Lionnes Indomptables pour la Coupe
du Monde 2027, écosystème Silicon Mountain, Couverture Santé Universelle...), reformulés pour le site.

Chaque rubrique (sauf Société) affiche une **vraie photo en bannière** sur l'accueil, la page
catégorie et l'admin, avec un dégradé aux couleurs de la rubrique par-dessus pour la lisibilité.

Testé et validé en conditions réelles (Apache + PHP 8.3 + MariaDB 10.11, exactement comme XAMPP) :
pages HTML, appels API JSON, connexion admin/utilisateur, likes, commentaires, proposition d'articles,
CRUD complet — tout a été vérifié avec de vraies captures d'écran, pas juste en ligne de commande.

---

## 🔑 Comptes administrateurs

| Nom | E-mail | Mot de passe |
|-----|--------|---------------|
| Rayan | `rayanhd168@gmail.com` | `Admin@2026` |
| Ghislain | `Ghislain@gmail.com` | `Admin@2026` |

**Compte lecteur de démonstration :** `aicha@exemple.cm` / `Lecteur@2026`

⚠️ Change ces mots de passe avant la mise en production réelle.

---

## ✨ Nouveautés de cette version

- **5ème rubrique "Société"** (santé, éducation, inclusion, solidarité) — couleur rouge dédiée
- **Bannières photo réelles** par rubrique (Culture, Musique, Sport, High-Tech), hébergées dans le
  site (`assets/img/rubriques/`) — aucune dépendance à un service externe, fonctionne hors ligne
- **Widget profil** : icône cliquable dans le header après connexion (nom, e-mail, avatar), avec accès
  rapide à "Mon espace", "Proposer un article" et "Déconnexion"
- **Proposition d'article par les lecteurs** : un utilisateur connecté peut rédiger un article
  (titre, rubrique, chapô, contenu, image) et l'envoyer à la rédaction. Il est enregistré en
  "brouillon" et apparaît dans l'admin avec un badge **"Proposition"**, en attente de relecture.
  Le tableau de bord admin signale ces propositions en attente.

---

## 🧩 Comment ça marche

Le HTML est statique — le navigateur charge directement les fichiers `.html`. Le **JavaScript**
(`fetch`) va chercher les données auprès du **PHP**, qui répond en **JSON** (API REST) après avoir
interrogé **MySQL**.

```
Navigateur (HTML + CSS) → JavaScript (fetch) → PHP (api/*.php, JSON) → MySQL
```

---

## 📁 Structure du projet

```
kmernews2/
├── index.html, login.html, register.html,      → Pages HTML publiques
│   article.html, category.html, search.html,
│   contact.html, apropos.html
├── espace/
│   ├── profil.html              → Espace personnel utilisateur
│   └── nouvel_article.html      → Formulaire de proposition d'article
├── admin/*.html                  → Espace administrateur (9 pages, dont rubriques.html)
│
├── api/                          → Backend PHP (répond en JSON)
│   ├── bootstrap.php, session.php, login.php, register.php, logout.php
│   ├── categories.php, articles.php, article.php, search.php
│   ├── like.php, comment.php, contact.php, profile.php, propose_article.php
│   └── admin/                    → API réservée aux administrateurs
│       ├── dashboard.php, articles.php, categories.php, rubriques.php,
│       ├── users.php, comments.php, messages.php
│
├── assets/
│   ├── css/style.css, admin.css
│   ├── js/api.js, layout.js, admin-layout.js, home.js, main.js
│   └── img/
│       ├── rubriques/            → Vraies photos par rubrique (culture, musique, sport, hightech)
│       └── uploads/               → Images uploadées par l'admin/les lecteurs (protégé)
│
├── config/                        → Connexion MySQL (protégé, non accessible depuis le web)
├── includes/                      → Fonctions PHP réutilisées par l'API (protégé)
└── database/
    ├── schema.sql                     → Structure vide + 5 rubriques + 2 comptes admin
    └── schema_avec_donnees_demo.sql   → Structure + 28 articles de démo (RECOMMANDÉ)
```

---

## 🚀 Déploiement (XAMPP)

1. Installe [XAMPP](https://www.apachefriends.org/).
2. Copie le dossier `kmernews2` dans `htdocs/` (Windows : `C:\xampp\htdocs\kmernews2`).
3. Démarre **Apache** et **MySQL** dans le panneau XAMPP (les deux doivent devenir verts).
4. Va sur `http://localhost/phpmyadmin`, crée une base nommée `kmernews`, puis importe
   **`database/schema_avec_donnees_demo.sql`** (onglet Importer).
5. Ouvre `http://localhost/kmernews2/index.html` — **jamais** en double-cliquant sur le fichier
   (l'URL ne doit JAMAIS commencer par `C:/Users/...` ou `file:///`).

### Hébergement en ligne réel
1. Crée une base MySQL chez ton hébergeur, importe le même fichier SQL.
2. Transfère tout le dossier en FTP dans `public_html/`.
3. Modifie `config/config.php` avec les vrais identifiants et l'URL du site.
4. Passe `APP_DEBUG` à `false`.
5. Si le dossier n'est pas `/kmernews2/`, adapte la ligne `window.KMER_API_BASE = 'api';`
   (ou `'../api'`) présente en haut de chaque page HTML.

---

## 🔒 Sécurité

- Mots de passe hashés (bcrypt) — jamais stockés en clair
- Requêtes SQL 100% préparées (PDO) → anti injection SQL
- **CSRF** sur chaque appel API sensible (`X-CSRF-Token`), vérifié côté PHP
- Anti brute-force sur la connexion (5 tentatives / 15 min)
- Upload d'image validé par type MIME réel + taille limitée + exécution de scripts désactivée
- Toutes les sorties échappées côté JS (`escapeHtml()`) → anti-XSS
- Chaque route `api/admin/*.php` vérifie le rôle admin **côté serveur**, jamais seulement côté JS

---

## 👤 Connexion et redirection automatique du rôle

`api/login.php` vérifie le rôle en base et renvoie `role: "admin"` ou `role: "user"`. Le JavaScript
redirige alors vers `admin/index.html` ou `espace/profil.html`. Un utilisateur simple qui tente
d'accéder directement à une URL `admin/*.html` est intercepté par `guardAdminPage()` et renvoyé vers
l'accueil — et même en modifiant le JavaScript, l'API `api/admin/*.php` refuserait quand même (403).

---

## 🛠️ En cas de bug pendant ta présentation

- Vérifie que `mbstring` est activé côté PHP (nécessaire pour les caractères accentués).
- Si "Erreur de connexion à la base de données" : vérifie que MySQL tourne et que
  `config/config.php` a les bons identifiants.
- Si une page blanche ou une erreur 500 apparaît : vérifie qu'aucun `.htaccess` n'a été modifié
  par erreur (les blocs `<Directory>` sont interdits dans un `.htaccess`, seulement dans la config
  principale d'Apache).
- Ouvre la console du navigateur (F12) pour voir les erreurs JS ou d'appel API en détail.

Bonne présentation ! 🇨🇲
