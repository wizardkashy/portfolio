-- CREATE TABLE IF NOT EXISTS `user` (
--     `id` int(11) NOT NULL AUTO_INCREMENT,
--     `username` varchar(32) NOT NULL UNIQUE,
--     `rating` int(11) DEFAULT '1000',
--     `rd` int(11) DEFAULT '350',
--     `is_admin` tinyint(1),
--     `password_hash` TEXT NOT NULL,
--     PRIMARY KEY (`id`)
-- );

CREATE TABLE IF NOT EXISTS `friendship` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `requester_id` int(11) NOT NULL,
    `requestee_id` int(11) NOT NULL,
    `status` varchar(9),
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `match` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `host_id` int(11),
    `guest_id` int(11),
    `host_elochange` int(3),
    `guest_elochange` int(3),
    `match_events` TEXT,
    `deck_order` TEXT,
    `winner_id` int(11),
    `timestamp` TIMESTAMP,
    PRIMARY KEY (`id`)
);

SET time_zone="+00:00";
