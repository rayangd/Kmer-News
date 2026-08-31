-- ============================================================
-- KMER NEWS - Schéma de base de données
-- Journal numérique interactif
-- ============================================================

CREATE DATABASE IF NOT EXISTS kmernews CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kmernews;

-- ------------------------------------------------------------
-- Table : users (visiteurs inscrits + administrateurs)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(80) NOT NULL,
    nom VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT NULL,
    statut ENUM('actif', 'suspendu') NOT NULL DEFAULT 'actif',
    derniere_connexion DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : categories (les 4 rubriques + extensible)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(60) NOT NULL UNIQUE,
    slug VARCHAR(60) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    couleur VARCHAR(7) NOT NULL DEFAULT '#0048D9',
    icone VARCHAR(50) DEFAULT NULL,
    ordre INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : articles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    sous_titre VARCHAR(255) DEFAULT NULL,
    chapo TEXT DEFAULT NULL,
    contenu LONGTEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    tag VARCHAR(60) DEFAULT NULL,
    categorie_id INT NOT NULL,
    auteur_id INT NOT NULL,
    statut ENUM('brouillon', 'publie') NOT NULL DEFAULT 'brouillon',
    a_la_une TINYINT(1) NOT NULL DEFAULT 0,
    vues INT NOT NULL DEFAULT 0,
    published_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE RESTRICT,
    FULLTEXT KEY ft_recherche (titre, chapo, contenu)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : comments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    contenu TEXT NOT NULL,
    statut ENUM('en_attente', 'approuve', 'rejete') NOT NULL DEFAULT 'approuve',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : article_likes (articles populaires)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS article_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_like (article_id, user_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : messages (formulaire de contact)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    sujet VARCHAR(200) DEFAULT NULL,
    contenu TEXT NOT NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table : login_attempts (anti brute-force)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifiant VARCHAR(150) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    tentative_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DONNÉES INITIALES
-- ============================================================

INSERT INTO categories (nom, slug, description, couleur, icone, ordre) VALUES
('Culture', 'culture', 'Patrimoine, arts, traditions', '#7C3AED', 'culture', 1),
('Musique', 'musique', 'Makossa, bikutsi, afrobeats', '#DB1E5B', 'musique', 2),
('Sport', 'sport', 'Lions Indomptables et au-delà', '#0EA88A', 'sport', 3),
('High-Tech', 'high-tech', 'Silicon Mountain et innovation locale', '#F5A623', 'hightech', 4),
('Société', 'societe', 'Santé, éducation, inclusion et solidarité', '#DC2626', 'societe', 5);

-- Comptes administrateurs : mot de passe par défaut Admin@2026 pour les deux
-- (hash bcrypt réel généré avec password_hash PHP)
INSERT INTO users (prenom, nom, email, password_hash, role) VALUES
('Rayan', 'HD', 'rayanhd168@gmail.com', '$2y$10$/OiIYxUSZSxLgNxFg6kR3OYcGAZLgKc8ndmOGNLOPPFwNX5tjh50K', 'admin'),
('Ghislain', 'Tagne', 'Ghislain@gmail.com', '$2y$10$/OiIYxUSZSxLgNxFg6kR3OYcGAZLgKc8ndmOGNLOPPFwNX5tjh50K', 'admin');
