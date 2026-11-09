-- =====================================================================
-- Touche pas au klaxon : jeu d'essais
-- Comptes de test :
--   Administrateur : admin@klaxon.fr             / Admin2026!
--   Utilisateurs   : tous les employes ci-dessous / Klaxon2026!
--     (exemple : alexandre.martin@email.fr)
-- Les dates des trajets sont relatives a la date d'execution du script.
-- =====================================================================
USE klaxon;

INSERT INTO agence (nom) VALUES
    ('Paris'),
    ('Lyon'),
    ('Marseille'),
    ('Toulouse'),
    ('Nice'),
    ('Nantes'),
    ('Strasbourg'),
    ('Montpellier'),
    ('Bordeaux'),
    ('Lille'),
    ('Rennes'),
    ('Reims');

INSERT INTO employe (nom, prenom, telephone, email, mot_de_passe, role) VALUES
    ('Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr', '$2y$10$4CyPyO2.tNQbQvdYNfnNWe.o.oVZlzFh5FoF4IdaI6b9D.pDYxh3O', 'user'),
    ('Dubois', 'Sophie', '0698765432', 'sophie.dubois@email.fr', '$2y$10$cI39zrBN6pe4sqIRwVFzFe27ieF6SZFTc9OKf7n/SucHU25JNEREi', 'user'),
    ('Bernard', 'Julien', '0622446688', 'julien.bernard@email.fr', '$2y$10$yy1zoNCotwYYRNKlNlpDvuoKWhtleLkACAw.Tf273Rf6zpCHPYcsy', 'user'),
    ('Moreau', 'Camille', '0611223344', 'camille.moreau@email.fr', '$2y$10$2jG0e3Ve7U.bCeumJ.F2ueIyW1E/2/1YTgf4KAQYWecCffgfESK6u', 'user'),
    ('Lefèvre', 'Lucie', '0777889900', 'lucie.lefevre@email.fr', '$2y$10$gD0Bx49nrIKWw2UIQKnHHO6JB87AK0bUyUAu7kry5hYcuYsketVoC', 'user'),
    ('Leroy', 'Thomas', '0655443322', 'thomas.leroy@email.fr', '$2y$10$Idg8N9b8O8KC3laGhdN/teBqd6g3Pn5Ae3rvLZb6le87YaWAEmqoa', 'user'),
    ('Roux', 'Chloé', '0633221199', 'chloe.roux@email.fr', '$2y$10$DebpvdxpcDigwDhFa5rASulQvI4t8.rpRWl8abjhsngeky0D.RAAS', 'user'),
    ('Petit', 'Maxime', '0766778899', 'maxime.petit@email.fr', '$2y$10$yccTVABucOA9IJgeJpPnAeSpL626NDLwjCh1DYXeulUsnvdfpk9me', 'user'),
    ('Garnier', 'Laura', '0688776655', 'laura.garnier@email.fr', '$2y$10$/ReZYyRDgAspIF0NQkYzn.GSwg3Ciq3DgLxctRMLX9SDuLHvOI6Za', 'user'),
    ('Dupuis', 'Antoine', '0744556677', 'antoine.dupuis@email.fr', '$2y$10$SGEqLNL7UMNj32m.JWSTJO30aWyOw9sUpV0/c6oELO62PwkXHsuoG', 'user'),
    ('Lefebvre', 'Emma', '0699887766', 'emma.lefebvre@email.fr', '$2y$10$DQbLYBdqcND9yYtg9mCHSOxutlzVmmW5NvYiRBxY60LbnYNlKWzt.', 'user'),
    ('Fontaine', 'Louis', '0655667788', 'louis.fontaine@email.fr', '$2y$10$zykWbJdgZrbtL8mQfOow1eNSD6ejlRUTGAkKxzEDLDpkkLQIxiKUy', 'user'),
    ('Chevalier', 'Clara', '0788990011', 'clara.chevalier@email.fr', '$2y$10$wtKmuzIgOt4/UML8RV4id.VGzBf4aOGng9IbsfAkDMVoy/wGyLLvy', 'user'),
    ('Robin', 'Nicolas', '0644332211', 'nicolas.robin@email.fr', '$2y$10$CZzXNMYvOgv0z7loK2gf7u2c/lbuTEjReSExj4N/fVyVIW0Jb34Y2', 'user'),
    ('Gauthier', 'Marine', '0677889922', 'marine.gauthier@email.fr', '$2y$10$5AocRUPjqf6bw9xBWjK8kek6fgjqm/kgdHLQUgMNfENvW6WQ/vGry', 'user'),
    ('Fournier', 'Pierre', '0722334455', 'pierre.fournier@email.fr', '$2y$10$9jIReRedyYyf1pASXIcBqeEWPcRk5yRrt2pI/VSpKIEjD5dp550OG', 'user'),
    ('Girard', 'Sarah', '0688665544', 'sarah.girard@email.fr', '$2y$10$wv1xxQ8fhkLqRytwlEgoleUzs2VfnNNgp3QYAssyBykaEEOZ/p60a', 'user'),
    ('Lambert', 'Hugo', '0611223366', 'hugo.lambert@email.fr', '$2y$10$DQ9lsErGN.d06.j92e7Hgu4QWUgOmx781zOSYd7yu2Bm2DyyuUYX2', 'user'),
    ('Masson', 'Julie', '0733445566', 'julie.masson@email.fr', '$2y$10$3i2evv8eOD4UWfk9F8iUduuMkvdIMtbStmgBGqNdesp6h0nWD/UsW', 'user'),
    ('Henry', 'Arthur', '0666554433', 'arthur.henry@email.fr', '$2y$10$xriXY1JG6mZpflkHLXaYPe9c6vzllKQYOS1vVIIbYVxUahB8Fayeu', 'user'),
    ('Administrateur', 'Klaxon', '0100000000', 'admin@klaxon.fr', '$2y$10$iwiDyl4CSUxihYqiWsq1tergyA0vCX.LlITOQLfhvoQ7rQQKwep9.', 'admin');

