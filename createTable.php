<?php
foreach ($maxAgePersons as $person) {
    echo '<tr>';
    foreach ($person as $value) {
        echo '<td>'.$value.'</td>';
    }
    echo '<tr>';
}