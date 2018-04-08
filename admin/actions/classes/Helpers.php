<?php
class Helpers {
    /**
     * @param string $columns database columns
     * @param string $year to insert
     * @return array of decades separated by meteostation_id | example: ['1, 2018, NULL, NULL...']
     */
    public function generate_insert($columns, $year)
    {
        $columns_arr = explode(',', $columns);

        $sql_str = null;
        $sql_array = Array();

        for($i = 1; $i <= 16; $i++)
        {
            $sql_str .= "$i, $year,";
            foreach ($columns_arr as $column)
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
    public function generate_values($values_arr)
    {
        $values_str = '';
        //Wrap values in brackets
        foreach ($values_arr as $key => $value) {
            $values_str .= "(" . "$value" .")";
            $values_str .= ",";
        }
        return $values_str;
    }
}