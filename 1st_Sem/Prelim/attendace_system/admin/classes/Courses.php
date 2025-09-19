<?php

require_once __DIR__ . '/Database.php';

class Courses extends Database {

    // Add a new course
    public function addCourse($courseName, $courseTime = null) {
        $stmt = $this->conn->prepare("INSERT INTO courses (course_name, course_time) VALUES (?, ?)");
        $stmt->bind_param("ss", $courseName, $courseTime);
        return $stmt->execute();
    }

    // View all courses
    public function getCourses() {
        $result = $this->conn->query("SELECT * FROM courses ORDER BY course_name ASC");
        return $result;
    }

    // Delete a course
    public function deleteCourse($courseId) {
        $stmt = $this->conn->prepare("DELETE FROM courses WHERE id=?");
        $stmt->bind_param("i", $courseId);
        return $stmt->execute();
    }
}
