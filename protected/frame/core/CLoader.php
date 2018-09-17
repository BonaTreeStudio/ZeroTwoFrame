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
        $path = CApp::getInstance()->getConfig(LOADER_PATH_CONFIG);
        $classFile = $file.'.php';
        //Инклудим кор в начале, тк его нельзя переопределять только перегрузить
        $filePath = dirname(__FILE__).'/'.$classFile;
        if (file_exists($filePath)) {
            require_once $filePath;
            return true;
        }
        if (!empty($path)) {
            foreach ($path as $p) {
                $p = str_replace('.', '/', $p);
                $filePath = PROTECTED_DIR.$p.'/'.$classFile;
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
