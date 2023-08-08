<?php

namespace InfinityBrackets\Core;

use \stdClass;

class Logger {
    protected Database $db;
    protected Controller $controller;

    public function __construct($database = NULL) {
        $this->db = new Database($database ?? 'ils');
    }

    public function Register(Controller $controller) {
        $this->controller = $controller;
    }

    public function GetMethods(): array {
        // Remove all magic methods from action list
        $magicMethods = ['__construct'];

        return array_values(
            array_diff(
                get_class_methods($this->controller), 
                get_class_methods(new Controller), 
                $magicMethods,
                $this->excludes
            )
        );
    }

    public function Action($actionLog = NULL) {
        if(!empty((array)$actionLog)) {
            try {
                $this->db->Begin();
                $data = (array)$actionLog;
                unset($data['details']);

                $keys = array_keys($data);
                $parameters = [];
                
                foreach($data as $key => $value) {
                    $parameters[':in_' . $key] = $value;
                }
                if(!in_array('created_by', $keys)) {
                    array_push($keys, 'created_by');
                    $parameters[':in_created_by'] = Application::$app->user->user->id;
                }
                
                $logId = $this->db->InsertOne("action_logs", $keys, $parameters);

                $data = (array)$actionLog;
                if(isset($data['details'])) {

                    foreach($data['details'] as $detail) {
                        $keys = [];
                        $parameters = [];
                        foreach((array)$detail as $key => $value) {
                            $keys[] = $key;
                            $parameters[':in_' . $key] = $value;
                        }
                        $keys[] = "action_log_id";
                        $parameters[':in_action_log_id'] = $logId;
                
                        $this->db->InsertOne("action_log_attributes", $keys, $parameters);
                    }
                }
                $this->db->Commit();
                $this->db->Update("action_logs", ['status' => ':in_status'], "WHERE `id` = :in_log_id", ['in_status' => 1, 'in_log_id' => $logId]);
            } catch(\Exception $e) {
                $this->db->Rollback();
                $this->Error($e->getCode(), $e->getMessage());
            }
        }
        // $content = json_encode($actionLog);
        // $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/myText.txt","wb");
        // fwrite($fp, $content);
        // fclose($fp);
    }

    public function Error($code, $message) {
        $this->db->InsertOne("exception_logs", ['code', 'message'], [':in_code' => $code, ':in_message' => $message]);
    }
}