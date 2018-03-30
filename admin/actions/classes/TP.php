<?php

class TP {
    private $table = 'ClimateData_TP';
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param array $post should have 'year_to_edit' and 'select' properties | ex: '[select=1]'
     */
    public function set($post)
    {
        $year = $post['year_to_edit'];
        $meteostation_id = $post['select'];

        /**
         * @param array $post should have Ti and Pi properties | ex: 'T1,T2'
         * @return string $update | example: 'T1=12, ..., P12=32'
         */
        function generate_update($post)
        {
            $update = null;
            for($i=1; $i <= 12; $i++)
            {
                $keyT = "T$i";
                $keyP = "P$i";
                $update .= "$keyP = $post[$keyP],";
                $update .= "$keyT = $post[$keyT],";
            }
            return rtrim($update, ',');
        }
        $update = generate_update($post);

        $query = "UPDATE $this->table
                    SET $update
                  WHERE Year = $year AND MeteostationID = $meteostation_id";

        $this->db-> prepare( $query )->execute();
    }
}
