<?
function Mean($a_Data)
{
    $Count = 0;
    $Summ = 0;
    foreach($a_Data as $key => $val)
    {
        $Summ += $val;
        $Count++;
    }

    return round($Summ/$Count, 2);
}

function STD_Deviation($a_Data, $a_Dispersia = false)
{
    $Mean = Mean($a_Data);

    $Count = 0;
    $Summ = 0;
    foreach($a_Data as $key => $val)
    {
        $Summ += ($val - $Mean)*($val - $Mean);
        $Count++;
    }

    $Dispersia = $Summ / ($Count - 1);

    if($a_Dispersia == false)
    {
        return round(sqrt($Dispersia), 2);
    }

    return round($Dispersia, 2);
}

function GetCount($a_Data)
{
    return count($a_Data);
}

function Correlation($a_Data)
{
    $Count = GetCount($a_Data);

    $CurYear = 1;
    $SummX = 1;
    foreach($a_Data as $key => $val)
    {
        $CorData[$CurYear] = $val;
        $SummX += $CurYear;
        $CurYear++;
    }

    $MeanX = $SummX / $Count;
    $MeanY = Mean($a_Data);
    $Chislitel = 0;
    $Znamenatel1 = 0;
    $Znamenatel2 = 0;
    foreach($CorData as $key => $val)
    {
        $Chislitel += (($key - $MeanX) * ($val - $MeanY));
        $Znamenatel1 += (($key - $MeanX)*($key - $MeanX));
        $Znamenatel2 += (($val - $MeanY)*($val - $MeanY));
    }
    $Znamenatel = $Znamenatel1 * $Znamenatel2;

    $Correlation = $Chislitel / (sqrt($Znamenatel));

    return round($Correlation, 2);

}

function GetSumm($a_Data)
{
    $Summ = 0;
    foreach($a_Data as $key => $val)
    {
        $Summ += $val;
    }

    return $Summ;
}

function Regression($a_Data)
{
    $Count = GetCount($a_Data);

    $CurYear = 1;
    $SummX = 1;
    foreach($a_Data as $key => $val)
    {
        $RegData[$CurYear] = $val;

        $SummX += $CurYear;
        $SummY += $val;

        $XY[$CurYear] = $CurYear * $val;

        $X2[$CurYear] = $CurYear * $CurYear;
        $Y2[$CurYear] = $val * $val;
        $CurYear++;
    }

    $MeanX = $SummX / $Count;
    $MeanY = Mean($a_Data);

    $SummY = GetSumm($RegData);
    $SummXY = GetSumm($XY);
    $SummX2 = GetSumm($X2);
    $SummY2 = GetSumm($Y2);

    //calc b
    $b = ($Count*$SummXY - ($SummX * $SummY)) / (($Count * $SummX2) - ($SummX * $SummX));

    $a = $MeanY - $b * $MeanX;

    $b = round($b, 3);
    $a = round($a, 3);

    $Correlation = Correlation($a_Data);
    $Correlation *= $Correlation;

    return "y = ".$b."*x + ".$a." Коэффициент аппроксимации = ".$Correlation;


}

function CountProb($a_Mean, $STD_Dev, $a_Value)
{
    $ValueZ = ($a_Value - $a_Mean) / $STD_Dev;


    if($ValueZ < 0.0)
    {
        $ValueZ = -$ValueZ;
    }

    $ValueZ = round($ValueZ, 2);
    $Prob = SearchZ($ValueZ);

    if($Prob == -9999)
    {
        $Prob = 0;
    }

    if($a_Value < $a_Mean)
    {
        $Prob = 100 - $Prob;

    }



    return $Prob;
}

