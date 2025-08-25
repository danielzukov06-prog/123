<?php
function valid_email(string $email): bool {
return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


// Eesti isikukoodi kontroll (pikkus + kontrollnumber)
function valid_isikukood(string $code): bool {
if (!preg_match('/^\d{11}$/', $code)) return false;
$w1 = [1,2,3,4,5,6,7,8,9,1];
$w2 = [3,4,5,6,7,8,9,1,2,3];
$sum = 0; for ($i=0; $i<10; $i++) $sum += intval($code[$i]) * $w1[$i];
$k = $sum % 11;
if ($k == 10) {
$sum = 0; for ($i=0; $i<10; $i++) $sum += intval($code[$i]) * $w2[$i];
$k = $sum % 11; if ($k == 10) $k = 0;
}
return intval($code[10]) === $k;
}
?>