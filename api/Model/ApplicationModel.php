<?php
require_once './Service/DBConnect.php';

class ApplicationModel
{

    protected static $cache = [
        'defect_types' => null,
        'priorities' => null,
        'statuses' => null,
    ];

    protected $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function getDefectTypes()
    {
        if (self::$cache['defect_types'] === null) {
            $result = $this->mysqli->query("SELECT id_defect_type, name FROM defect_types");
            $types = [];
            while ($row = $result->fetch_assoc()) {
                $types[$row['name']] = $row['id_defect_type'];
            }
            self::$cache['defect_types'] = $types;
        }
        return self::$cache['defect_types'];
    }

    public function getPriorities()
    {
        if (self::$cache['priorities'] === null) {
            $result = $this->mysqli->query("SELECT id_priority, name FROM priorities");
            $priorities = [];
            while ($row = $result->fetch_assoc()) {
                $priorities[$row['name']] = $row['id_priority'];
            }
            self::$cache['priorities'] = $priorities;
        }
        return self::$cache['priorities'];
    }

    public function getStatuses()
    {
        if (self::$cache['statuses'] === null) {
            $result = $this->mysqli->query("SELECT id_status, name FROM statuses");
            $priorities = [];
            while ($row = $result->fetch_assoc()) {
                $priorities[$row['name']] = $row['id_status'];
            }
            self::$cache['statuses'] = $priorities;
        }
        return self::$cache['statuses'];
    }

    public function getDefectTypesID(string $userType)
    {
        $types = $this->getDefectTypes();

        $userType = mb_strtolower((trim($userType)));

        foreach ($types as $name => $id) {
            if (mb_strtolower($name) === $userType) {
                return $id;
            }
        }
        return null;
    }

    public function getPrioritiesID(string $userPriority)
    {
        $priorities = $this->getPriorities();

        $userPriority = mb_strtolower((trim($userPriority)));

        foreach ($priorities as $name => $id) {
            if (mb_strtolower($name) === $userPriority) {
                return $id;
            }
        }
        return null;
    }

    public function getStatusesID(string $userStatus)
    {
        $statuses = $this->getStatuses();

        $userStatus = mb_strtolower((trim($userStatus)));

        foreach ($statuses as $name => $id) {
            if (mb_strtolower($name) === $userStatus) {
                return $id;
            }
        }
        return null;
    }

    public function createApplication(array $data)
    {
        try {
            $floor = $data['floor'];
            $room = $data['room'];
            $defectTypeId = $this->getDefectTypesID($data['defect_type']);
            $priorityId = $this->getPrioritiesID($data['priority']);
            $description = $data['description'];
            $photo = $data['path'];
            $statusId = $this->getStatusesID("в ожидании");

            $stmt = $this->mysqli->prepare("INSERT INTO applications (floor, room, defect_type_id, priority_id, description, photo, status_id) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param("ssiissi", $floor, $room, $defectTypeId, $priorityId, $description, $photo, $statusId);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (mysqli_sql_exception $ex) {
            error_log("Ошибка при выполнении запроса: " . $ex->getMessage());
            return false;
        }
    }
}