function SearchZ($a_valueZ)
{
    $ValuesZ["0.0"] = 0.5;
    $ValuesZ["0.01"] = 0.496;
    $ValuesZ["0.02"] = 0.492;
    $ValuesZ["0.03"] = 0.488;
    $ValuesZ["0.04"] = 0.484;
    $ValuesZ["0.05"] = 0.4801;
    $ValuesZ["0.06"] = 0.4761;
    $ValuesZ["0.07"] = 0.4721;
    $ValuesZ["0.08"] = 0.4681;
    $ValuesZ["0.09"] = 0.4641;
    $ValuesZ["0.1"] = 0.4602;
    $ValuesZ["0.11"] = 0.4562;
    $ValuesZ["0.12"] = 0.4522;
    $ValuesZ["0.13"] = 0.4483;
    $ValuesZ["0.14"] = 0.4443;
    $ValuesZ["0.15"] = 0.4404;
    $ValuesZ["0.16"] = 0.4364;
    $ValuesZ["0.17"] = 0.4325;
    $ValuesZ["0.18"] = 0.4286;
    $ValuesZ["0.19"] = 0.4247;
    $ValuesZ["0.2"] = 0.4207;
    $ValuesZ["0.21"] = 0.4168;
    $ValuesZ["0.22"] = 0.4129;
    $ValuesZ["0.23"] = 0.409;
    $ValuesZ["0.24"] = 0.4052;
    $ValuesZ["0.25"] = 0.4013;
    $ValuesZ["0.26"] = 0.3974;
    $ValuesZ["0.27"] = 0.3936;
    $ValuesZ["0.28"] = 0.3897;
    $ValuesZ["0.29"] = 0.3859;
    $ValuesZ["0.3"] = 0.3821;
    $ValuesZ["0.31"] = 0.3783;
    $ValuesZ["0.32"] = 0.3745;
    $ValuesZ["0.33"] = 0.3707;
    $ValuesZ["0.34"] = 0.3669;
    $ValuesZ["0.35"] = 0.3632;
    $ValuesZ["0.36"] = 0.3594;
    $ValuesZ["0.37"] = 0.3557;
    $ValuesZ["0.38"] = 0.352;
    $ValuesZ["0.39"] = 0.3483;
    $ValuesZ["0.4"] = 0.3446;
    $ValuesZ["0.41"] = 0.3409;
    $ValuesZ["0.42"] = 0.3372;
    $ValuesZ["0.43"] = 0.3336;
    $ValuesZ["0.44"] = 0.33;
    $ValuesZ["0.45"] = 0.3264;
    $ValuesZ["0.46"] = 0.3228;
    $ValuesZ["0.47"] = 0.3192;
    $ValuesZ["0.48"] = 0.3156;
    $ValuesZ["0.49"] = 0.3121;
    $ValuesZ["0.5"] = 0.3085;
    $ValuesZ["0.51"] = 0.305;
    $ValuesZ["0.52"] = 0.3015;
    $ValuesZ["0.53"] = 0.2981;
    $ValuesZ["0.54"] = 0.2946;
    $ValuesZ["0.55"] = 0.2912;
    $ValuesZ["0.56"] = 0.2877;
    $ValuesZ["0.57"] = 0.2843;
    $ValuesZ["0.58"] = 0.281;
    $ValuesZ["0.59"] = 0.2776;
    $ValuesZ["0.6"] = 0.2743;
    $ValuesZ["0.61"] = 0.2709;
    $ValuesZ["0.62"] = 0.2676;
    $ValuesZ["0.63"] = 0.2643;
    $ValuesZ["0.64"] = 0.2611;
    $ValuesZ["0.65"] = 0.2578;
    $ValuesZ["0.66"] = 0.2546;
    $ValuesZ["0.67"] = 0.2514;
    $ValuesZ["0.68"] = 0.2483;
    $ValuesZ["0.69"] = 0.2451;
    $ValuesZ["0.7"] = 0.242;
    $ValuesZ["0.71"] = 0.2389;
    $ValuesZ["0.72"] = 0.2358;
    $ValuesZ["0.73"] = 0.2327;
    $ValuesZ["0.74"] = 0.2296;
    $ValuesZ["0.75"] = 0.2266;
    $ValuesZ["0.76"] = 0.2236;
    $ValuesZ["0.77"] = 0.2206;
    $ValuesZ["0.78"] = 0.2177;
    $ValuesZ["0.79"] = 0.2148;
    $ValuesZ["0.8"] = 0.2119;
    $ValuesZ["0.81"] = 0.209;
    $ValuesZ["0.82"] = 0.2061;
    $ValuesZ["0.83"] = 0.2033;
    $ValuesZ["0.84"] = 0.2005;
    $ValuesZ["0.85"] = 0.1977;
    $ValuesZ["0.86"] = 0.1949;
    $ValuesZ["0.87"] = 0.1922;
    $ValuesZ["0.88"] = 0.1894;
    $ValuesZ["0.89"] = 0.1867;
    $ValuesZ["0.9"] = 0.1841;
    $ValuesZ["0.91"] = 0.1814;
    $ValuesZ["0.92"] = 0.1788;
    $ValuesZ["0.93"] = 0.1762;
    $ValuesZ["0.94"] = 0.1736;
    $ValuesZ["0.95"] = 0.1711;
    $ValuesZ["0.96"] = 0.1685;
    $ValuesZ["0.97"] = 0.1660;
    $ValuesZ["0.98"] = 0.1635;
    $ValuesZ["0.99"] = 0.1611;
    $ValuesZ["1.0"] = 0.1587;
    $ValuesZ["1.01"] = 0.1562;
    $ValuesZ["1.02"] = 0.1539;
    $ValuesZ["1.03"] = 0.1515;
    $ValuesZ["1.04"] = 0.1492;
    $ValuesZ["1.05"] = 0.1469;
    $ValuesZ["1.06"] = 0.1446;
    $ValuesZ["1.07"] = 0.1423;
    $ValuesZ["1.08"] = 0.1401;
    $ValuesZ["1.09"] = 0.1379;
    $ValuesZ["1.1"] = 0.1357;
    $ValuesZ["1.11"] = 0.1335;
    $ValuesZ["1.12"] = 0.1314;
    $ValuesZ["1.13"] = 0.1292;
    $ValuesZ["1.14"] = 0.1271;
    $ValuesZ["1.15"] = 0.1251;
    $ValuesZ["1.16"] = 0.1230;
    $ValuesZ["1.17"] = 0.1210;
    $ValuesZ["1.18"] = 0.1190;
    $ValuesZ["1.19"] = 0.1170;
    $ValuesZ["1.2"] = 0.1151;
    $ValuesZ["1.21"] = 0.1131;
    $ValuesZ["1.22"] = 0.1112;
    $ValuesZ["1.23"] = 0.1093;
    $ValuesZ["1.24"] = 0.1075;
    $ValuesZ["1.25"] = 0.1056;
    $ValuesZ["1.26"] = 0.1038;
    $ValuesZ["1.27"] = 0.1020;
    $ValuesZ["1.28"] = 0.1003;
    $ValuesZ["1.29"] = 0.0985;
    $ValuesZ["1.3"] = 0.0968;
    $ValuesZ["1.31"] = 0.0951;
    $ValuesZ["1.32"] = 0.0934;
    $ValuesZ["1.33"] = 0.0918;
    $ValuesZ["1.34"] = 0.0901;
    $ValuesZ["1.35"] = 0.0885;
    $ValuesZ["1.36"] = 0.0869;
    $ValuesZ["1.37"] = 0.0853;
    $ValuesZ["1.38"] = 0.0838;
    $ValuesZ["1.39"] = 0.0823;
    $ValuesZ["1.4"] = 0.0808;
    $ValuesZ["1.41"] = 0.0793;
    $ValuesZ["1.42"] = 0.0778;
    $ValuesZ["1.43"] = 0.0764;
    $ValuesZ["1.44"] = 0.0749;
    $ValuesZ["1.45"] = 0.0735;
    $ValuesZ["1.46"] = 0.0721;
    $ValuesZ["1.47"] = 0.0708;
    $ValuesZ["1.48"] = 0.0694;
    $ValuesZ["1.49"] = 0.0681;
    $ValuesZ["1.5"] = 0.0668;
    $ValuesZ["1.51"] = 0.0655;
    $ValuesZ["1.52"] = 0.0643;
    $ValuesZ["1.53"] = 0.063;
    $ValuesZ["1.54"] = 0.0618;
    $ValuesZ["1.55"] = 0.0606;
    $ValuesZ["1.56"] = 0.0594;
    $ValuesZ["1.57"] = 0.0582;
    $ValuesZ["1.58"] = 0.0571;
    $ValuesZ["1.59"] = 0.0559;
    $ValuesZ["1.6"] = 0.0548;
    $ValuesZ["1.61"] = 0.0537;
    $ValuesZ["1.62"] = 0.0526;
    $ValuesZ["1.63"] = 0.0516;
    $ValuesZ["1.64"] = 0.0505;
    $ValuesZ["1.65"] = 0.0495;
    $ValuesZ["1.66"] = 0.0485;
    $ValuesZ["1.67"] = 0.0475;
    $ValuesZ["1.68"] = 0.0465;
    $ValuesZ["1.69"] = 0.0455;
    $ValuesZ["1.7"] = 0.0446;
    $ValuesZ["1.71"] = 0.0436;
    $ValuesZ["1.72"] = 0.0427;
    $ValuesZ["1.73"] = 0.0418;
    $ValuesZ["1.74"] = 0.0409;
    $ValuesZ["1.75"] = 0.0401;
    $ValuesZ["1.76"] = 0.0392;
    $ValuesZ["1.77"] = 0.0384;
    $ValuesZ["1.78"] = 0.0375;
    $ValuesZ["1.79"] = 0.0367;
    $ValuesZ["1.8"] = 0.0359;
    $ValuesZ["1.81"] = 0.0351;
    $ValuesZ["1.82"] = 0.0344;
    $ValuesZ["1.83"] = 0.0336;
    $ValuesZ["1.84"] = 0.0329;
    $ValuesZ["1.85"] = 0.0322;
    $ValuesZ["1.86"] = 0.0314;
    $ValuesZ["1.87"] = 0.0307;
    $ValuesZ["1.88"] = 0.0301;
    $ValuesZ["1.89"] = 0.0294;
    $ValuesZ["1.9"] = 0.0287;
    $ValuesZ["1.91"] = 0.0281;
    $ValuesZ["1.92"] = 0.0274;
    $ValuesZ["1.93"] = 0.0268;
    $ValuesZ["1.94"] = 0.0262;
    $ValuesZ["1.95"] = 0.0256;
    $ValuesZ["1.96"] = 0.025;
    $ValuesZ["1.97"] = 0.0244;
    $ValuesZ["1.98"] = 0.0239;
    $ValuesZ["1.99"] = 0.0233;
    $ValuesZ["2.0"] = 0.0228;
    $ValuesZ["2.01"] = 0.0222;
    $ValuesZ["2.02"] = 0.0217;
    $ValuesZ["2.03"] = 0.0212;
    $ValuesZ["2.04"] = 0.0207;
    $ValuesZ["2.05"] = 0.0202;
    $ValuesZ["2.06"] = 0.0197;
    $ValuesZ["2.07"] = 0.0192;
    $ValuesZ["2.08"] = 0.0188;
    $ValuesZ["2.09"] = 0.0183;
    $ValuesZ["2.1"] = 0.0179;
    $ValuesZ["2.11"] = 0.0174;
    $ValuesZ["2.12"] = 0.017;
    $ValuesZ["2.13"] = 0.0166;
    $ValuesZ["2.14"] = 0.0162;
    $ValuesZ["2.15"] = 0.0158;
    $ValuesZ["2.16"] = 0.0154;
    $ValuesZ["2.17"] = 0.015;
    $ValuesZ["2.18"] = 0.0146;
    $ValuesZ["2.19"] = 0.0143;
    $ValuesZ["2.2"] = 0.0139;
    $ValuesZ["2.21"] = 0.0136;
    $ValuesZ["2.22"] = 0.0132;
    $ValuesZ["2.23"] = 0.0129;
    $ValuesZ["2.24"] = 0.0125;
    $ValuesZ["2.25"] = 0.0122;
    $ValuesZ["2.26"] = 0.0119;
    $ValuesZ["2.27"] = 0.0116;
    $ValuesZ["2.28"] = 0.0113;
    $ValuesZ["2.29"] = 0.011;
    $ValuesZ["2.3"] = 0.0107;
    $ValuesZ["2.31"] = 0.0104;
    $ValuesZ["2.32"] = 0.0102;
    $ValuesZ["2.33"] = 0.0099;
    $ValuesZ["2.34"] = 0.0096;
    $ValuesZ["2.35"] = 0.0094;
    $ValuesZ["2.36"] = 0.0091;
    $ValuesZ["2.37"] = 0.0089;
    $ValuesZ["2.38"] = 0.0087;
    $ValuesZ["2.39"] = 0.0084;
    $ValuesZ["2.4"] = 0.0082;
    $ValuesZ["2.41"] = 0.008;
    $ValuesZ["2.42"] = 0.0078;
    $ValuesZ["2.43"] = 0.0075;
    $ValuesZ["2.44"] = 0.0073;
    $ValuesZ["2.45"] = 0.0071;
    $ValuesZ["2.46"] = 0.0069;
    $ValuesZ["2.47"] = 0.0068;
    $ValuesZ["2.48"] = 0.0066;
    $ValuesZ["2.49"] = 0.0064;
    $ValuesZ["2.5"] = 0.0062;
    $ValuesZ["2.51"] = 0.006;
    $ValuesZ["2.52"] = 0.0059;
    $ValuesZ["2.53"] = 0.0057;
    $ValuesZ["2.54"] = 0.0055;
    $ValuesZ["2.55"] = 0.0054;
    $ValuesZ["2.56"] = 0.0052;
    $ValuesZ["2.57"] = 0.0051;
    $ValuesZ["2.58"] = 0.0049;
    $ValuesZ["2.59"] = 0.0048;
    $ValuesZ["2.6"] = 0.0047;
    $ValuesZ["2.61"] = 0.0045;
    $ValuesZ["2.62"] = 0.0044;
    $ValuesZ["2.63"] = 0.0043;
    $ValuesZ["2.64"] = 0.0041;
    $ValuesZ["2.65"] = 0.004;
    $ValuesZ["2.66"] = 0.0039;
    $ValuesZ["2.67"] = 0.0038;
    $ValuesZ["2.68"] = 0.0037;
    $ValuesZ["2.69"] = 0.0036;
    $ValuesZ["2.7"] = 0.0035;
    $ValuesZ["2.71"] = 0.0034;
    $ValuesZ["2.72"] = 0.0033;
    $ValuesZ["2.73"] = 0.0032;
    $ValuesZ["2.74"] = 0.0031;
    $ValuesZ["2.75"] = 0.003;
    $ValuesZ["2.76"] = 0.0029;
    $ValuesZ["2.77"] = 0.0028;
    $ValuesZ["2.78"] = 0.0027;
    $ValuesZ["2.79"] = 0.0026;
    $ValuesZ["2.8"] = 0.0026;
    $ValuesZ["2.81"] = 0.0025;
    $ValuesZ["2.82"] = 0.0024;
    $ValuesZ["2.83"] = 0.0023;
    $ValuesZ["2.84"] = 0.0023;
    $ValuesZ["2.85"] = 0.0022;
    $ValuesZ["2.86"] = 0.0021;
    $ValuesZ["2.87"] = 0.0021;
    $ValuesZ["2.88"] = 0.002;
    $ValuesZ["2.89"] = 0.0019;
    $ValuesZ["2.9"] = 0.0019;
    $ValuesZ["2.91"] = 0.0018;
    $ValuesZ["2.92"] = 0.0018;
    $ValuesZ["2.93"] = 0.0017;
    $ValuesZ["2.94"] = 0.0016;
    $ValuesZ["2.95"] = 0.0016;
    $ValuesZ["2.96"] = 0.0015;
    $ValuesZ["2.97"] = 0.0015;
    $ValuesZ["2.98"] = 0.0014;
    $ValuesZ["2.99"] = 0.0014;
    $ValuesZ["3.0"] = 0.0013;
    $ValuesZ["3.01"] = 0.0013;
    $ValuesZ["3.02"] = 0.0013;
    $ValuesZ["3.03"] = 0.0012;
    $ValuesZ["3.04"] = 0.0012;
    $ValuesZ["3.05"] = 0.0011;
    $ValuesZ["3.06"] = 0.0011;
    $ValuesZ["3.07"] = 0.0011;
    $ValuesZ["3.08"] = 0.001;
    $ValuesZ["3.09"] = 0.001;
    $ValuesZ["3.1"] = 0.001;
    $ValuesZ["3.2"] = 0.0007;
    $ValuesZ["3.3"] = 0.0005;
    $ValuesZ["3.4"] = 0.0003;
    $ValuesZ["3.5"] = 0.0002;
    $ValuesZ["3.6"] = 0.0002;
    $ValuesZ["3.7"] = 0.0001;



    foreach($ValuesZ as $key => $val)
    {
        if($key == $a_valueZ)
        {
            //$val = 1 - $val;
            $val *= 100;

            return round($val, 0);

        }
    }

    return -9999;
}

?>