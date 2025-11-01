<?php
require_once './Service/DBConnect.php';

class ApplicationModel {
    private static $cache = [
        'defect_types' => null,
        'priorities' => null,
    ];

    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;     
    }

    public function getDefectTypes(){
        if(self::$cache['defect_types'] === null){
            $result = $this->mysqli->query("SELECT id_defect_type, name FROM defect_type");
            $types = [];
            while ($row = $result->fetch_assoc()){
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

    public function createApplication(array $data){
        
    }
}