/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: kmernews
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `kmernews`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `kmernews` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `kmernews`;

--
-- Table structure for table `article_likes`
--

DROP TABLE IF EXISTS `article_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_like` (`article_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `article_likes_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_likes`
--

LOCK TABLES `article_likes` WRITE;
/*!40000 ALTER TABLE `article_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sous_titre` varchar(255) DEFAULT NULL,
  `chapo` text DEFAULT NULL,
  `contenu` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `tag` varchar(60) DEFAULT NULL,
  `categorie_id` int(11) NOT NULL,
  `auteur_id` int(11) NOT NULL,
  `statut` enum('brouillon','publie') NOT NULL DEFAULT 'brouillon',
  `a_la_une` tinyint(1) NOT NULL DEFAULT 0,
  `vues` int(11) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `categorie_id` (`categorie_id`),
  KEY `auteur_id` (`auteur_id`),
  FULLTEXT KEY `ft_recherche` (`titre`,`chapo`,`contenu`),
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`auteur_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES
(1,'Le Ngondo officiellement inscrit au patrimoine de l\'UNESCO','le-ngondo-officiellement-inscrit-au-patrimoine-de-l-unesco',NULL,'Le rituel des peuples sawa reconnu comme patrimoine culturel immatériel de l\'humanité.','Le Ngondo, cérémonie traditionnelle des peuples sawa de Douala dédiée au culte de l\'eau, a été inscrit sur la liste représentative du patrimoine culturel immatériel de l\'humanité par l\'UNESCO le 4 décembre 2024.\nLe certificat officiel a été remis le 5 juillet 2025 à Douala, lors d\'une cérémonie au Centre culturel sawa, en présence du ministre des Arts et de la Culture Bidoung Mkpatt.\nCélébré chaque année en décembre sur les rives du Wouri, le Ngondo rassemble courses de pirogues, luttes traditionnelles et concours culturels. D\'autres pratiques camerounaises, comme le Mvet des Fang-Beti ou l\'Achu des Grassfields, sont actuellement à l\'étude pour une future inscription.',NULL,'Patrimoine',1,1,'publie',1,882,'2026-08-13 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(2,'Le Nguon de Foumban, grand rendez-vous du peuple bamoun','le-nguon-de-foumban-grand-rendez-vous-du-peuple-bamoun',NULL,'Une fête biennale qui rassemble musulmans et chrétiens autour d\'une même tradition.','Le Nguon (ou Ngouon) est une grande fête traditionnelle du peuple bamoun, célébrée tous les deux ans à Foumban, dans la région de l\'Ouest.\nPendant plusieurs jours, la cérémonie réunit danses traditionnelles, caravanes de femmes, foire commerciale et compétitions sportives, dans une ambiance qui rassemble aussi bien musulmans que chrétiens de la communauté bamoun.\nLa fête est aussi l\'occasion de visiter les sites touristiques emblématiques de Foumban, capitale historique du royaume bamoun et haut lieu de l\'artisanat camerounais.',NULL,'Patrimoine',1,1,'publie',0,601,'2026-08-20 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(3,'Le Cameroun, une mosaïque de plus de 200 langues et ethnies','le-cameroun-une-mosaique-de-plus-de-200-langues-et-ethnies',NULL,'Le pays est souvent surnommé \"l\'Afrique en miniature\" pour sa richesse culturelle.','Le Cameroun compte plus de 200 groupes ethniques et langues locales, une diversité qui lui vaut le surnom d\'\"Afrique en miniature\".\nCette mosaïque culturelle se retrouve dans les traditions, les musiques, les architectures et les cuisines qui varient fortement d\'une région à l\'autre du pays.\nCette richesse constitue à la fois un atout touristique et culturel majeur, et un défi pour la préservation de certaines pratiques et langues aujourd\'hui menacées.',NULL,'Diversité',1,1,'publie',0,288,'2026-08-17 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(4,'D\'autres traditions camerounaises en lice pour l\'UNESCO','d-autres-traditions-camerounaises-en-lice-pour-l-unesco',NULL,'Le Mvet, la Fantasia et l\'Achu à l\'étude pour une reconnaissance internationale.','Après le succès de l\'inscription du Ngondo, le ministère des Arts et de la Culture a annoncé poursuivre les démarches pour d\'autres pratiques culturelles camerounaises.\nParmi les dossiers à l\'étude : le Mvet, art oratoire et musical des Fang-Beti transmis par les conteurs ; la Fantasia et le Gouna, pratiques de la zone soudano-sahélienne ; et l\'Achu, tradition culinaire et festive des Grassfields de l\'Ouest.\nCes démarches visent à faire reconnaître internationalement la richesse du patrimoine immatériel camerounais, région par région.',NULL,'Patrimoine',1,1,'publie',0,406,'2026-08-17 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(5,'Yaoundé accueille un festival international de danse','yaounde-accueille-un-festival-international-de-danse',NULL,'Le festival Corps en Folie réunit création, innovation et échanges artistiques.','La capitale camerounaise a accueilli une nouvelle édition du festival international Corps en Folie, un rendez-vous consacré à la danse contemporaine et à la création chorégraphique.\nPendant plusieurs jours, artistes camerounais et internationaux se sont retrouvés à Yaoundé pour des spectacles, ateliers et rencontres autour de l\'innovation artistique.\nL\'événement s\'inscrit dans une dynamique plus large de valorisation des arts vivants au Cameroun, aux côtés du cinéma et des arts plastiques.',NULL,'Danse',1,1,'publie',0,621,'2026-08-11 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(6,'Les cases traditionnelles de l\'Ouest, un patrimoine architectural vivant','les-cases-traditionnelles-de-l-ouest-un-patrimoine-architectural-vivant',NULL,'Les chefferies bamiléké témoignent d\'un savoir-faire ancestral menacé par l\'urbanisation.','Dans la région de l\'Ouest, les chefferies traditionnelles bamiléké conservent une architecture typique faite de cases richement sculptées et de toits coniques en chaume.\nCes structures, encore habitées ou utilisées pour des cérémonies, témoignent d\'un savoir-faire transmis de génération en génération.\nFace à l\'urbanisation croissante, plusieurs initiatives locales cherchent aujourd\'hui à documenter et valoriser ce patrimoine architectural auprès des jeunes générations et des visiteurs.',NULL,'Architecture',1,1,'publie',0,576,'2026-08-17 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(7,'Manu Dibango, l\'homme qui a exporté le makossa dans le monde','manu-dibango-l-homme-qui-a-exporte-le-makossa-dans-le-monde',NULL,'\"Soul Makossa\" (1972) reste l\'un des plus grands succès internationaux de la musique camerounaise.','Le makossa, genre musical né dans la culture sawa à Douala, doit une grande partie de sa renommée mondiale au saxophoniste Manu Dibango.\nEn 1972, son titre \"Soul Makossa\" devient un succès international, popularisant ce style de musique dansante caractérisé par des lignes de basse marquées et des cuivres, proche du soukous congolais.\nDepuis, le makossa reste l\'un des piliers de la musique populaire camerounaise, aux côtés du bikutsi, de l\'assiko et du mangambeu.',NULL,'Héritage',2,1,'publie',0,288,'2026-08-22 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(8,'Le bikutsi, rythme emblématique du peuple beti','le-bikutsi-rythme-emblematique-du-peuple-beti',NULL,'Un genre né des cérémonies traditionnelles, aujourd\'hui pilier de la musique urbaine.','Le bikutsi est un genre musical traditionnel du peuple beti, originaire de la région de Yaoundé, caractérisé par un rythme intense à 6/8 temps.\nJoué historiquement lors des fêtes, mariages et funérailles, il s\'est progressivement imposé comme un rival populaire du makossa plus urbain.\nAujourd\'hui modernisé par de nombreux artistes, le bikutsi continue d\'occuper une place centrale dans la scène musicale camerounaise contemporaine.',NULL,'Traditions',2,1,'publie',0,863,'2026-08-21 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(9,'La nouvelle génération d\'artistes camerounais rayonne à l\'international','la-nouvelle-generation-d-artistes-camerounais-rayonne-a-l-international',NULL,'Entre R&B, afrobeats et sonorités traditionnelles, une scène urbaine en plein essor.','Une nouvelle génération d\'artistes camerounais gagne en visibilité sur la scène musicale internationale, en mêlant R&B, afrobeats et influences traditionnelles locales.\nCes artistes s\'appuient de plus en plus sur les plateformes de streaming pour toucher un public au-delà des frontières du pays, une tendance confirmée par la progression des écoutes de musique camerounaise ces dernières années.\nCette effervescence profite à l\'ensemble de l\'écosystème musical local, des studios d\'enregistrement aux organisateurs de concerts.',NULL,'Nouvelle scène',2,1,'publie',0,756,'2026-08-21 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(10,'Yamê, une voix façonnée entre le Cameroun et la France','yame-une-voix-faconnee-entre-le-cameroun-et-la-france',NULL,'L\'artiste explore une identité musicale à cheval sur deux héritages culturels.','L\'artiste camerounais Yamê construit une identité musicale singulière, puisant à la fois dans ses racines camerounaises et son parcours en France.\nSon travail illustre une tendance plus large chez les artistes de la diaspora, qui cherchent à faire dialoguer sonorités traditionnelles et influences contemporaines occidentales.\nCette double culture nourrit une scène musicale camerounaise de plus en plus visible à l\'international.',NULL,'Identité',2,1,'publie',0,622,'2026-08-18 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(11,'Hommage aux grandes voix disparues de la musique camerounaise','hommage-aux-grandes-voix-disparues-de-la-musique-camerounaise',NULL,'Wes Madiko, Francis Bebey, Lapiro de Mbanga : des artistes qui continuent d\'inspirer.','Chaque année, la Fête de la Musique est l\'occasion de rendre hommage aux artistes camerounais qui ont marqué l\'histoire du pays.\nParmi eux, Wes Madiko, connu pour le titre international \"Alané\" ; Francis Bebey, auteur-compositeur qui a mêlé tradition africaine et sonorités modernes ; ou encore Lapiro de Mbanga, figure de la chanson engagée restée associée à la défense des populations.\nLeurs œuvres continuent d\'être écoutées et de nourrir l\'identité musicale camerounaise auprès des nouvelles générations.',NULL,'Hommage',2,1,'publie',0,609,'2026-08-10 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(12,'Les Lionnes Indomptables éliminent le Nigéria et filent en demi-finale','les-lionnes-indomptables-eliminent-le-nigeria-et-filent-en-demi-finale',NULL,'Une victoire historique 1-0 qui ouvre la voie à la Coupe du Monde 2027.','Le 9 août 2026 à Casablanca, les Lionnes Indomptables du Cameroun ont créé la sensation en battant le Nigéria (1-0) en quart de finale de la CAN féminine, décrochant au passage un billet pour la Coupe du Monde féminine 2027 au Brésil.\nRepêchées après leur élimination en qualifications par l\'Algérie grâce à l\'élargissement du tournoi à 16 équipes, les joueuses de la sélectionneuse Valentine Nguélé ont réalisé l\'exploit face aux Super Falcons, championnes d\'Afrique en titre.\nCette victoire marque une étape importante pour le football féminin camerounais, dans une compétition marquée par une préparation mouvementée.',NULL,'Football féminin',3,1,'publie',1,407,'2026-08-22 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(13,'Route vers la CAN 2027 : les Lions Indomptables en reconquête','route-vers-la-can-2027-les-lions-indomptables-en-reconquete',NULL,'Les éliminatoires débuteront en septembre 2026, avec une génération montante à suivre.','Après une CAN 2025 qui n\'a pas permis au Cameroun d\'aller au bout de ses ambitions, les Lions Indomptables se tournent déjà vers la CAN 2027, dont les éliminatoires débuteront en septembre 2026 selon la Confédération Africaine de Football.\nPlusieurs jeunes talents évoluant en Europe attirent l\'attention, comme Carlos Baleba (Brighton), auteur d\'un but élu \"but du mois\" en Premier League en mai 2025, ou Karl Etta Eyong (Levante).\nRoger Milla, légende du football camerounais et ambassadeur de la sélection, continue d\'encourager cette nouvelle génération à porter haut les couleurs du pays, quintuple vainqueur de la CAN.',NULL,'Football',3,1,'publie',0,204,'2026-08-20 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(14,'Éliminatoires Mondial 2026 : le Cameroun vise une 9e qualification historique','eliminatoires-mondial-2026-le-cameroun-vise-une-9e-qualification-historique',NULL,'Les Lions Indomptables occupent une place de choix dans leur groupe.','Dans le cadre des éliminatoires de la Coupe du Monde 2026, dont la phase finale se déroulera aux États-Unis, au Canada et au Mexique, les Lions Indomptables visent une 9e qualification historique en Coupe du Monde.\nLa sélection, emmenée par son capitaine André Onana, a notamment dominé l\'Île Maurice (2-0) lors des éliminatoires, confortant sa position dans son groupe.\nSeul le premier de chaque groupe se qualifie directement, les meilleurs deuxièmes devant passer par un tour de barrage africain.',NULL,'Football',3,1,'publie',0,733,'2026-08-13 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(15,'La FECAFOOT modernise la préparation des Lions Indomptables','la-fecafoot-modernise-la-preparation-des-lions-indomptables',NULL,'Séances nocturnes et suivi individualisé pour se rapprocher des conditions de match.','La Fédération Camerounaise de Football (FECAFOOT) a mis en place un programme de préparation renouvelé pour les Lions Indomptables, incluant des séances d\'entraînement nocturnes au Stade Militaire de Yaoundé.\nL\'objectif est de rapprocher les conditions d\'entraînement de celles des matchs officiels, avec un protocole individualisé pour chaque joueur.\nCette démarche s\'appuie sur un suivi statistique continu et une planification précise autour des fenêtres internationales FIFA.',NULL,'Préparation',3,1,'publie',0,351,'2026-08-16 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(16,'CAN féminine 2026 : un parcours semé d\'embûches pour les Lionnes','can-feminine-2026-un-parcours-seme-d-embuches-pour-les-lionnes',NULL,'Un match nul accroché face au Cap-Vert avant le exploit en quart de finale.','Avant leur exploit face au Nigéria, les Lionnes Indomptables avaient connu un parcours de poule compliqué lors de la CAN féminine 2026, concédant notamment un match nul (1-1) face au Cap-Vert.\nMenées puis rejointes en fin de rencontre, les joueuses camerounaises avaient dû s\'employer pour ne pas s\'incliner face aux \"Requins Bleus\" cap-verdiens.\nCe parcours mouvementé rend d\'autant plus marquante la qualification obtenue ensuite face aux Super Falcons du Nigéria.',NULL,'Football féminin',3,1,'publie',0,989,'2026-08-12 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(17,'Silicon Mountain, le pôle technologique du Cameroun anglophone','silicon-mountain-le-pole-technologique-du-cameroun-anglophone',NULL,'Un nom imaginé en 2013, devenu la référence de la tech camerounaise.','L\'expression \"Silicon Mountain\" a été popularisée en 2013 par l\'entrepreneure camerounaise Rebecca Enonchong lors d\'un BarCamp à Buea, pour désigner l\'écosystème technologique naissant autour du Mont Cameroun.\nCe pôle, qui s\'étend historiquement de Buea à Limbe, dans le Sud-Ouest du pays, a vu naître de nombreuses startups et incubateurs au fil des années.\nMalgré les difficultés traversées par la région, Silicon Mountain reste une référence symbolique pour l\'entrepreneuriat technologique camerounais.',NULL,'Écosystème',4,1,'publie',1,869,'2026-08-19 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(18,'ActivSpaces, le pionnier des incubateurs technologiques au Cameroun','activspaces-le-pionnier-des-incubateurs-technologiques-au-cameroun',NULL,'Aujourd\'hui présent à Buea, Douala et Bangangté.','ActivSpaces est considéré comme le tout premier incubateur technologique du Cameroun, à l\'origine du développement de Silicon Mountain autour de Buea.\nAu fil des années, la structure a élargi sa présence à Douala et Bangangté, tout en réorientant une partie de ses activités vers la formation des jeunes aux métiers du numérique.\nPlusieurs startups camerounaises aujourd\'hui reconnues, notamment dans l\'agritech et l\'immobilier en ligne, sont nées ou sont passées par cet incubateur.',NULL,'Incubateur',4,1,'publie',0,477,'2026-08-22 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(19,'Le mobile money, pilier de l\'inclusion financière au Cameroun','le-mobile-money-pilier-de-l-inclusion-financiere-au-cameroun',NULL,'Le Cameroun concentre à lui seul 60% des flux de paiement digital de la zone CEMAC.','Porté par le duopole Orange Money et MTN MoMo, le mobile money s\'est imposé comme le principal outil d\'inclusion financière au Cameroun.\nLe pays représenterait à lui seul une large majorité des flux de paiement digital de la zone CEMAC, avec plusieurs millions d\'utilisateurs actifs et des milliards de FCFA de transactions chaque année.\nCe mode de paiement, moins coûteux que la carte bancaire classique, permet de toucher une population encore largement non bancarisée, notamment en zone rurale.',NULL,'Mobile Money',4,1,'publie',0,460,'2026-08-11 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(20,'L\'écart entre hommes et femmes se réduit sur le mobile money','l-ecart-entre-hommes-et-femmes-se-reduit-sur-le-mobile-money',NULL,'La part des utilisatrices est passée d\'environ 29% à 44% en quelques années.','Le profil des utilisateurs du mobile money au Cameroun évolue progressivement vers plus de parité entre hommes et femmes.\nAlors que les femmes ne représentaient qu\'une faible part des utilisateurs il y a quelques années, leur proportion a nettement progressé, portée par des campagnes d\'inclusion financière ciblées et une présence accrue d\'agents mobile money féminines.\nCette évolution est particulièrement suivie par les acteurs du secteur, qui y voient un indicateur clé de l\'inclusion économique des femmes camerounaises.',NULL,'Inclusion',4,1,'publie',0,627,'2026-08-20 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(21,'Orange Cameroun transforme le smartphone en télévision de poche','orange-cameroun-transforme-le-smartphone-en-television-de-poche',NULL,'Le service Max It TV mise sur le streaming mobile pour toucher un public connecté.','L\'opérateur Orange Cameroun a lancé Max It TV, un service permettant de regarder la télévision en streaming directement depuis un smartphone.\nCette offre s\'inscrit dans une stratégie plus large des opérateurs télécoms camerounais visant à valoriser la data mobile et à répondre à une consommation de contenus de plus en plus tournée vers le mobile.\nElle illustre la diversification croissante des services numériques proposés aux abonnés camerounais, au-delà du seul accès internet.',NULL,'Innovation',4,1,'publie',0,797,'2026-08-14 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(22,'Silicon Mountain face aux défis de la connectivité','silicon-mountain-face-aux-defis-de-la-connectivite',NULL,'L\'écosystème technologique du Sud-Ouest a dû composer avec des coupures internet.','L\'écosystème de Silicon Mountain, dans les régions anglophones du Cameroun, a connu des périodes de forte perturbation liées à des coupures de connexion internet, affectant directement l\'activité des startups locales dépendantes du numérique.\nPlusieurs entrepreneurs ont dû adapter leurs activités, certains se relocalisant temporairement vers Douala pour poursuivre leur travail.\nCes épisodes ont rappelé à quel point une connectivité stable est une condition essentielle au développement d\'un écosystème technologique durable.',NULL,'Défis',4,1,'publie',0,486,'2026-08-21 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(23,'La Couverture Santé Universelle franchit le cap des 9 millions de bénéficiaires','la-couverture-sante-universelle-franchit-le-cap-des-9-millions-de-beneficiaires',NULL,'Lancée en 2023, la réforme veut garantir l\'accès aux soins essentiels pour tous.','Lancée le 12 avril 2023 à Mandjou, dans la région de l\'Est, la Couverture Santé Universelle (CSU) a permis à plus de 9 millions de Camerounais d\'accéder à des prestations de santé, selon le ministère de la Santé publique.\nLa réforme prévoit notamment la gratuité de certains soins de maternité, la prise en charge du VIH/Sida et du paludisme, ainsi que l\'extension des centres de dialyse à travers le pays.\nLe gouvernement, sous l\'impulsion du ministre Manaouda Malachie, affiche l\'ambition de poursuivre l\'extension de cette couverture dans les années à venir, avec un objectif affiché de 6 millions de personnes pré-enrôlées à moyen terme, déjà largement dépassé.',NULL,'Santé',5,1,'publie',1,125,'2026-08-18 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(24,'Un nouveau programme pour l\'emploi des jeunes dans l\'Extrême-Nord','un-nouveau-programme-pour-l-emploi-des-jeunes-dans-l-extreme-nord',NULL,'Le CAP2E ambitionne de créer 5 000 emplois d\'ici 2029.','Le gouvernement camerounais et la Banque africaine de développement ont lancé le 20 février 2026 à Maroua le programme CAP2E, consacré au renforcement des capacités et de l\'employabilité dans la région de l\'Extrême-Nord.\nCe programme ambitionne un taux d\'insertion professionnelle de 80% pour les diplômés formés d\'ici 2029, ainsi que la création de 5 000 emplois, dont une large part réservée aux jeunes et aux femmes.\nIl s\'inscrit dans une stratégie plus globale de développement du capital humain et des infrastructures sociales de base dans le septentrion camerounais.',NULL,'Emploi',5,1,'publie',0,411,'2026-08-20 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(25,'L\'autonomisation économique des femmes progresse au Cameroun','l-autonomisation-economique-des-femmes-progresse-au-cameroun',NULL,'Programmes dédiés et initiatives bancaires se multiplient pour soutenir les entrepreneures.','Plusieurs initiatives se développent au Cameroun pour soutenir l\'entrepreneuriat féminin, à l\'image du Programme Wonder qui met en lumière des femmes entrepreneures inspirantes à travers le pays.\nDes acteurs bancaires proposent désormais des produits dédiés aux femmes chefs d\'entreprise, bien que l\'accès aux garanties bancaires classiques reste un obstacle pour de nombreuses candidates à l\'entrepreneuriat.\nCes efforts s\'inscrivent dans une dynamique régionale plus large de soutien à l\'autonomisation économique des femmes en Afrique centrale.',NULL,'Entrepreneuriat féminin',5,1,'publie',0,741,'2026-08-16 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(26,'Vers une extension de la protection sociale au Cameroun','vers-une-extension-de-la-protection-sociale-au-cameroun',NULL,'Un rapport international pointe le rôle clé de la fiscalité pour financer la réforme.','Un rapport consacré au financement de la protection sociale au Cameroun souligne l\'importance des recettes fiscales générales pour étendre durablement la couverture sociale du pays.\nLe document met en avant un contexte marqué par une forte informalité de l\'économie et une pression fiscale encore limitée, qui freinent le financement des dispositifs de protection sociale existants.\nParmi les pistes évoquées : une meilleure formalisation de l\'économie et une réforme fiscale ciblée pour accompagner l\'extension de la Couverture Santé Universelle.',NULL,'Protection sociale',5,1,'publie',0,99,'2026-08-16 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(27,'Le mobile money, un outil d\'émancipation économique pour les femmes','le-mobile-money-un-outil-d-emancipation-economique-pour-les-femmes',NULL,'La progression des utilisatrices reflète des efforts d\'inclusion financière ciblés.','Au-delà de son rôle économique, le développement du mobile money au Cameroun est de plus en plus perçu comme un levier d\'inclusion financière pour les femmes, notamment en zone rurale.\nLa progression du nombre d\'utilisatrices ces dernières années traduit les effets de campagnes de sensibilisation spécifiques et d\'un réseau d\'agents mobile money davantage féminisé.\nCes évolutions s\'inscrivent dans les objectifs plus larges de réduction des inégalités économiques entre hommes et femmes au Cameroun.',NULL,'Inclusion financière',5,1,'publie',0,666,'2026-08-14 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54'),
(28,'Le défi de l\'insertion professionnelle des jeunes diplômés','le-defi-de-l-insertion-professionnelle-des-jeunes-diplomes',NULL,'Plusieurs programmes visent à mieux connecter formation et marché de l\'emploi.','Face au défi de l\'insertion professionnelle des jeunes diplômés, plusieurs programmes publics et partenariats internationaux se multiplient au Cameroun pour renforcer l\'adéquation entre formation et marché du travail.\nCes initiatives combinent souvent renforcement des compétences techniques, accompagnement à l\'entrepreneuriat et amélioration des infrastructures sociales de base dans les régions concernées.\nL\'enjeu est particulièrement suivi dans les régions les plus touchées par le chômage des jeunes, où ces programmes espèrent créer des milliers d\'emplois durables dans les prochaines années.',NULL,'Éducation',5,1,'publie',0,436,'2026-08-14 23:13:54','2026-08-22 23:13:54','2026-08-22 23:13:54');
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `couleur` varchar(7) NOT NULL DEFAULT '#0048D9',
  `icone` varchar(50) DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'Culture','culture','Patrimoine, arts, traditions','#7C3AED','culture',1),
(2,'Musique','musique','Makossa, bikutsi, afrobeats','#DB1E5B','musique',2),
(3,'Sport','sport','Lions Indomptables et au-delà','#0EA88A','sport',3),
(4,'High-Tech','high-tech','Silicon Mountain et innovation locale','#F5A623','hightech',4),
(5,'Société','societe','Santé, éducation, inclusion et solidarité','#DC2626','societe',5);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `statut` enum('en_attente','approuve','rejete') NOT NULL DEFAULT 'approuve',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(150) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `tentative_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `sujet` varchar(200) DEFAULT NULL,
  `contenu` text NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(80) NOT NULL,
  `nom` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `statut` enum('actif','suspendu') NOT NULL DEFAULT 'actif',
  `derniere_connexion` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Rayan','HD','rayanhd168@gmail.com','$2y$10$/OiIYxUSZSxLgNxFg6kR3OYcGAZLgKc8ndmOGNLOPPFwNX5tjh50K','admin',NULL,'actif','2026-08-23 05:50:17','2026-08-22 23:13:54'),
(2,'Ghislain','Tagne','Ghislain@gmail.com','$2y$10$/OiIYxUSZSxLgNxFg6kR3OYcGAZLgKc8ndmOGNLOPPFwNX5tjh50K','admin',NULL,'actif',NULL,'2026-08-22 23:13:54'),
(3,'Aïcha','Ngo Bell','aicha@exemple.cm','$2y$10$LTQBCyqICWYWS1H/T8lf7eE4LpVwB2YknlaWOVjXYyb08y4AeKGkG','user',NULL,'actif','2026-08-23 05:49:56','2026-08-22 23:13:54');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-23  6:27:26
