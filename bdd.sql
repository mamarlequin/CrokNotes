CREATE TABLE `USER`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `PP_ext` VARCHAR(255) NOT NULL DEFAULT 'None',
    `admin` BOOLEAN NOT NULL
);
CREATE TABLE `RECETTE`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_createur` BIGINT NOT NULL,
    `id_categorie` BIGINT NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `image_ext` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL
);
CREATE TABLE `ETAPE`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_recette` BIGINT NOT NULL,
    `contenu` TEXT NOT NULL
);
CREATE TABLE `INGREDIENT`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL
);
CREATE TABLE `APPARTIENT`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_ingredient` BIGINT NOT NULL,
    `id_recette` BIGINT NOT NULL,
    `quantite` BIGINT NOT NULL,
    `unite` VARCHAR(255) NOT NULL
);
CREATE TABLE `CATEGORIE`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL
);
CREATE TABLE `PANIER`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_user` BIGINT NOT NULL,
    `id_recette` BIGINT NOT NULL
);
ALTER TABLE
    `PANIER` ADD CONSTRAINT `panier_id_user_foreign` FOREIGN KEY(`id_user`) REFERENCES `USER`(`id`);
ALTER TABLE
    `ETAPE` ADD CONSTRAINT `etape_id_recette_foreign` FOREIGN KEY(`id_recette`) REFERENCES `RECETTE`(`id`);
ALTER TABLE
    `RECETTE` ADD CONSTRAINT `recette_id_categorie_foreign` FOREIGN KEY(`id_categorie`) REFERENCES `CATEGORIE`(`id`);
ALTER TABLE
    `APPARTIENT` ADD CONSTRAINT `appartient_id_ingredient_foreign` FOREIGN KEY(`id_ingredient`) REFERENCES `INGREDIENT`(`id`);
ALTER TABLE
    `APPARTIENT` ADD CONSTRAINT `appartient_id_recette_foreign` FOREIGN KEY(`id_recette`) REFERENCES `RECETTE`(`id`);
ALTER TABLE
    `PANIER` ADD CONSTRAINT `panier_id_recette_foreign` FOREIGN KEY(`id_recette`) REFERENCES `RECETTE`(`id`);
ALTER TABLE
    `RECETTE` ADD CONSTRAINT `recette_id_createur_foreign` FOREIGN KEY(`id_createur`) REFERENCES `USER`(`id`);

INSERT INTO CATEGORIE (nom) VALUES
('Entrées Froides'),
('Entrées Chaudes'),
('Plats de Résistance'),
('Desserts'),
('Apéritifs & Amuse-bouches'),
('Accompagnements'),
('Sauces & Condiments'),
('Soupes & Veloutés'),
('Salades Composées'),
('Petit-déjeuner & Brunch'),
('Goûter & Snacks'),
('Boissons & Smoothies'),
('Cocktails & Mocktails'),
('Boulangerie & Viennoiserie'),
('Pâtisserie Fine'),
('Cuisine Française'),
('Cuisine Italienne'),
('Cuisine Asiatique'),
('Cuisine Mexicaine'),
('Cuisine Maghrébine'),
('Cuisine Indienne'),
('Cuisine Méditerranéenne'),
('Végétarien'),
('Vegan'),
('Sans Gluten'),
('Sans Lactose'),
('Minceur & Bien-être'),
('Recettes de Fêtes'),
('Cuisine Rapide (moins de 30 min)'),
('Petits Budgets'),
('Cuisine au Four'),
('Cuisine à la Vapeur'),
('Grillades & Barbecue');