<?php
include('TP.php');

class Decades {
    private $table_name = "ClimateDataDecade_TP";
    private $db;


    public function __construct($db)
    {
        $this->db = $db;
        //$this->migrate();
        //$this->insert_empty_year(2018);
    }

    public function get($meteostation_id, $year)
    {
        if(!$meteostation_id || !$year) return;

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
        $query = "UPDATE $this->table_name " .
                 "SET $set_sting " .
                 "WHERE Year = $year AND MeteostationID = $meteostation_id";
        $this->db->prepare($query)->execute();

        $tp_table = new TP($this->db);
        $tp_table->set($this->count_average($post));
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
         * @return array of elements to insert | ex: [T1 => 2, P1=> 3, ... , P12 => 16]
         */
        function get_average($post, $param){
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

        $average_arr =  array_merge(get_average($post, 'T'), get_average($post, 'P'));
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

        /**
         * @param string $decades of decades
         * @param string $year to insert
         * @return array of decades separated by meteostation_id | example: ['1, 2018, NULL, NULL...']
         */
        function generate_insert($decades, $year)
        {
            $decades = explode(',', $decades);

            $sql_str = null;
            $sql_array = Array();

            for($i = 1; $i <= 16; $i++)
            {
                $sql_str .= "$i, $year,";
                foreach ($decades as $decade)
                {
                    $sql_str .= "NULL,";
                }
                // pushed item example: '1, 2020, NULL ... NULL'
                array_push($sql_array, rtrim($sql_str, ','));
                $sql_str = null;
                
            }
            return $sql_array;
        }

        /**
         * @param array $values_arr
         * @return string $values_str | example: '(1, 2020, NULL, ... NULL),(...),'
         */
        function generate_values($values_arr)
        {
            $values_str = '';
            //Wrap values in brackets
            foreach ($values_arr as $key => $value) {
                $values_str .= "(" . "$value" .")";
                $values_str .= ",";
            }
            return $values_str;
        }
        $values = generate_insert($decades, $year);
        $values = rtrim(generate_values($values), ',');


        $sql = "INSERT INTO $this->table_name (MeteostationID, Year, $decades)" .
               "VALUES $values";
        $this->db->prepare($sql)->execute();

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
            (!$val)? ($post += [$key => "NULL"]) : ($post += [$key => $val]);
        }
        return $post;
    }



}