<?php
//If no values send then don't
($i >= count($arr1)) ? $precipitation['base_year']      = 'null' : '';
($i >= count($arr2)) ? $precipitation['current_year']   = 'null' : '';