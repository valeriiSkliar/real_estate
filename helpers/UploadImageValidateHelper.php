<?php

namespace app\helpers;

use Yii;
use yii\base\Model;

class UploadImageValidateHelper extends Model
{
    public static function validateImage($uploadedFile) {
        // Validate extension
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        if (!in_array($uploadedFile->extension, $allowedExtensions)) {
            throw new \yii\web\BadRequestHttpException('Invalid file extension.');
        }

        // Validate MIME type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($uploadedFile->type, $allowedMimeTypes)) {
            throw new \yii\web\BadRequestHttpException('Invalid file type.');
        }

        // Validate as image by GD extension
        $imageData = getimagesize($uploadedFile->tempName);
        if ($imageData === false) {
            throw new \yii\web\BadRequestHttpException('Invalid image file.');
        }

        // Check the file content for any embedded PHP or code scripts
        $contents = file_get_contents($uploadedFile->tempName);
        if (preg_match('/<\?php/i', $contents)) {
            throw new \yii\web\BadRequestHttpException('Invalid file content.');
        }

        // Image Re-Rendering
        switch ($uploadedFile->type) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($uploadedFile->tempName);
                break;
            case 'image/png':
                $image = imagecreatefrompng($uploadedFile->tempName);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
            default:
                throw new \yii\web\BadRequestHttpException('Unsupported image type.');
        }

        if ($image === false) {
            throw new \yii\web\BadRequestHttpException('Invalid image file.');
        }

        // Save the re-rendered image, overwriting the original
        switch ($uploadedFile->type) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($image, $uploadedFile->tempName);
                break;
            case 'image/png':
                imagepng($image, $uploadedFile->tempName);
                break;
        }

        // Destroy image resource to free memory
        imagedestroy($image);

        // Check the file name for any suspicious or executable extensions, e.g., .php, .exe, .js
        if (preg_match('/\.(php|exe|js)$/i', $uploadedFile->name)) {
            throw new \yii\web\BadRequestHttpException('Invalid file name.');
        }
    }

    public static function upload($uploadedFile, $subFolder, $name = null): string
    {
        $entity = Yii::$app->request->post('entity');
        $id = Yii::$app->request->post('id');

        // Validate
        self::validateImage($uploadedFile);

        // Generate a unique filename for the uploaded file
        $fileName = ($name)
            ? $name . '.' . $uploadedFile->extension
            : Yii::$app->security->generateRandomString() . '.' . $uploadedFile->extension;

        // Ensure the directory exists
        $filePath = Yii::getAlias("@app/web/uploads/") . $subFolder;
        self::makeDirectory($filePath);

        // Save the uploaded file
        if ($uploadedFile->saveAs($filePath . '/' . $fileName)) {
            return '/uploads/' . $subFolder . '/' . $fileName;
        } else {
            // If the file could not be saved, return an error
            throw new \yii\web\ServerErrorHttpException('Could not save file.');
        }
    }

    public static function makeDirectory($folder)
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
    }
}
