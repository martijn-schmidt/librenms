<?php

/*
 * LibreNMS OS Polling module for the Stulz GmbH Klimatechnik WIB 8000
 *
 * © 2019 Martijn Schmidt <martijn.schmidt@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */

$hardware        = 'WIB 8000';
$version         = 'v'.snmp_get($device, 'wibFirmware.0', '-Oqv', 'Stulz-WIB8000-MIB');
$serial          = '';

// Define all the hardware recognized by unitsettingHwType in Stulz-WIB8000-MIB.
$unitsettingHwType_array = [
    0 => 'unknown',
    1 => 'C4000',
    2 => 'C1001',
    3 => 'C1002',
    4 => 'C5000',
    5 => 'C6000',
    6 => 'C1010',
    7 => 'C7000IOC',
    8 => 'C7000AT',
    9 => 'C7000PT',
    10 => 'C5MSC',
    11 => 'C7000PT2',
    12 => 'C2020',
    13 => 'C100',
    14 => 'C102',
    15 => 'C103',
];

// Define all the families recognized by unitsettingFamily in Stulz-WIB8000-MIB.
// 0 => 'invalid' was removed since we don't want to show it in $features.
$unitsettingFamily_array = [
    1 => 'CyberAir',
    2 => 'CyberAir 2',
    3 => 'CyberAir 3',
    4 => 'Minispace',
    5 => 'Compact Plus',
    6 => 'CyberCool Indoor',
    7 => 'CyberCool Outdoor',
    8 => 'CyberCool Pumpstation',
    9 => 'CyberRow',
    10 => 'CyberRow Small',
    11 => 'Airbooster',
    12 => 'Airmodulator',
    13 => 'Eco-Air',
    14 => 'Free-Air',
    15 => 'Predator',
    16 => 'Prodigy',
];

// Define all the types recognized by unitsettingFamily in Stulz-WIB8000-MIB.
$unitsettingType_array = [
    0 => 'MC',
    1 => 'DX',
    2 => 'CW',
    3 => 'CH',
    4 => 'ECO-COOL',
    5 => 'MSC',
    6 => 'GE1',
    7 => 'GE2',
    8 => 'Dualfluid',
    9 => 'CW2',
    10 => 'CHD',
    11 => 'CHP',
    12 => 'FAU',
    13 => 'CPP',
    14 => 'Predator',
    15 => 'Prodigy',
    16 => 'ENS',
    17 => 'CyberRow A',
    18 => 'CyberRow CW',
    19 => 'CyberRow G',
    255 => 'unknown',
];

$unitTable_array = snmpwalk_cache_multi_oid($device, 'unitTable', $unitTable_array, 'Stulz-WIB8000-MIB');
$sWVersion_array = snmpwalk_cache_multi_oid($device, 'sWVersion', $sWVersion_array, 'Stulz-WIB8000-MIB');

foreach ($unitTable_array as $index => $unitTable) {
    isset($features) ? $features = rtrim($features).', ' : $features = '';
    $features .= 'Unit '.$index.' ';
    if(isset($unitsettingFamily_array[$unitTable['unitsettingFamily']])) {
        $features .= $unitsettingFamily_array[$unitTable['unitsettingFamily']].' ';
    }
    if(isset($unitsettingHwType_array[$unitTable['unitsettingHwType']])) {
        $features .= $unitsettingHwType_array[$unitTable['unitsettingHwType']].' ';
    }
    if(isset($unitsettingType_array[$unitTable['unitsettingType']])) {
        $features .= $unitsettingType_array[$unitTable['unitsettingType']].' ';
    }
    if(isset($sWVersion_array["$index.1"]['sWVersion'])) {
        echo $sWVersion_array["$index.1"]['sWVersion'];
        $features .= 'v'.substr_replace($sWVersion_array["$index.1"]['sWVersion'], '.', 1, 0).' ';
    }
}

isset($features) ? $features = trim($features) : $features = '';
