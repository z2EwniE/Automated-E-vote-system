ALTER TABLE `candidates` 
ADD COLUMN `election_id` int(11) DEFAULT NULL,
ADD FOREIGN KEY (`election_id`) REFERENCES `election_settings`(`id`) ON DELETE SET NULL;