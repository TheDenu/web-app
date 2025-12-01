<?php
require_once './Service/DBConnect.php';

// Создание таблицы ролей
$sql_roles = "
CREATE TABLE IF NOT EXISTS roles (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы пользователей
$sql_users = "
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fio VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id_role) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы с местами
$sql_places = "
CREATE TABLE IF NOT EXISTS places (
    id_place INT AUTO_INCREMENT PRIMARY KEY,
    floor INT,
    room VARCHAR(10),
    section VARCHAR(30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы типов дефектов
$sql_defect_types = "
CREATE TABLE IF NOT EXISTS defect_types (
    id_defect_type INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы приоритетов
$sql_priorities = "
CREATE TABLE IF NOT EXISTS priorities (
    id_priority INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы приоритетов
$sql_statuses = "
CREATE TABLE IF NOT EXISTS statuses (
    id_status INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы заявок
$sql_applications = "
CREATE TABLE IF NOT EXISTS applications (
    id_application INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    place_id INT,
    description TEXT,
    defect_type_id INT,
    priority_id INT,
    status_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    solved_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE SET NULL,
    FOREIGN KEY (place_id) REFERENCES places(id_place) ON DELETE SET NULL,
    FOREIGN KEY (defect_type_id) REFERENCES defect_types(id_defect_type) ON DELETE SET NULL,
    FOREIGN KEY (priority_id) REFERENCES priorities(id_priority) ON DELETE SET NULL,
    FOREIGN KEY (status_id) REFERENCES statuses(id_status) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы фотографий
$sql_photos = "
CREATE TABLE IF NOT EXISTS photos (
    id_photo INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT,
    path VARCHAR(255),
    FOREIGN KEY (application_id) REFERENCES applications(id_application) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Создание таблицы токенов
$sql_user_tokens = "
CREATE TABLE IF NOT EXISTS user_tokens (
    id_token INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$queries = [
    $sql_roles,
    $sql_users,
    $sql_places,
    $sql_defect_types,
    $sql_priorities,
    $sql_statuses,
    $sql_applications,
    $sql_photos,
    $sql_user_tokens,
];

$mysqli = getDBConnection();

foreach ($queries as $query) {
    if (!$mysqli->query($query)) {
        echo "Ошибка миграции: " . $mysqli->error . "\n";
        exit;
    }
}

echo "Миграции успешно применены.\n";
