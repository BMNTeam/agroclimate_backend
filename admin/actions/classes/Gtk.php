<?php
require_once('../../../include/DB_itit.php');
require_once('Helpers.php');

/**
 *
 *
 */
class Gtk
{
    private $db;
    private $table_name = 'ClimateData_GTK';
    private $helpers;
    private $minimal_temperature = 10;

    function __construct($db)
    {
        $this->db = $db;
        $this->helpers = new Helpers();
    }

    public function get()
    {

    }

    public function set($gtk_str, $year, $meteostation_id)
    {
        if (!$this->year_exists($year)) $this->insert_empty_year($year);

        $query = "UPDATE $this->table_name
                  SET $this->helpers->clear_from_null($gtk_str)
                  WHERE Year=$year AND MeteostationID=$meteostation_id";

        $this->db->prepare($query)->execute();


    }

    /**
     * @param $post array of data to analyze | ex: [Year=2018, MeteostationID = 5, ...]
     * @return void  set values to database using $this->set() function
     */
    public function count_gtk($post)
    {
        $year = $post['Year'];
        $meteostation_id = $post['MeteostationID'];
        /**
         * Get months from April to October
         * @param $post | [
         * @return array grouped by months | [[0] => [P4_1 => 10 .. T4_3 = 22] [1] ...]
         */
        function filter_months($post)
        {
            $months = array();
            for ($m = 4; $m <= 10; $m++) {
                $month = array();
                foreach ($post as $key => $val) {
                    //Check if key starts with P or T and has index equal to permitted
                    if (preg_match("/(P|T)$m.+/", $key)) $month += [$key => $val];
                }
                array_push($months, $month);
                $month = null;

            }
            return $months;
        }

        /**
         * @param $string | string to find month number using Precipitations
         * @return string | boolean months number or false if nothing found
         */
        function get_month_number($string)
        {
            if (preg_match('/P(\d+)_.+/', $string, $month_number)) return $month_number[1];
            return false;
        }

        /**
         * @param $month array of T and P separated by months |
         *        [ [P4_1 => 10 .. T4_3 = 22] ]
         * @param $year
         * @return int|null | number of days in given months
         */
        function get_days_in_month($month, $year)
        {
            $days_in_months = null;
            foreach ($month as $key => $value) {
                //Find number in given key | P1_3 find 1 => P('1')_3
                if ($month_number = get_month_number($key)) {
                    $month_number = (int)trim($month_number);
                    $days_in_months = cal_days_in_month(CAL_GREGORIAN, $month_number, $year);
                    break;
                }

            }
            return $days_in_months;
        }

        /**
         * @param $months array permitted months | ex: [[0] => [P4_1 => 10 .. T4_3 = 22] [1] ...]
         * @param $year
         * @return array | example: [ [0] => [ ['P1_1'] => 10; ['T1_1'] => 4; ['days'] => 10 ] ... [6] =>.. ]
         */
        function separate_by_decades($months, $year)
        {
            $months_by_decades = [];

            foreach ($months as $month => $key) { // $key example [P1_1] => 10

                $days = (int)get_days_in_month($key, $year);

                $first_decade = array_slice($key, 0, 2); // Ex:[ ['P1_1' => 10] ['T1_1']=>4 ]
                $second_decade = array_slice($key, 2, 2);
                $third_decade = array_slice($key, 4, 2);

                $month = [];

                foreach ([$first_decade, $second_decade, $third_decade] as $key => $val) {
                    if ($days >= 13) {
                        $val['days'] = 10;
                        $days -= 10;

                    } else {
                        $val['days'] = $days;
                    }
                    array_push($month, $val); // $month = [ ['P1_1'] => 10; ['T1_1'] => 4; ['days'] => 10; ]
                }
                array_push($months_by_decades, $month);
                // $month_by_decades = [ [0] => [ ['P1_1'] => 10; ['T1_1'] => 4; ['days'] => 10 ]... [6].. ]
            }

            return $months_by_decades;
        }

        $permitted_months = filter_months($post);

        $permitted_months = separate_by_decades($permitted_months, $year);


        /**
         * @param $permitted_months array of months from April till October
         * @param $temperature minimal temperature to calculate GTK
         * @return array of precipitations, temperature and days
         */
        function calculate_gtk($permitted_months, $temperature)
        {
            $gtk_data = [];
            foreach ($permitted_months as $index => $months) { // Foreach month
                $all_filled = true;

                $month_n = null;

                $days = null;
                $temperature_sum = null;
                $precipitations_sum = null;

                foreach ($months as $decade_index => $decade) { //Foreach decade in month

                    foreach ($decade as $key => $value) {
                        if (get_month_number($key)) $month_n = get_month_number($key); //Set month number

                        if ($value === 'NULL') {$all_filled = false; continue;} //Check that all three decades filled

                        //Exit if temperature less than minimal value
                        if (strpos($key, 'T') !== false) if ($value < $temperature) break;

                        //Prepare multiplication temperature to days for numerator in formula
                        if (strpos($key, 'T') !== false) $temperature_sum += $value * $decade['days'];
                        if (strpos($key, 'P') !== false) $precipitations_sum += $value; //Sum precipitations
                        if (strpos($key, 'days') !== false) $days += $value; //Sum days
                    }
                }

                $temperature_ave = round($temperature_sum / $days, 1); //Count average

                //If one of the decades not filled make all month's values NULL
                if (!$all_filled) {$days = 'NULL'; $temperature_ave = 'NULL'; $precipitations_sum = 'NULL';}


                array_push($gtk_data,
                    ["P$month_n" => $precipitations_sum, "T$month_n" => $temperature_ave, "D$month_n" => $days]);

            }

            return $gtk_data;
        }

        $gtk_arr = calculate_gtk($permitted_months, $this->minimal_temperature);

        /**
         * @param $gtk_arr array of the separated by months
         * @return string concatenated string of temperatures, precipitations, days
         */
        function gtk_to_string($gtk_arr)
        {
            $str = null;
            foreach ($gtk_arr as $key => $month)
            {
                foreach ($month as $key => $value)
                {
                    $str .= "$key=$value,";
                }
            }
            return rtrim($str, ',');
        }

        $gtk_str =  gtk_to_string($gtk_arr);
        $this->set($gtk_str, $year, $meteostation_id);

    }


    private function insert_empty_year($year)
    {
        $columns_str = $this->generate_columns();

        $values = $this->helpers->generate_insert($columns_str, $year);
        $values = rtrim($this->helpers->generate_values($values), ',');

        $query = "INSERT INTO $this->table_name (MeteostationID, Year, $columns_str)" .
            "VALUES $values";

        $this->db->prepare($query)->execute();
    }

    /**
     * @return string | 'P4,T4,D4 ... T10,D10'
     */
    private function generate_columns()
    {
        $columns = null;
        for ($p = 4; $p <= 10; $p++) //Months
        {
            foreach (['P', 'T', 'D'] as $key) {
                $columns .= "$key$p,";
            }
        }
        return rtrim($columns, ',');

    }

    private function year_exists($year)
    {
        $is_exist = true;
        $query = "SELECT * FROM $this->table_name WHERE Year = $year";
        $result = $this->db->query($query);
        if (empty($result->fetchAll())) {
            $is_exist = false;
        }
        return $is_exist;
    }


}

