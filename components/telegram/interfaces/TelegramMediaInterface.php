<?php

namespace app\components\telegram\interfaces;

interface TelegramMediaInterface
{
    public function getAudio(): ?string;
    public function getFile(): ?string;
    public function getVideo(): ?string;
    public function getImage(): ?string;
}