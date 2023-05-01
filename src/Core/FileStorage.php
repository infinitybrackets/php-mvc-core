<?php

namespace InfinityBrackets\Core;

class FileStorage
{
    private static $files = [];

    protected function Files() {
        return self::$files;
    }
    
    public function Push($file)
    {
        self::$files = $file;
    }

    public function HasFiles()
    {
        return count(self::$files) > 0 ? TRUE : FALSE;
    }

    public function Upload($storage) {
        $fileName = self::$files['name'];
        $tempLocation = self::$files['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $name = uniqid(true) . '.' . $fileExtension;

        $upload = move_uploaded_file($tempLocation, $storage . $name) ? TRUE : FALSE;

        return Application::$app->ToObject(['success' => $upload, 'name' => $name]);
    }
}