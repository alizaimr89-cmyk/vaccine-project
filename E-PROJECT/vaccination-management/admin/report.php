<?php
require_once __DIR__.'/../includes/functions.php';
require_login('admin');
$pdo = db();
$type = $_GET['type'] ?? 'appointments';
if ($type === 'vaccinations') {
    $sql = "SELECT r.id, c.full_name AS child_name, v.name AS vaccine_name, v.dose, h.name AS hospital_name, r.administered_on, u.name AS recorded_by, r.notes FROM vaccination_records r JOIN children c ON c.id=r.child_id JOIN vaccines v ON v.id=r.vaccine_id JOIN hospitals h ON h.id=r.hospital_id LEFT JOIN users u ON u.id=r.administered_by ORDER BY r.administered_on DESC";
    $filename = 'nurturedose-vaccination-records.csv';
} else {
    $sql = "SELECT b.id, c.full_name AS child_name, p.name AS parent_name, p.email AS parent_email, v.name AS vaccine_name, v.dose, h.name AS hospital_name, h.city, b.appointment_date, b.appointment_time, b.status, b.parent_note, b.created_at FROM bookings b JOIN children c ON c.id=b.child_id JOIN users p ON p.id=b.parent_id JOIN vaccines v ON v.id=b.vaccine_id JOIN hospitals h ON h.id=b.hospital_id ORDER BY b.appointment_date DESC, b.appointment_time DESC";
    $filename = 'nurturedose-appointments.csv';
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: no-store, no-cache, must-revalidate');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");
$stmt = $pdo->query($sql);
$first = $stmt->fetch(PDO::FETCH_ASSOC);
if ($first) {
    fputcsv($out, array_keys($first));
    fputcsv($out, array_map(static fn($value) => is_string($value) ? $value : (string)$value, array_values($first)));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) fputcsv($out, array_values($row));
}
fclose($out);
exit;
