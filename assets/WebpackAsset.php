<?php
namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class WebpackAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    // public $sourcePath = '@webroot/assets/dist';
    
    public $css = [];
    public $js = [];
    
    public $depends = [
        // 'yii\web\YiiAsset',
        'app\assets\NpmAsset'
    ];

    public static function register($view)
    {
        $bundle = parent::register($view);
        
        $useDevServer = self::isDevServerRunning();
        if ($useDevServer) {
            // Use webpack-dev-server URLs
            $bundle->basePath = null;
            $bundle->baseUrl = 'http://localhost:8090/assets/dist';
            $bundle->js = [
                'js/vendors.js',
                'js/main.js'
            ];
            $bundle->css = [
                'css/style.css',
                // 'css/all.css',
            ];
        } else if (file_exists(\Yii::getAlias('@webroot/assets/dist/manifest.json'))) {
            // Use manifest for production
            $manifest = json_decode(file_get_contents(\Yii::getAlias('@webroot/assets/dist/manifest.json')), true);
            Yii::debug('manifest: ' , $manifest);
            // Add CSS files from manifest
            if (isset($manifest['style.css'])) {
                $bundle->css[] = $manifest['style.css'];
            } else {
                // Fallback to non-hashed files
                $bundle->css[] = 'css/style.css';
            }
            
            // Add all.css directly (copied by CopyWebpackPlugin)
            $bundle->css[] = 'css/all.css';
            
            // Add JS files from manifest
            if (isset($manifest['vendors.js'])) {
                $bundle->js[] = $manifest['vendors.js'];
            }
            if (isset($manifest['main.js'])) {
                $bundle->js[] = $manifest['main.js'];
            }
        }
        
        return $bundle;
    }

    private static function isDevServerRunning()
    {
        try {
            $handle = @fsockopen('localhost', 8090);
            if ($handle) {
                fclose($handle);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}