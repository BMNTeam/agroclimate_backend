<?php
include('TP.php');
include_once('Helpers.php');

class Decades {
    private $table_name = "ClimateDataDecade_TP";
    private $db;
    private $helpers;


    public function __construct($db)
    {
        $this->db = $db;
        $this->helpers = new Helpers();

        //$this->migrate();
        //$this->insert_empty_year(2018);
    }

    public function get($meteostation_id, $year)
    {

        if(!$meteostation_id || !$year) return;
        if(! $this->is_already_exist($year)) $this->insert_empty_year($year);

        $sql = "SELECT * FROM $this->table_name WHERE MeteostationID = $meteostation_id AND  Year = $year";

        $query = $this->db->query($sql);
        $response = $query->fetchAll(PDO::FETCH_ASSOC);
        return $response;


    }
    public function set($post)
    {
        $year = $post['Year'];
        $meteostation_id = $post['MeteostationID'];

        $columns_arr = explode(',', rtrim($this->generate_columns(), ','));

        /**
         * @param array $post
         * @param array of decades columns
         * @return string SET values | example 'T1_1 = 12, ..., P12_3 = 6'
        */
        function prepare_insert($post,$columns_arr)
        {
            $update = null;
            foreach($columns_arr as $column)
            {
                $column = trim($column);
                $update .= "$column = $post[$column],";
            }
            return rtrim($update, ',');
        }
        $set_sting = prepare_insert($post, $columns_arr);
        $this->helpers->clear_from_null($set_sting);
        $query = "UPDATE $this->table_name " .
                 "SET $set_sting " .
                 "WHERE Year = $year AND MeteostationID = $meteostation_id";
        $this->db->prepare($query)->execute();

        $tp_table = new TP($this->db);
        $tp_table->set($this->count_average($post));

        $gtk_table = new Gtk($this->db);
        $gtk_table->count_gtk($post);
    }

    /**
     * @param $post array with decades data | [ select => 1, T1_1 => 12, ... , P12_3 => 16]
     * @return array average temperatures | ex: [ 'year_to_edit' => 2018, 'T1' => '10' ... ]
     */
    private function count_average($post)
    {
        /**
         * @param $post array of post elements should have 'T1_1' etc
         * @param $param string might be temperature (T) or precipitations (P)
         * @return array of elements to insert | ex: [T1 => 2, T2=> 3, ... , T12 => 16]
         */
        function get_averages($post, $param){
            $average_arr = array();
            for($m=1; $m<=12; $m++)
            {
                $sum = null;
                for($d=1; $d<=3; $d++)
                {
                    $param_str = "$param$m".'_'."$d"; // Ex: T1_1
                    if($post[$param_str] == 'NULL'){ $sum = null; continue;};
                    $sum += $post[$param_str];
                }
                $average = $sum/3; //3 decades
                if($average === 0) { $average = 'NULL';}; // Might be an error if average is equal to 0

                $param_average = array("$param$m"=> $average); // Example | ['T1 => 12']
                array_push($average_arr, $param_average);
            }
            return $average_arr;
        }


        /**
         * @param $post array of post elements should have 'P1_1' etc
         * @param $param string might be temperature (T) or precipitations (P)
         * @return array of sums elements | ex: [P1 => 2, P2=> 3, ... , P12 => 16]
         */
        function get_sums($post, $param)
        {
            $sum_arr = array();
            for($m=1; $m<=12; $m++)
            {
                $sum = null;
                for($d=1; $d<=3; $d++)
                {
                    $param_str = "$param$m".'_'."$d"; // Ex: T1_1
                    if($post[$param_str] == 'NULL'){ $sum = null; continue;};
                    $sum += $post[$param_str];
                }
                (!$sum) ? $sum = 'NULL': $sum;

                $param_sum = array("$param$m"=> $sum);
                array_push($sum_arr, $param_sum);
            }
            return $sum_arr;
        }
        //

        /**
         * @param $average_arr array of returned from get_average function T and P
         * @return array string | example ['T1'=> '12', ..., 'P12' => '13']
         */
        function prepare_post_request ($average_arr) {
            $result = null;
            foreach ($average_arr as $key => $value)
            {
                foreach ($value as $key => $value)
                {

                    $result[$key] = round($value, 2);
                    if($value == 'NULL') { $result[$key] = 'NULL'; }; //If round returns 0 then explicitly set it NULL
                }
            }
            return $result;
        }

        $average_arr =  array_merge(get_averages($post, 'T'), get_sums($post, 'P'));
        $result = prepare_post_request($average_arr);

        //Prepare for insert into ClimateData_TP table
        $result['MeteostationID'] = $post['MeteostationID'];
        $result['Year'] = $post['Year'];

        return $result;


    }

    private function migrate()
    {

        $sql_decades = rtrim($this->generate_columns('DOUBLE DEFAULT NULL'), ',');
        $year = date("Y");

        $sql = "CREATE TABLE IF NOT EXISTS $this->table_name" .
                "(" .
                  "ID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,".
                  "MeteostationID INT(6) DEFAULT NULL," .
                  "Year INT(6) DEFAULT $year," .
                  "$sql_decades" .
                ")";

        $this->db->prepare($sql)->execute();

    }

    /**
     * @param string $action specify insertion parameters
     * @return string $sql_decades | example 'T1_1, P1_1, ..., T12_3,P12_3,'
    */
    private function generate_columns ($action = '')
    {
        $sql_decades = '';
        for($m = 1; $m <= 12; $m++)
        {
            for($d = 1; $d <= 3; $d++)
            {
                $sql_decades .= "T$m" .'_'. "$d $action,\n";
                $sql_decades .= "P$m" .'_'. "$d $action,";
            }
        }
        // !don't change comma (',') in the end it uses in many places
        return $sql_decades;
    }

    /**
     * @param string $year
     */
    private function insert_empty_year($year)
    {
        if($this->is_already_exist($year)) { die('Year already exists in the table, specify another one'); };

        $decades = rtrim($this->generate_columns(), ',');


        $values = $this->helpers->generate_insert($decades, $year);
        $values = rtrim($this->helpers->generate_values($values), ',');


        $query = "INSERT INTO $this->table_name (MeteostationID, Year, $decades)" .
               "VALUES $values";
        $this->db->prepare($query)->execute();

    }

    private function is_already_exist($year)
    {
        $is_exist = true;
        $query = "SELECT * FROM $this->table_name WHERE Year = $year";
        $result = $this->db->query($query);
        if(empty($result->fetchAll())) {
            $is_exist = false;
        }
        return $is_exist;
    }

    /**
     * Replace empty value with NULL
     * example: ['T1_3'-> ''...] converts to ['T1_3' => 'NULL']
     * @Param $dates JSON data
     * @return array $post without empty values
     *
    */
    public function addNull($datas)
    {
        $post = array();
        foreach ($datas as $key => $val){
            (!$val && $val !== 0)? ($post += [$key => "NULL"]) : ($post += [$key => $val]);
        }
        return $post;
    }



}