<?php

namespace app\models\dto;

class TelegramUserDto
{
    use DtoTrait;

    /**
     * @var string $firstName Имя пользователя.
     */
    private string $firstName;
    /**
     * @var string $lastName Фамилия пользователя.
     */
    private string $lastName;
    /**
     * @var ?string $phone Телефон пользователя.
     */
    private ?string $phone;
    /**
     * @var int $userId Идентификатор пользователя в Telegram.
     */
    private int $userId;
    /**
     * @var string $username Никнейм пользователя в Telegram.
     */
    private string $username;
    /**
     * @var string $text Сообщение которое к нам пришло.
     */
    private string $text;
    /**
     * @var string $language Язык пользователя.
     */
    private string $language;
    /**
     * @var string $data Ответ от кнопки (inline-keyboard).
     */
    private string $data;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getData(): ?string
    {
        return $this->data;
    }
}