INSERT INTO trajet (id_agence_depart, id_agence_arrivee, date_depart, date_arrivee, places_total, places_dispo, id_employe) VALUES
    (1, 2, TIMESTAMP(CURDATE() + INTERVAL 1 DAY, '08:00:00'), TIMESTAMP(CURDATE() + INTERVAL 1 DAY, '12:15:00'), 4, 3, 1),  -- Paris -> Lyon
    (2, 1, TIMESTAMP(CURDATE() + INTERVAL 1 DAY, '17:30:00'), TIMESTAMP(CURDATE() + INTERVAL 1 DAY, '21:45:00'), 3, 2, 2),  -- Lyon -> Paris
    (1, 10, TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '07:30:00'), TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '09:30:00'), 3, 1, 3),  -- Paris -> Lille
    (10, 1, TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '18:00:00'), TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '20:00:00'), 4, 4, 3),  -- Lille -> Paris
    (9, 4, TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '09:00:00'), TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '11:30:00'), 2, 2, 5),  -- Bordeaux -> Toulouse
    (4, 8, TIMESTAMP(CURDATE() + INTERVAL 3 DAY, '14:00:00'), TIMESTAMP(CURDATE() + INTERVAL 3 DAY, '16:30:00'), 3, 1, 6),  -- Toulouse -> Montpellier
    (3, 5, TIMESTAMP(CURDATE() + INTERVAL 3 DAY, '08:30:00'), TIMESTAMP(CURDATE() + INTERVAL 3 DAY, '10:45:00'), 4, 3, 7),  -- Marseille -> Nice
    (6, 11, TIMESTAMP(CURDATE() + INTERVAL 4 DAY, '07:45:00'), TIMESTAMP(CURDATE() + INTERVAL 4 DAY, '09:00:00'), 3, 2, 9),  -- Nantes -> Rennes
    (11, 1, TIMESTAMP(CURDATE() + INTERVAL 4 DAY, '13:00:00'), TIMESTAMP(CURDATE() + INTERVAL 4 DAY, '16:30:00'), 4, 2, 10),  -- Rennes -> Paris
    (7, 12, TIMESTAMP(CURDATE() + INTERVAL 5 DAY, '06:30:00'), TIMESTAMP(CURDATE() + INTERVAL 5 DAY, '10:00:00'), 3, 3, 12),  -- Strasbourg -> Reims
    (2, 3, TIMESTAMP(CURDATE() + INTERVAL 6 DAY, '09:15:00'), TIMESTAMP(CURDATE() + INTERVAL 6 DAY, '12:00:00'), 4, 1, 14),  -- Lyon -> Marseille
    (8, 3, TIMESTAMP(CURDATE() + INTERVAL 7 DAY, '16:00:00'), TIMESTAMP(CURDATE() + INTERVAL 7 DAY, '18:00:00'), 2, 1, 16),  -- Montpellier -> Marseille
    (5, 2, TIMESTAMP(CURDATE() + INTERVAL 8 DAY, '07:00:00'), TIMESTAMP(CURDATE() + INTERVAL 8 DAY, '13:30:00'), 4, 4, 18),  -- Nice -> Lyon
    (1, 12, TIMESTAMP(CURDATE() + INTERVAL 1 DAY, '09:00:00'), TIMESTAMP(CURDATE() + INTERVAL 1 DAY, '11:00:00'), 3, 0, 4),  -- Paris -> Reims
    (12, 7, TIMESTAMP(CURDATE() + INTERVAL 3 DAY, '15:00:00'), TIMESTAMP(CURDATE() + INTERVAL 3 DAY, '18:30:00'), 2, 0, 8),  -- Reims -> Strasbourg
    (2, 5, TIMESTAMP(CURDATE() + INTERVAL -3 DAY, '08:00:00'), TIMESTAMP(CURDATE() + INTERVAL -3 DAY, '14:00:00'), 4, 2, 11),  -- Lyon -> Nice
    (4, 9, TIMESTAMP(CURDATE() + INTERVAL -1 DAY, '10:00:00'), TIMESTAMP(CURDATE() + INTERVAL -1 DAY, '12:30:00'), 3, 1, 13);  -- Toulouse -> Bordeaux
