<?

include("config.php");

//функция преобразования кодировок
function win_utf8($s){
    $s= strtr ($s, array ("а"=>"\xD0\xB0", "А"=>"\xD0\x90","б"=>"\xD0\xB1", "Б"=>"\xD0\x91", "в"=>"\xD0\xB2", "В"=>"\xD0\x92", "г"=>"\xD0\xB3", "Г"=>"\xD0\x93", "д"=>"\xD0\xB4", "Д"=>"\xD0\x94", "е"=>"\xD0\xB5", "Е"=>"\xD0\x95", "ё"=>"\xD1\x91", "Ё"=>"\xD0\x81", "ж"=>"\xD0\xB6", "Ж"=>"\xD0\x96", "з"=>"\xD0\xB7", "З"=>"\xD0\x97", "и"=>"\xD0\xB8", "И"=>"\xD0\x98", "й"=>"\xD0\xB9", "Й"=>"\xD0\x99", "к"=>"\xD0\xBA", "К"=>"\xD0\x9A", "л"=>"\xD0\xBB", "Л"=>"\xD0\x9B", "м"=>"\xD0\xBC", "М"=>"\xD0\x9C", "н"=>"\xD0\xBD", "Н"=>"\xD0\x9D", "о"=>"\xD0\xBE", "О"=>"\xD0\x9E", "п"=>"\xD0\xBF", "П"=>"\xD0\x9F", "р"=>"\xD1\x80", "Р"=>"\xD0\xA0", "с"=>"\xD1\x81", "С"=>"\xD0\xA1", "т"=>"\xD1\x82", "Т"=>"\xD0\xA2", "у"=>"\xD1\x83", "У"=>"\xD0\xA3", "ф"=>"\xD1\x84", "Ф"=>"\xD0\xA4", "х"=>"\xD1\x85", "Х"=>"\xD0\xA5", "ц"=>"\xD1\x86", "Ц"=>"\xD0\xA6", "ч"=>"\xD1\x87", "Ч"=>"\xD0\xA7", "ш"=>"\xD1\x88", "Ш"=>"\xD0\xA8", "щ"=>"\xD1\x89", "Щ"=>"\xD0\xA9", "ъ"=>"\xD1\x8A", "Ъ"=>"\xD0\xAA", "ы"=>"\xD1\x8B", "Ы"=>"\xD0\xAB", "ь"=>"\xD1\x8C", "Ь"=>"\xD0\xAC", "э"=>"\xD1\x8D", "Э"=>"\xD0\xAD", "ю"=>"\xD1\x8E", "Ю"=>"\xD0\xAE", "я"=>"\xD1\x8F", "Я"=>"\xD0\xAF"));
    return $s;
}

