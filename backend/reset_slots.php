<?php
// One-time script: set collection slots to 3 days × 3 time slots each.
// Run once, then delete this file.
require_once __DIR__ . '/connect.php';

$conn = getDBConnection();
if (!$conn) { die("DB connection failed"); }

// 3 days × 3 slots = 9 slots
// IDs 1-8 already exist (FK-safe to UPDATE), insert 1 new for the 9th.
$desired = [
    //  id  => [day_offset, slot_time]
    1  => [0, '10:00 - 13:00'],
    2  => [0, '13:00 - 16:00'],
    3  => [0, '16:00 - 19:00'],
    4  => [1, '10:00 - 13:00'],
    5  => [1, '13:00 - 16:00'],
    6  => [1, '16:00 - 19:00'],
    7  => [2, '10:00 - 13:00'],
    8  => [2, '13:00 - 16:00'],
];
$newSlot = [2, '16:00 - 19:00']; // 9th slot — inserted fresh

// Update existing 8 rows
foreach ($desired as $slotId => [$offset, $time]) {
    $sql  = "UPDATE collection_slot
             SET slot_date = TRUNC(SYSDATE) + :off,
                 slot_day  = RTRIM(TO_CHAR(TRUNC(SYSDATE) + :off2, 'Day')),
                 slot_time = :stime
             WHERE collection_slot_id = :sid";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':off',   $offset);
    oci_bind_by_name($stmt, ':off2',  $offset);
    oci_bind_by_name($stmt, ':stime', $time);
    oci_bind_by_name($stmt, ':sid',   $slotId);
    oci_execute($stmt);
    oci_free_statement($stmt);
}

// Insert the 9th slot
[$offset9, $time9] = $newSlot;
$ins  = "INSERT INTO collection_slot (slot_day, slot_date, slot_time)
         VALUES (RTRIM(TO_CHAR(TRUNC(SYSDATE) + :off, 'Day')), TRUNC(SYSDATE) + :off2, :stime)";
$stmt = oci_parse($conn, $ins);
oci_bind_by_name($stmt, ':off',   $offset9);
oci_bind_by_name($stmt, ':off2',  $offset9);
oci_bind_by_name($stmt, ':stime', $time9);
oci_execute($stmt);
oci_free_statement($stmt);

oci_commit($conn);

// Show result
echo "<pre>Collection slots after reset:\n";
$s = oci_parse($conn, "SELECT collection_slot_id, slot_day, TO_CHAR(slot_date,'DD Mon YYYY (Dy)') AS d, slot_time FROM collection_slot WHERE slot_date >= TRUNC(SYSDATE) ORDER BY slot_date, slot_time");
oci_execute($s);
while ($r = oci_fetch_assoc($s)) {
    echo "  ID={$r['COLLECTION_SLOT_ID']}  {$r['SLOT_DAY']}  {$r['D']}  {$r['SLOT_TIME']}\n";
}
oci_free_statement($s);
oci_close($conn);
echo "\nDone. Delete this file.\n</pre>";
