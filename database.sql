-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 15 mai 2025 à 06:24
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `easystock`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(10, 'Accessoires'),
(11, 'Alimentation'),
(8, 'Automobile'),
(5, 'Beauté & Santé'),
(2, 'Électronique'),
(7, 'Jeux vidéo'),
(9, 'Livres'),
(4, 'Maison & Jardin'),
(6, 'Sports & Loisirs'),
(3, 'Vêtements');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_commande` datetime NOT NULL,
  `etat` varchar(255) NOT NULL DEFAULT 'Non pris en charge'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `user_id`, `date_commande`, `etat`) VALUES
(2, 3, '2025-05-11 07:59:19', 'Annulée'),
(3, 3, '2025-05-12 09:20:26', 'Livrée'),
(4, 3, '2025-05-15 04:01:13', 'Livrée'),
(5, 3, '2025-05-15 04:10:11', 'Non pris en charge');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

CREATE TABLE `fournisseurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `adresse` text NOT NULL,
  `ville` varchar(255) NOT NULL,
  `pays` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id`, `nom`, `tel`, `adresse`, `ville`, `pays`, `email`) VALUES
(2, 'SénElectro SARL', '77 123 4567', 'Rue 12, Zone Industrielle', 'Dakar', 'Sénégal', 'contact@senelectro.sn'),
(3, 'Fashion Empire', '+33 1 45 67 89 00', '12 Rue Lafayette', 'Paris', 'France', 'contact@fashionempire.fr'),
(4, 'Casa Verde', '+34 91 234 5678', 'Calle del Sol 45', 'Madrid', 'Espagne', 'ventas@casaverde.es'),
(5, 'BelleVie Cosmetics', '70 456 7890', 'Rue des HLM', 'Ziguinchor', 'Sénégal', 'support@bellevie.sn'),
(6, 'ActiveLife GmbH', '+49 30 123456', 'Sportweg 21', 'Berlin', 'Allemagne', 'info@activelife.de'),
(7, 'NeoGaming Inc.', '+1 212-555-6789', '5th Avenue, Suite 400', 'New York', 'États-Unis', 'support@neogaming.com'),
(8, 'AutoPlus Sénégal', '76 789 0123', 'Km 5, Route de Ouakam', 'Dakar', 'Sénégal', 'vente@autoplus.sn'),
(9, 'Librairie Baobab', '70 890 1234', 'Rue de la Culture', 'Kaolack', 'Sénégal', 'contact@baobabbooks.sn'),
(10, 'TokyoTrends Co.', '+81 3-1234-5678', 'Shibuya-ku, 1-2-3', 'Tokyo', 'Japon', 'info@tokyotrends.jp'),
(11, 'Teranga Market', '78 012 3456', 'Rue du Marché Central', 'Touba', 'Sénégal', 'support@terangamarket.sn'),
(12, 'Nestle', '77 777 77 77', 'Pikine Cite Lobatt Fall', 'Dakar', 'Senegal', 'nestle@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `quantite` int(11) DEFAULT 0,
  `prix` int(11) NOT NULL,
  `code_barre` varchar(50) DEFAULT NULL,
  `fournisseur` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT '../uploads/produits/default.svg',
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `categorie`, `quantite`, `prix`, `code_barre`, `fournisseur`, `description`, `photo`, `date_ajout`) VALUES
(1, 'Samsung Galaxy A54 5G', 'Électronique', 40, 299000, '8806094903006', 'SénElectro SARL', 'Smartphone milieu de gamme avec écran AMOLED 120Hz et triple capteur photo.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(2, 'Nike Air Force 1 Low', 'Accessoires', 99, 85000, '0019328723435', 'Fashion Empire', 'Sneakers emblématiques en cuir blanc, confort et style.', '../uploads/produits/prod_68255c3e0269d0.86111931.jpg', '2025-05-11 07:54:43'),
(3, 'Kärcher K2 Power Control', 'Maison & Jardin', 30, 125000, '4054278785605', 'Casa Verde', 'Nettoyeur haute pression idéal pour terrasses et véhicules.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(4, 'NIVEA Crème Douceur 250ml', 'Beauté & Santé', 150, 2500, '4005808721877', 'BelleVie Cosmetics', 'Crème hydratante pour tous types de peau, usage quotidien.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(5, 'Adidas Tiro 23 Training Pants', 'Sports & Loisirs', 60, 45000, '4065423790176', 'ActiveLife GmbH', 'Pantalon de sport respirant, coupe slim avec zips aux chevilles.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(6, 'PlayStation 5', 'Jeux vidéo', 20, 550000, '0711719541010', 'NeoGaming Inc.', 'Console de jeu nouvelle génération avec SSD ultra-rapide.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(7, 'Total Quartz 9000 5W40 - 5L', 'Automobile', 70, 27000, '3425901101713', 'AutoPlus Sénégal', 'Huile moteur synthétique hautes performances pour moteurs exigeants.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(8, 'L\'Étranger - Albert Camus', 'Livres', 120, 3500, '9782070360024', 'Librairie Baobab', 'Œuvre majeure de la littérature française, introspection et absurdité.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(9, 'Casque JBL Tune 510BT', 'Accessoires', 90, 35000, '6925281988316', 'TokyoTrends Co.', 'Casque sans fil Bluetooth avec basses puissantes et autonomie de 40h.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(10, 'Riz Royal Umbrella Thaï 25kg', 'Alimentation', 100, 23000, '8851026010257', 'Teranga Market', 'Riz parfumé thaïlandais premium, idéal pour la cuisine sénégalaise.', '../uploads/produits/default.svg', '2025-05-11 07:54:43'),
(12, 'Victus by HP Gaming Laptop 15z-fb200, 15.6\"', 'Électronique', 1, 799000, '0019328546735', 'NeoGaming Inc.', 'Windows 11 HomeAMD Ryzen™ 5 8645HS (up to 5.0 GHz, 16 MB L3 cache, 6 cores, 12 threads) + NVIDIA® GeForce RTX™ 3050 Laptop GPU (6 GB)8 GB DDR5-5600 MHz RAM (1 x 8 GB)512 GB PCIe® NVMe™ TLC M.2 SSD (4x4 SSD)', '../uploads/produits/prod_68255e7e814c21.02988491.webp', '2025-05-15 03:24:46');

-- --------------------------------------------------------

--
-- Structure de la table `produits_commandes`
--

CREATE TABLE `produits_commandes` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_total` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits_commandes`
--

INSERT INTO `produits_commandes` (`id`, `commande_id`, `produit_id`, `quantite`, `prix_total`, `created_at`) VALUES
(1, 2, 1, 1, 299000, '2025-05-11 07:59:19'),
(2, 3, 1, 1, 299000, '2025-05-12 09:20:26'),
(3, 4, 12, 1, 799000, '2025-05-15 04:01:13'),
(4, 5, 2, 1, 85000, '2025-05-15 04:10:11');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `adresse` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `login`, `password`, `tel`, `adresse`, `photo`, `role`) VALUES
(2, 'Ibrahima', 'Cissé', 'eic', '$2y$10$1sgYasC7PaFIVxSEEpRCgOyMrZr0RP1sroWNngmrk.dwUjUCr1/hq', '77 777 77 77', 'Pikine Cite Lobatt Fall', '../uploads/profile/default.svg', 'admin'),
(3, 'Lo', 'Cheikh', 'dtb', '$2y$10$rPMJwkvRjKSg1UmFCRY59.Eajju6tcUgAfe/PGXtQ9OkSW6qY9Sju', '77 777 77 77', 'Pikine', '../uploads/profile/default.svg', 'user');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`nom`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_barre` (`code_barre`);

--
-- Index pour la table `produits_commandes`
--
ALTER TABLE `produits_commandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `produits_commandes`
--
ALTER TABLE `produits_commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
