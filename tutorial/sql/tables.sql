
-- users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    UNIQUE (email)
);

--todos table
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `todos` (
  `id` int(11) NOT NULL AUTO_INCREMENT, -- Primary key column with auto-increment
  `title` text NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `creater` int(11) NOT NULL,
  PRIMARY KEY (`id`), -- Define 'id' as the primary key
  FOREIGN KEY (`creater`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE todos
ADD COLUMN category VARCHAR(255) DEFAULT NULL;

COMMIT;
