<?php

namespace InfinityBrackets\Core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';
    protected $USER_KEY = 'user';

    public function __construct($userKey)
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;

        // Set Session Key of User based on session auth configuration
        if($userKey) {
            $this->USER_KEY = $userKey;
        }
    }

    public function SetFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function GetFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function SetSwal($icon, $title, $message)
    {
        $key = 'swal';
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'icon' => $icon,
            'title' => $title,
            'text' => $message
        ];
    }

    public function GetSwal()
    {
        return $_SESSION[self::FLASH_KEY]['swal'] ?? false;
    }

    public function Auth($value) {
        $this->Set($this->USER_KEY, $value);
    }

    public function GetAuth() {
        return $this->Get($this->USER_KEY);
    }

    public function DeAuth() {
        $this->Remove($this->USER_KEY);
    }

    public function Set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function Get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function Remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->RemoveFlashMessages();
    }

    private function RemoveFlashMessages()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}