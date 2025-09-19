<?php

require_once __DIR__ . '/Database.php';

class Attendance extends Database {
    private $student_id;

    public function __construct($student_id) {
        parent::__construct();
        $this->student_id = $student_id;
    }

    // View attendance history
    public function viewAttendance() {
        $stmt = $this->conn->prepare("
            SELECT date, time_in, status, is_late
            FROM attendance
            WHERE student_id=? 
            ORDER BY date DESC
        ");
        $stmt->bind_param("i", $this->student_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Record new attendance
    public function addAttendance($status = "Present", $isLate = false) {
        $date = date("Y-m-d");
        $timeIn = date("H:i:s");

        $stmt = $this->conn->prepare("
            INSERT INTO attendance (student_id, date, time_in, status, is_late)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssi", $this->student_id, $date, $timeIn, $status, $isLate);
        return $stmt->execute();
    }
}
