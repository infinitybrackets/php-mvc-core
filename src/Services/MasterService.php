<?php

namespace InfinityBrackets\Services;

use InfinityBrackets\Core\Application;
use InfinityBrackets\Core\Database;

class MasterService
{
    private array $masters = [
        'campus' => 'master_campuses',
        'college' => 'master_colleges',
        'course' => 'master_courses',
        'office' => 'master_offices',
        'userType' => 'user_types'
    ];

    public function Match(array $params = []) {
        if(!$params) {
            return FALSE;
        }
        $key = array_keys($params)[0];
        $value = $params[array_keys($params)[0]];
        if(!array_key_exists($key, $this->masters)) {
            return FALSE;
        }
        $table = $this->masters[$key];
        return $this->Fetch($table, $value);
    }

    protected function Fetch($table, $value) {
        $db = new Database(Application::$app->config->env->APP_ENV == 'local' ? 'ils-local' : 'ils-live');
        return $db->SelectOne("SELECT * FROM `" . $table . "` WHERE `id` = :in_id", ['in_id' => $value])->Get();
    }
}