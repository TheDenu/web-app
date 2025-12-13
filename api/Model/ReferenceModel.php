<?php

class ReferenceModel
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function getPlaces(): array
    {
        $stmt = $this->mysqli->prepare("
            SELECT id_place as id, floor, room, section
            FROM places 
            ORDER BY floor ASC, room ASC, section ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getDefectTypes(): array
    {
        $stmt = $this->mysqli->prepare("
            SELECT id_defect_type as id, name 
            FROM defect_types 
            WHERE name IS NOT NULL 
            ORDER BY name ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPriorities(): array
    {
        $stmt = $this->mysqli->prepare("
            SELECT id_priority as id, name 
            FROM priorities 
            WHERE name IS NOT NULL 
            ORDER BY id_priority ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getStatuses(): array
    {
        $stmt = $this->mysqli->prepare("
            SELECT id_status as id, name 
            FROM statuses 
            WHERE name IS NOT NULL 
            ORDER BY id_status ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
