<?
$months = Array('Январь', "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
$month_index = 1; ?>
<? $months_by_quarters = array_chunk($months, 3); ?>
<? foreach ($months_by_quarters as $quarter): ?>
    <div class="row">
        <? foreach ($quarter as $month): ?>
            <div class="col-md-4">
                <table class="table table-hover table-responsive <? echo $switch['class'] ?>">
                    <thead>
                    <tr>
                        <th>Месяц</th>
                        <th>I дек</th>
                        <th>II дек</th>
                        <th>III дек</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><? echo $month ?></td>

                        <td><input class="input-editable" type="text"
                                   value="<?php echo $decades_data[ $switch['label'] .($month_index )."_1"]; ?>"
                                   name="<? echo($switch['label'].($month_index).'_1') ;?>"</td>
                        <td><input class="input-editable" type="text"
                                   value="<?php echo $decades_data[ $switch['label'] .($month_index )."_2"]; ?>"
                                   name="<? echo($switch['label'] .($month_index).'_2') ;?>"</td>
                        <td><input class="input-editable" type="text"
                                   value="<?php echo $decades_data[ $switch['label'] .($month_index )."_3"]; ?>"
                                   name="<? echo($switch['label'] .($month_index).'_3') ;?>"</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <? $month_index++?>
        <? endforeach; ?>
    </div>
<?php endforeach; ?>