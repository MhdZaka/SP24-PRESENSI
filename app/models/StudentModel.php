<?php
require_once 'ApiModel.php';

class StudentModel extends ApiModel {
    public static function getAll() {
        return self::request('/students', 'GET');
    }

    public static function create($data) {
        return self::request('/students', 'POST', $data);
    }

    public static function update($id, $data) {
        return self::request('/students/' . $id, 'PUT', $data);
    }

    public static function delete($id) {
        return self::request('/students/' . $id, 'DELETE');
    }
}
