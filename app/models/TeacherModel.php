<?php
require_once 'ApiModel.php';

class TeacherModel extends ApiModel {
    public static function loginAdmin($username, $password) {
        return self::request('/auth/admin/login', 'POST', [
            'username' => $username,
            'password' => $password
        ], false);
    }

    public static function loginGuru($username, $password) {
        return self::request('/auth/teacher/login', 'POST', [
            'username' => $username,
            'password' => $password
        ], false);
    }

    public static function getAll() {
        return self::request('/teachers', 'GET');
    }

    public static function create($data) {
        return self::request('/teachers', 'POST', $data);
    }

    public static function update($id, $data) {
        return self::request('/teachers/' . $id, 'PUT', $data);
    }

    public static function delete($id) {
        return self::request('/teachers/' . $id, 'DELETE');
    }
}
