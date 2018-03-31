<?php
include_once('./TP.php');

class Meteostations {
    private $table_name = "ClimateData_meteostation_id";
    private $db;


    public function __construct($db)
    {
        $this->db = $db;

    }

    public function get()
    {
        $query = "SELECT * FROM $this->table_name";
        $meteostations = $this->db->query($query);
        return $meteostations->fetchAll();

    }
}