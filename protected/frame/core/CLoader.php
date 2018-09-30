<?php
/**
 * CLoader is class loader
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
class CLoader
{
    public static function autoload($file, $ext = FALSE, $dir = FALSE)
    {
        //Try to include from core first
        $namespaceFileInclude = explode('\\', $file);
        $partsLen = (count($namespaceFileInclude) - 1);
        foreach ($namespaceFileInclude as $key => $part) {
            if ($partsLen != $key) {
                $namespaceFileInclude[$key] = mb_strtolower($part);
            }
        }
        $namespaceFileInclude = implode('/', $namespaceFileInclude).'.php';
        $frameFilePath = FRAME_PATH_CONFIG . $namespaceFileInclude;
        if (file_exists($frameFilePath)) {
            require_once $frameFilePath;
            return true;
        }
        $path = Core\CApp::getInstance()->getConfig(LOADER_PATH_CONFIG);
        //Инклудим кор в начале, тк его нельзя переопределять только перегрузить
        if (!empty($path)) {
            foreach ($path as $p) {
                $p = str_replace('.', '/', $p);
                $filePath = APP_ROOT . $p . '/' . $namespaceFileInclude;
                if (file_exists($filePath)) {
                    require_once $filePath;
                    return true;
                }
            }
        }
        return false;
    }
}
spl_autoload_register('\CLoader::autoload');
