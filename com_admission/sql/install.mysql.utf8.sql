CREATE TABLE IF NOT EXISTS `#__admission_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `description` text,
    `email` varchar(100),
    `phone` varchar(20),
    `status` varchar(50) DEFAULT 'pending',
    `state` tinyint(1) NOT NULL DEFAULT 1,
    `created` datetime NOT NULL,
    `created_by` int(11) NOT NULL,
    `modified` datetime,
    `modified_by` int(11),
    `ordering` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;