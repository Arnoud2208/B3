<?php
$hash = '$2y$10$PlY2C5tIc0M9XhqTmilXKOuj4KYSNe6NmXzL17cpupUf6iLznOAHm';

var_dump(password_verify('test123', $hash));