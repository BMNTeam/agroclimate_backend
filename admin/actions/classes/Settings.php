<?php
include_once('Helpers.php');

class Settings {
    private $table = 'ClimateData_Settings';
    private $db;
    public $maintenance;


    public function __construct($db)
    {
        $this->db = $db;
        $this->get();
    }
    private function get()
    {
        $sql = "SELECT * FROM $this->table";
        $settings = $this->db->query($sql);

        $res = $settings->fetchAll(PDO::FETCH_ASSOC)[0];
        $this->maintenance = $res['Maintenance'];

    }

    public function save() {
        $sql = "UPDATE $this->table
                    SET Maintenance = $this->maintenance";
        $this->db->prepare($sql)->execute();
    }
}