//функция создания интерактивной диаграммы
function CreatePeriodChart($a_ChartTitle, $a_ChartData, $a_ChartLimits)
{
    $ChartString = "<img src=\"http://chart.apis.google.com/chart?chxl=2:|%D0%93%D0%BE%D0%B4%D1%8B&chxp=2,100&chxs=0,676767,10,0,lt,676767|1,676767,10,-0.5,lt,676767&chxtc=0,5|1,5&chxt=x,y,x&chs=500x400&cht=lc&chco=008000&chg=-1,-1,0,0&chls=3&chts=030000,11.5";
    $CountValues = count($a_ChartData);

    $ChartDataBase = $a_ChartData;

    ksort($a_ChartData);
    $i = 0;
    foreach($a_ChartData as $key=>$value)
    {
        if($i == 1)
            break;

        $FirstValue = $key;
        $i++;
    }

    krsort($a_ChartData);
    $i = 0;
    foreach($a_ChartData as $key=>$value)
    {
        if($i == 1)
            break;

        $LastValue = $key;
        $i++;
    }

    $ChartString .= "&chxr=0,".$FirstValue.",".$LastValue."|1,";

    //поиск минимального значения
    asort($a_ChartData);
    $MinValue = 0;
    $MinValueKey;
    $i = 0;
    foreach($a_ChartData as $key=>$value)
    {
        if($i == 1)
            break;

        $MinValue = $value;
        $MinAxesValue = round($MinValue,0);

        $MinValueKey = $key;

        $i++;
    }

    $AxesMin = $MinValue;
    $AxesMin = floor($AxesMin);

    //поиск максимального значения
    arsort($a_ChartData);
    $MaxValue = 0;
    $MaxValueKey;
    $i = 0;
    foreach($a_ChartData as $key=>$value)
    {
        if($i == 1)
            break;

        $MaxValue = $value;
        $MaxAxesValue = round($MaxValue,0);
        $MaxValueKey = $key;

        $i++;
    }
    $AxesMax = $MaxValue;
    $AxesMax = ceil($AxesMax);

    $AxesDelta = ($AxesMax - $AxesMin) * 0.25;

    $AxesMin -= $AxesDelta;
    $AxesMax += $AxesDelta;
    $AxesMin = floor($AxesMin);
    $AxesMax = ceil($AxesMax);

    $AxesLength = $AxesMax - $AxesMin;
    $ChartString = "";

    $ChartString .= $AxesMin;
    $ChartString .= ",";
    $ChartString .= $AxesMax;

    $ChartDataString = "";

    $ChartDataString .= "&chd=t:";
    $i = 1;
    $ChartDataBaseLen = count($ChartDataBase);
    foreach($ChartDataBase as $key=>$value)
    {
        if($value == $MinValue)
        {
            $MinValuesArray[$i-1] = $key;
        }

        if($value == $MaxValue)
        {
            $MaxValuesArray[$i-1] = $key;
        }
        $CurValue = $value - $AxesMin;
        $CurValue = ($CurValue * 100) / $AxesLength;
        $ChartDataString .= round($CurValue, 1);

        if($i < $ChartDataBaseLen)
        {
            $ChartDataString .= ",";
        }

        $i++;
    }

    $ChartDataString .= "&chm=o,FF0000,0,-1,5";
    if(count($a_ChartLimits) > 0)
    {
        $ChartDataString .= "|";
    }

    foreach($a_ChartLimits as $key=>$value)
    {
        if($value < $AxesMin)
            $value = $AxesMin;

        $CurValue = $value - $AxesMin;

        $ChartLimitsConverted[$key] = round((($CurValue * 100) / $AxesLength) / 100, 2);
    }
    $i = 1;
    $PrevValue = 0;
    foreach($ChartLimitsConverted as $key=>$value)
    {
        $ChartDataString .= "r,";
        $ChartDataString .= $key;
        $ChartDataString .= ",0,";
        $ChartDataString .= $PrevValue;
        $ChartDataString .= ",";
        if($i < count($ChartLimitsConverted))
            $ChartDataString .= $value;
        else
            $ChartDataString .= "1.0";

        $ChartDataString .= ",-1";

        $PrevValue = $value;
        if($i < count($ChartLimitsConverted))
            $ChartDataString .= "|";

        $i++;

    }

    $ChartDataString .= "&chem=";
    foreach($MinValuesArray as $key=>$value)
    {
        if($key > 40)
            $ChartDataString .= "y;s=bubble_text_small;d=bbtr,";
        else
            $ChartDataString .= "y;s=bubble_text_small;d=bbtl,";


        $ChartDataString .= $value;
        $ChartDataString .= ",FFFFFF,000000;ds=0;dp=";
        $ChartDataString .= $key;
        $ChartDataString .= "|";
    }
    $i = 1;
    foreach($MaxValuesArray as $key=>$value)
    {
        if($key > 40)
            $ChartDataString .= "y;s=bubble_text_small;d=bbbr,";
        else
            $ChartDataString .= "y;s=bubble_text_small;d=bb,";


        $ChartDataString .= $value;
        $ChartDataString .= ",FFFFFF,000000;ds=0;dp=";
        $ChartDataString .= $key;

        if($i < count($MaxValuesArray))
        {
            $ChartDataString .= "|";
        }
    }

    $ChartDataString .= "&chtt=";
    $ChartDataString .= $a_ChartTitle;

    $ChartDataString .= "\" width=\"500\" height=\"400\"";
    $ChartDataString .= "\"/>";

    $ChartString .= $ChartDataString;
    echo $ChartString;
}

