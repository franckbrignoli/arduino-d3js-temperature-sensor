<?php

require('TemperatureTable.php');

die(json_encode(TemperatureTable::getTodayReport(15)));
