<?php

namespace app\controllers\admin;

use app\helpers\UploadImageValidateHelper;
use Yii;
use yii\web\UploadedFile;

class ImageTinyUploaderController extends AdminController
{
    public $enableCsrfValidation = false;

    public function actionUpload()
    {
        $uploadedFile = UploadedFile::getInstanceByName('file'); // 'file' is the default name used by TinyMCE
        $entity = Yii::$app->request->post('entity');

        // Validate
        UploadImageValidateHelper::validateImage($uploadedFile);

        // generate a unique filename for the uploaded file
        $fileName = Yii::$app->security->generateRandomString() . '.webp';

        $subFolder = 'uploads/' . $entity;
        $filePath = Yii::getAlias('@app/web/') . '.' . $uploadedFile->extension;
        $this->makeDirectory($filePath);

        // save the uploaded file
        if ($uploadedFile->saveAs($filePath . '/' . $fileName)) {
            // if the file was saved successfully, return the URL to the file
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['location' => \Yii::$app->params['url'] . '/' . $subFolder . '/' . $fileName];
        } else {
            // if the file could not be saved, return an error
            throw new \yii\web\ServerErrorHttpException('Could not save file.');
        }
    }
    public function makeDirectory($folder)
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
    }
}