<?php
include (dirname(__FILE__) . "/../../../include/DB_itit.php");

class Settings {
    private $table = 'ClimateData_administration';
    private $db;
    public $maintenance;
    public $maintenanceMshsk;


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

        $this->maintenance = $res['maintenance'];
        $this->maintenanceMshsk = $res['maintenanceMshsk'];

    }

    public function save() {
        $sql = "UPDATE $this->table
                    SET maintenance = $this->maintenance, maintenanceMshsk = $this->maintenanceMshsk
                    ";
        $this->db->prepare($sql)->execute();
    }
}

$settings = new Settings($db);
