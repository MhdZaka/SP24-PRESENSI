<?php
require_once 'ApiModel.php';

class PresenceModel extends ApiModel {
    public static function getAll() {
        return self::request('/presences', 'GET');
    }

    public static function create($data) {
        return self::request('/presences', 'POST', $data);
    }

    public static function getByParentNis($nis) {
        return self::request('/presences/parent/' . $nis, 'GET', null, false);
    }
}
