<?php

class ApplicationModel
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function getAll(int $limit = 10, int $offset = 0, ?int $status_id = null, ?string $search = null): array
    {
        $sql = "
        SELECT a.*, 
               n.fio as user_fio,
               u.login as user_login,
               p.floor, p.room, p.section,
               dt.name as defect_type, 
               pr.name as priority, 
               s.name as status
        FROM applications a
        LEFT JOIN users u ON a.user_id = u.id_user
        LEFT JOIN names n ON u.fio_id = n.id_fio
        LEFT JOIN places p ON a.place_id = p.id_place
        LEFT JOIN defect_types dt ON a.defect_type_id = dt.id_defect_type
        LEFT JOIN priorities pr ON a.priority_id = pr.id_priority
        LEFT JOIN statuses s ON a.status_id = s.id_status
        WHERE 1=1
    ";

        $params = [];
        $types = '';

        if ($status_id !== null) {
            $sql .= " AND a.status_id = ?";
            $types .= 'i';
            $params[] = $status_id;
        }

        if ($search !== null && $search !== '') {
            $sql .= " AND (
            a.description LIKE CONCAT('%', ?, '%')
            OR n.fio LIKE CONCAT('%', ?, '%')
            OR u.login LIKE CONCAT('%', ?, '%')
            OR p.room LIKE CONCAT('%', ?, '%')
            OR p.section LIKE CONCAT('%', ?, '%')
            OR p.floor LIKE CONCAT('%', ?, '%')
        )";
            $types .= 'ssssss';
            $search_param = trim($search);
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $apps = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($apps as &$app) {
            $app['photos'] = $this->getPhotosByApplicationId((int)$app['id_application']);
        }

        return $apps;
    }
    
    public function getAdminStats(): array
    {
        $stmt = $this->mysqli->prepare("
        SELECT 
            s.id_status,
            s.name,
            COUNT(a.id_application) as count
        FROM statuses s
        LEFT JOIN applications a ON s.id_status = a.status_id
        GROUP BY s.id_status, s.name
        ORDER BY s.id_status
    ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public function getApplicationsCount(): int
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as count FROM applications");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['count'];
    }

    public function getByUser(
        int $user_id,
        int $limit = 10,
        int $offset = 0,
        ?string $search = null,
        ?int $status_id = null
    ): array {
        $sql = "
        SELECT a.*, 
               n.fio as user_fio,
               p.floor, p.room, p.section,
               dt.name as defect_type, 
               pr.name as priority, 
               s.name as status
        FROM applications a
        LEFT JOIN users u ON a.user_id = u.id_user
        LEFT JOIN names n ON u.fio_id = n.id_fio
        LEFT JOIN places p ON a.place_id = p.id_place
        LEFT JOIN defect_types dt ON a.defect_type_id = dt.id_defect_type
        LEFT JOIN priorities pr ON a.priority_id = pr.id_priority
        LEFT JOIN statuses s ON a.status_id = s.id_status
        WHERE a.user_id = ?
    ";

        $params = [$user_id];
        $types  = 'i';

        if ($status_id !== null) {
            $sql .= " AND a.status_id = ?";
            $types .= 'i';
            $params[] = $status_id;
        }

        if ($search !== null && $search !== '') {
            $sql .= " AND (
            a.description LIKE CONCAT('%', ?, '%')
            OR p.room LIKE CONCAT('%', ?, '%')
            OR p.section LIKE CONCAT('%', ?, '%')
        )";
            $types .= 'sss';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";

        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $apps = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($apps as &$app) {
            $app['photos'] = $this->getPhotosByApplicationId((int)$app['id_application']);
        }

        return $apps;
    }

    public function getUserApplicationsCount(int $user_id): int
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as count FROM applications WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['count'];
    }

    private function getPhotosByApplicationId(int $applicationId): array
    {
        $stmt = $this->mysqli->prepare("
        SELECT path 
        FROM photos 
        WHERE application_id = ?
        ORDER BY id_photo ASC
    ");
        $stmt->bind_param("i", $applicationId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return array_column($rows, 'path');
    }




    public function create($user_id, $data, $files = null)
    {
        $this->mysqli->begin_transaction();

        try {
            $stmt = $this->mysqli->prepare("
                INSERT INTO applications (user_id, place_id, description, defect_type_id, priority_id, status_id) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->bind_param(
                "iisii",
                $user_id,
                $data['place_id'],
                $data['description'],
                $data['defect_type_id'],
                $data['priority_id']
            );
            if (!$stmt->execute()) {
                $this->mysqli->rollback();
            }

            $application_id = $this->mysqli->insert_id;

            if ($files && isset($files['name']) && is_array($files['name'])) {
                $uploadsDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }

                $stmtPhoto = $this->mysqli->prepare("
                    INSERT INTO photos (application_id, path) VALUES (?, ?)
                ");

                for ($i = 0; $i < count($files['name']); $i++) {
                    if (
                        $files['error'][$i] === UPLOAD_ERR_OK &&
                        is_uploaded_file($files['tmp_name'][$i])
                    ) {

                        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                        if (!in_array($ext, $allowed)) {
                            error_log("Неподдерживаемый формат: " . $files['name'][$i]);
                            continue;
                        }

                        $filename = uniqid('app_photo_' . $application_id . '_') . '.' . $ext;
                        $filePath = $uploadsDir . $filename;

                        if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
                            $relativePath = 'uploads/' . $filename;
                            $stmtPhoto->bind_param("is", $application_id, $relativePath);
                            $stmtPhoto->execute();
                        } else {
                            error_log("Ошибка сохранения файла: " . $files['name'][$i]);
                        }
                    }
                }
                $stmtPhoto->close();
            }

            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            error_log("Ошибка создания заявки с фото: " . $e->getMessage());
            return false;
        }
    }

    public function deleteById($application_id, $user_id = null)
    {
        $stmt = $this->mysqli->prepare("
            SELECT status_id FROM applications 
            WHERE id_application = ? " . ($user_id ? 'AND user_id = ?' : '') . "
        ");

        if ($user_id) {
            $stmt->bind_param("ii", $application_id, $user_id);
        } else {
            $stmt->bind_param("i", $application_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['status_id'] == 1) {
                $stmtDelete = $this->mysqli->prepare("
                DELETE FROM applications 
                WHERE id_application = ? " . ($user_id ? 'AND user_id = ?' : '') . "
            ");

                if ($user_id) {
                    $stmtDelete->bind_param("ii", $application_id, $user_id);
                } else {
                    $stmtDelete->bind_param("i", $application_id);
                }

                return $stmtDelete->execute();
            }
        }

        return false;
    }

    public function updateStatus(int $application_id, int $status_id): bool
    {
        $stmt = $this->mysqli->prepare("
        UPDATE applications 
        SET status_id = ?
        WHERE id_application = ?
    ");
        $stmt->bind_param("ii", $status_id, $application_id);
        return $stmt->execute();
    }
}
