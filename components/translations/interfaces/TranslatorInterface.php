<?php

namespace app\components\translations\interfaces;

interface TranslatorInterface
{
    public function translate($text);
    public function translateBatch($texts);
    public function setTargetLanguage($targetLanguage): void;
    public function getTargetLanguage(): string;
}