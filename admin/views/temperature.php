<!--Температура-->
<? $decades = new Decades($db);
$decades_data = $decades->get($_GET['select'], $_GET['year_to_edit'])[0]; ?>
<?php if (!empty($resultDataToAnalyse) || !empty($decades_data)): ?>
    <div class="col-md-12">
    <h2>Температура</h2>
    <? if (empty($decades_data)): ?>
        <table class="table table-hover table-responsive table-temperature">
        <thead>
        <tr>
            <th>Год</th>
            <th>Январь</th>
            <th>Февраль</th>
            <th>Март</th>
            <th>Апрель</th>
            <th>Май</th>
            <th>Июнь</th>
            <th>Июль</th>
            <th>Август</th>
            <th>Сентябрь</th>
            <th>Октябрь</th>
            <th>Ноябрь</th>
            <th>Декабрь</th>
            <th>Среднее</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$isEditable): ?>
            <?php foreach ($resultDataToAnalyse as $key => $value): ?>
                <tr>

                    <td><?php echo $value['Year']; ?></td>
                    <td><?php echo $value['T1']; ?></td>
                    <td><?php echo $value['T2']; ?></td>
                    <td><?php echo $value['T3']; ?></td>
                    <td><?php echo $value['T4']; ?></td>
                    <td><?php echo $value['T5']; ?></td>
                    <td><?php echo $value['T6']; ?></td>
                    <td><?php echo $value['T7']; ?></td>
                    <td><?php echo $value['T8']; ?></td>
                    <td><?php echo $value['T9']; ?></td>
                    <td><?php echo $value['T10']; ?></td>
                    <td><?php echo $value['T11']; ?></td>
                    <td><?php echo $value['T12']; ?></td>
                    <td><?php
                        $sum = 0;
                        for ($i = 1; $i <= 12; $i++) {
                            $sum += $value['T' . $i];

                        }
                        echo round($sum / 12, 1);
                        ?></td>


                </tr>
                </tbody>
                </table>

            <?php endforeach; ?>
        <?php elseif ($isEditable && empty($decades_data)): ?>

            <?php foreach ($resultDataToAnalyse as $key => $value): ?>

                <tr>
                    <td><?php echo $value['Year']; ?></td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T1']; ?>" name="T1"</td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T2']; ?>" name="T2">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T3']; ?>" name="T3">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T4']; ?>" name="T4">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T5']; ?>" name="T5">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T6']; ?>" name="T6">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T7']; ?>" name="T7">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T8']; ?>" name="T8">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T9']; ?>" name="T9">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T10']; ?>" name="T10">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T11']; ?>" name="T11">
                    </td>
                    <td><input class="input-editable" type="text" value="<?php echo $value['T12']; ?>" name="T12">
                        <input type="hidden" name="MeteostationID" value="<?php echo $value['MeteostationID']; ?>"
                    </td>

                </tr>
            <?php endforeach; ?>
            </tbody>
            </table>

        <? endif ?>
    <? elseif ($isEditable && !empty($decades_data)): ?>
        <?
        // Needs to define T and css class in included file, ex: 'T1_1'
        $switch = array('label' => 'T', 'class' => 'table-temperature');
        include('temperature_decades.php')
        ?>
    <?php endif; ?>
    </div>

    <!--Осадки-->
    <div class="col-md-12">
        <h2>Осадки</h2>
        <? if (empty($decades_data)): ?>
            <table class="table table-hover table-responsive precipitation-table">
                <thead>
                <tr>
                    <th>Год</th>
                    <th>Январь</th>
                    <th>Февраль</th>
                    <th>Март</th>
                    <th>Апрель</th>
                    <th>Май</th>
                    <th>Июнь</th>
                    <th>Июль</th>
                    <th>Август</th>
                    <th>Сентябрь</th>
                    <th>Октябрь</th>
                    <th>Ноябрь</th>
                    <th>Декабрь</th>
                    <th style="white-space: pre">Сумма</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$isEditable): ?>
                    <?php foreach ($resultDataToAnalyse as $key => $value): ?>
                        <tr>

                            <td><?php echo $value['Year']; ?></td>
                            <td><?php echo $value['P1']; ?></td>
                            <td><?php echo $value['P2']; ?></td>
                            <td><?php echo $value['P3']; ?></td>
                            <td><?php echo $value['P4']; ?></td>
                            <td><?php echo $value['P5']; ?></td>
                            <td><?php echo $value['P6']; ?></td>
                            <td><?php echo $value['P7']; ?></td>
                            <td><?php echo $value['P8']; ?></td>
                            <td><?php echo $value['P9']; ?></td>
                            <td><?php echo $value['P10']; ?></td>
                            <td><?php echo $value['P11']; ?></td>
                            <td><?php echo $value['P12']; ?></td>
                            <td><?php
                                $sum = 0;
                                for ($i = 1; $i <= 12; $i++) {
                                    $sum += $value['P' . $i];

                                }
                                echo $sum;
                                ?></td>


                        </tr>

                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($resultDataToAnalyse as $key => $value): ?>
                        <tr>

                            <td><?php echo $value['Year']; ?></td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P1']; ?>" name="P1"
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P2']; ?>" name="P2">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P3']; ?>" name="P3">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P4']; ?>" name="P4">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P5']; ?>" name="P5">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P6']; ?>" name="P6">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P7']; ?>" name="P7">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P8']; ?>" name="P8">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P9']; ?>" name="P9">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P10']; ?>"
                                       name="P10">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P11']; ?>"
                                       name="P11">
                            </td>
                            <td><input class="input-editable" type="text" value="<?php echo $value['P12']; ?>"
                                       name="P12">
                            </td>

                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
                </tbody>
            </table>

        <? elseif ($isEditable && !empty($decades_data)) : // if decades data not empty?>
            <?
            // Needs to define P and css class in included file, ex: 'P1_1'
            $switch = array('label' => 'P', 'class' => 'precipitation-table');
            include('temperature_decades.php') ?>
        <? endif ?>
    </div>


<?php else: ?>
    <div class="row">
        <div class="container">
            <div class="col-md-12">
                <h3>Год отсутствует в базе данных</h3>
            </div>
        </div>
    </div>
<?php endif; ?>

