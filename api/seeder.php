<?php
require_once 'Service/DBConnect.php';

$sql_roles = "
    INSERT INTO `roles`(`role_name`) 
    VALUES ('user'), ('admin');
";

$sql_places = "
INSERT INTO places (floor, room, section) VALUES
(2, '201', 'male'), (2, '202', 'male'), (2, '203', 'male'), (2, '204', 'male'), (2, '205', 'male'), (2, '206', 'male'), (2, '207', 'male'), (2, '208', 'male'), (2, 'кухня', 'male'), (2, 'коридор', 'male'),
(2, '211', 'female'), (2, '212', 'female'), (2, '213', 'female'), (2, '214', 'female'), (2, '215', 'female'), (2, '216', 'female'), (2, '217', 'female'), (2, '218', 'female'), (2, 'кухня', 'female'), (2, 'коридор', 'female'),
(3, '301', 'male'), (3, '302', 'male'), (3, '303', 'male'), (3, '304', 'male'), (3, '305', 'male'), (3, '306', 'male'), (3, '307', 'male'), (3, '308', 'male'), (3, 'кухня', 'male'), (3, 'коридор', 'male'),
(3, '311', 'female'), (3, '312', 'female'), (3, '313', 'female'), (3, '314', 'female'), (3, '315', 'female'), (3, '316', 'female'), (3, '317', 'female'), (3, '318', 'female'), (3, 'кухня', 'female'), (3, 'коридор', 'female'),
(4, '401', 'male'), (4, '402', 'male'), (4, '403', 'male'), (4, '404', 'male'), (4, '405', 'male'), (4, '406', 'male'), (4, '407', 'male'), (4, '408', 'male'), (4, 'кухня', 'male'), (4, 'коридор', 'male'),
(4, '411', 'female'), (4, '412', 'female'), (4, '413', 'female'), (4, '414', 'female'), (4, '415', 'female'), (4, '416', 'female'), (4, '417', 'female'), (4, '418', 'female'), (4, 'кухня', 'female'), (4, 'коридор', 'female'),
(5, '501', 'male'), (5, '502', 'male'), (5, '503', 'male'), (5, '504', 'male'), (5, '505', 'male'), (5, '506', 'male'), (5, '507', 'male'), (5, '508', 'male'), (5, 'кухня', 'male'), (5, 'коридор', 'male'),
(5, '511', 'female'), (5, '512', 'female'), (5, '513', 'female'), (5, '514', 'female'), (5, '515', 'female'), (5, '516', 'female'), (5, '517', 'female'), (5, '518', 'female'), (5, 'кухня', 'female'), (5, 'коридор', 'female'),
(6, '601', 'male'), (6, '602', 'male'), (6, '603', 'male'), (6, '604', 'male'), (6, '605', 'male'), (6, '606', 'male'), (6, '607', 'male'), (6, '608', 'male'), (6, 'кухня', 'male'), (6, 'коридор', 'male'),
(6, '611', 'female'), (6, '612', 'female'), (6, '613', 'female'), (6, '614', 'female'), (6, '615', 'female'), (6, '616', 'female'), (6, '617', 'female'), (6, '618', 'female'), (6, 'кухня', 'female'), (6, 'коридор', 'female'),
(7, '701', 'male'), (7, '702', 'male'), (7, '703', 'male'), (7, '704', 'male'), (7, '705', 'male'), (7, '706', 'male'), (7, '707', 'male'), (7, '708', 'male'), (7, 'кухня', 'male'), (7, 'коридор', 'male'),
(7, '711', 'female'), (7, '712', 'female'), (7, '713', 'female'), (7, '714', 'female'), (7, '715', 'female'), (7, '716', 'female'), (7, '717', 'female'), (7, '718', 'female'), (7, 'кухня', 'female'), (7, 'коридор', 'female'),
(8, '801', 'male'), (8, '802', 'male'), (8, '803', 'male'), (8, '804', 'male'), (8, '805', 'male'), (8, '806', 'male'), (8, '807', 'male'), (8, '808', 'male'), (8, 'кухня', 'male'), (8, 'коридор', 'male'),
(8, '811', 'female'), (8, '812', 'female'), (8, '813', 'female'), (8, '814', 'female'), (8, '815', 'female'), (8, '816', 'female'), (8, '817', 'female'), (8, '818', 'female'), (8, 'кухня', 'female'), (8, 'коридор', 'female'),
(9, '901', 'male'), (9, '902', 'male'), (9, '903', 'male'), (9, '904', 'male'), (9, '905', 'male'), (9, '906', 'male'), (9, '907', 'male'), (9, '908', 'male'), (9, 'кухня', 'male'), (9, 'коридор', 'male'),
(9, '911', 'female'), (9, '912', 'female'), (9, '913', 'female'), (9, '914', 'female'), (9, '915', 'female'), (9, '916', 'female'), (9, '917', 'female'), (9, '918', 'female'), (9, 'кухня', 'female'), (9, 'коридор', 'female');

";

$sql_defect_types = "
    INSERT INTO defect_types(name)
    VALUES ('сантехника'), ('электрика'), ('плотник');
";

$sql_priorities = "
    INSERT INTO priorities (name)
    VALUES ('низкий'), ('средний'), ('высокий');
";

$sql_statuses = "
    INSERT INTO statuses (name)
    VALUES ('проверка'), ('ожидание'), ('выполняется'), ('выполнена'), ('отказано');
";

$queries = [
    $sql_roles,
    $sql_places,
    $sql_defect_types,
    $sql_priorities,
    $sql_statuses,
];

$mysqli = getDBConnection();

foreach ($queries as $query) {
    if (!$mysqli->query($query)) {
        echo "Ошибка сидера: " . $mysqli->error . "\n";
        exit;
    }
}

echo "Данные успешно добавлены.\n";