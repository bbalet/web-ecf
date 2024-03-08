-- Ce fichier est un exemple de transaction SQL pour copier un cinéma.
-- On indique le nom du cinéma qui sera la source de la copie (par ex. "Nantes")
-- et le nom du nouveau cinéma (par ex. "Marseille")
-- le script copie les salles et leur disposition de sièges à l'indentique
--
-- Mode d'emploi :
-- 1. Remplacer les valeurs entre crochets en début de script par les valeurs correspondantes
-- 2. Exécuter le fichier dans un client SQL (phpMyAdmin, etc.) ou via la ligne de commande
-- 3. Vérifier que la transaction s'est bien déroulée
-- 
-- Exemple d'utilisation (app est la base de données cible) :
-- sudo mysql -u root -D app < copy-theater.sql
--

SET collation_connection = 'utf8mb4_unicode_ci';
START TRANSACTION;

-- ATTENTION : veuillez modifier ces valeurs
SET @source_theater_name = 'Nantes'; -- [nom du cinéma source de la copie];
SET @target_theater_name = 'Marseille'; -- [nom du nouveau cinéma];
SET @source_theater_id = (SELECT id FROM theater WHERE city = @source_theater_name);

-- créer le nouveau cinéma
INSERT INTO theater (city) VALUES (@target_theater_name);
SET @target_theater_id = LAST_INSERT_ID();

-- créer les salles en les copiant depuis le cinéma source de la copie
INSERT INTO room (theater_id, quality_id, number, capacity, columns)  
    SELECT @target_theater_id, quality_id, number, capacity, columns
    FROM room
    WHERE theater_id = @source_theater_id;

-- créer les sièges dans les salles
INSERT INTO seat (room_id, number, for_disabled)  
    SELECT room_target.id, seat.number, for_disabled
    FROM seat
    INNER JOIN room room_source ON room_source.id = seat.room_id
    INNER JOIN room room_target ON room_target.number = room_source.number
    WHERE room_source.theater_id = @source_theater_id
    AND room_target.theater_id = @target_theater_id;

-- Valider la transaction
COMMIT;
