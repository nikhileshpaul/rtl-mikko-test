<?php

$filename = 'months.csv';
$rows = array_map('str_getcsv', file($filename));
$header = array_shift($rows);
$csv = [];
foreach ($rows as $row) {
    $csv[] = array_combine($header, $row);
}
$weekends = ['Saturday', 'Sunday'];
$bonusDays = [];
$payDays = [];

//CSV put data
$newData = [];
$i = 1;
$newData[0] = ['Month', 'BonusDay', 'PayDay', 'ProcessedStatus'];

foreach ($csv as $data) {
    if (strtolower(trim($data['ProcessedStatus'])) === 'no') {
        //Bonus day: Find 15th day of the month
        $fifteenth = date('l', strtotime('15-' . strtolower($data['Month']) . date('Y')));
        if (in_array($fifteenth, $weekends)) {
            //Pay bonus on next Wednesday
            $bonusDay = date('d-m-Y', strtotime('next Wednesday', strtotime('15-' . strtolower($data['Month']) . date('Y'))));
        } else {
            //Pay bonus on this day
            $bonusDay = date('d-m-Y', strtotime('15-' . strtolower($data['Month']) . date('Y')));
        }
        //Payday: Find last working weekday
        $lastDay = date('l', strtotime('last day '. date('F Y', strtotime('next month '. strtolower($data['Month']) . ' ' . date('Y')))));
        if (in_array($lastDay, $weekends)) {
            //Pay on last working day
            $payDay = date('d-m-Y', strtotime('last weekday '. date('F Y', strtotime('next month '. strtolower($data['Month']) . ' ' . date('Y')))));
        } else {
            //Payday
            $payDay = date('d-m-Y', strtotime('last day '. date('F Y', strtotime('next month '. strtolower($data['Month']) . ' ' . date('Y')))));
        }
        $newData[$i] = [$data['Month'], $bonusDay, $payDay, 'Yes'];
    } else {
        $newData[$i] = [$data['Month'], $data['BonusDay'], $data['PayDay'], 'Yes'];
    }
    $i++;
}

$fileopen = fopen($filename, 'w');

if ($fileopen !== FALSE) {
    foreach ($newData as $newRow) {
        fputcsv($fileopen, $newRow);
    }
    echo "CSV updated";
} else {
    echo "Error with CSV file";
}

?>