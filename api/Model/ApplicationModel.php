<?php

class ApplicationModel
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function getAll()
    {
        $result = $this->mysqli->query("
            SELECT a.*, u.fio as user_fio, p.floor, p.room, p.section,
                   dt.name as defect_type, pr.name as priority, s.name as status
            FROM applications a
            LEFT JOIN users u ON a.user_id = u.id_user
            LEFT JOIN places p ON a.place_id = p.id_place
            LEFT JOIN defect_types dt ON a.defect_type_id = dt.id_defect_type
            LEFT JOIN priorities pr ON a.priority_id = pr.id_priority
            LEFT JOIN statuses s ON a.status_id = s.id_status
            ORDER BY a.created_at DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByUser($user_id)
    {
        $stmt = $this->mysqli->prepare("
            SELECT a.*, p.floor, p.room, p.section,
                   dt.name as defect_type, pr.name as priority, s.name as status
            FROM applications a
            LEFT JOIN places p ON a.place_id = p.id_place
            LEFT JOIN defect_types dt ON a.defect_type_id = dt.id_defect_type
            LEFT JOIN priorities pr ON a.priority_id = pr.id_priority
            LEFT JOIN statuses s ON a.status_id = s.id_status
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
}