//функция создания интерактивной столбчатой диаграммы
function CreateInteractiveBarChart($a_DivID, $a_Width, $a_Height, $a_Title, $a_VaxisTitle, $a_HaxisTitle, $a_LegendTitles, $a_ValueTitles, $a_Values1, $a_Values2, $a_Colors)
{
    $CountValues = count($a_Values1);
    echo "<script type=\"text/javascript\">\n";
    echo "google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});\n";
    echo "google.setOnLoadCallback(drawChart);\n";
    echo "function drawChart() {\n";
    echo "var data = new google.visualization.DataTable();\n";
    echo "data.addColumn('string', '$a_LegendTitles[0]');\n";
    echo "data.addColumn('number', '$a_LegendTitles[1]');\n";
    echo "data.addColumn('number', '$a_LegendTitles[2]');\n";
    echo "data.addRows($CountValues);\n";
    for($i = 0; $i < $CountValues; $i++)
    {
        $SetValue = "data.setValue($i, 0, '$a_ValueTitles[$i]');\n";
        $SetValue .= "data.setValue($i, 1, $a_Values1[$i]);\n";
        $SetValue .= "data.setValue($i, 2, $a_Values2[$i]);\n";

        echo $SetValue;
    }

    echo "var chart = new google.visualization.ColumnChart(document.getElementById('$a_DivID'));\n";
    echo "chart.draw(data, {width: $a_Width, height: $a_Height, title: '$a_Title',
				hAxis: {title: '$a_HaxisTitle', titleTextStyle: {color: '#000000'}, textStyle: {color: '#a9a9a9', fontSize: 9}},
				vAxis: {title: '$a_VaxisTitle', titleTextStyle: {color: '#000000'}, textStyle: {color: '#a9a9a9', fontSize: 12}},
				legend: 'bottom',\n";
    $ColorsString = "colors:[";
    for($i=0; $i < count($a_Colors); $i++)
    {
        $ColorsString .= "'".$a_Colors[$i]."'";

        if($i < count($a_Colors) - 1)
        {
            $ColorsString .= ",";
        }
    }
    $ColorsString  .= "]\n";
    echo $ColorsString;
    echo "\n";
    echo "});\n";
    echo "}\n";
    echo "</script>\n";

}

//функция создания интерактивной столбчатой диаграммы расширенная
function CreateInteractiveBarChartExtended($a_DivID, $a_Width, $a_Height, $a_Title, $a_VaxisTitle, $a_HaxisTitle, $a_LegendTitles, $a_ValueTitles, $a_Values1, $a_Values2, $a_Values3, $a_Colors)
{
    $CountValues = count($a_Values1);
    echo "<script type=\"text/javascript\">\n";
    echo "google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});\n";
    echo "google.setOnLoadCallback(drawChart);\n";
    echo "function drawChart() {\n";
    echo "var data = new google.visualization.DataTable();\n";
    echo "data.addColumn('string', '$a_LegendTitles[0]');\n";
    echo "data.addColumn('number', '$a_LegendTitles[1]');\n";
    echo "data.addColumn('number', '$a_LegendTitles[2]');\n";
    echo "data.addColumn('number', '$a_LegendTitles[3]');\n";
    echo "data.addRows($CountValues);\n";
    for($i = 0; $i < $CountValues; $i++)
    {
        $SetValue = "data.setValue($i, 0, '$a_ValueTitles[$i]');\n";
        $SetValue .= "data.setValue($i, 1, $a_Values1[$i]);\n";
        $SetValue .= "data.setValue($i, 2, $a_Values2[$i]);\n";
        $SetValue .= "data.setValue($i, 3, $a_Values3[$i]);\n";

        echo $SetValue;
    }

    echo "var chart = new google.visualization.ColumnChart(document.getElementById('$a_DivID'));\n";
    echo "chart.draw(data, {width: $a_Width, height: $a_Height, title: '$a_Title',
				hAxis: {title: '$a_HaxisTitle', titleTextStyle: {color: '#000000'}, textStyle: {color: '#a9a9a9', fontSize: 9}},
				vAxis: {title: '$a_VaxisTitle', titleTextStyle: {color: '#000000'}, textStyle: {color: '#a9a9a9', fontSize: 12}},
				legend: 'bottom',\n";
    $ColorsString = "colors:[";
    for($i=0; $i < count($a_Colors); $i++)
    {
        $ColorsString .= "'".$a_Colors[$i]."'";

        if($i < count($a_Colors) - 1)
        {
            $ColorsString .= ",";
        }
    }
    $ColorsString  .= "]\n";
    echo $ColorsString;
    echo "\n";
    echo "});\n";
    echo "}\n";
    echo "</script>\n";

}
?>