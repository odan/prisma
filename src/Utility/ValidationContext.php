<?php

namespace App\Utility;

/**
 * Class ValidationContext
 */
class ValidationContext
{

    /**
     * @var null|string
     */
    private $message;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * ValidationContext constructor.
     *
     * @param string $message Main message
     */
    public function __construct(string $message = null)
    {
        $this->message = $message;
    }

    /**
     * Set message.
     *
     * @param string $message Main Message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Get message.
     *
     * @return null|string Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Add new error.
     *
     * @param string|int $field Error field name
     * @param string|null $message Error message for $field
     * @return void
     */
    public function addError($field, $message): void
    {
        $this->errors[] = [
            'field' => $field,
            'message' => $message
        ];
    }

    /**
     * Get all errors.
     *
     * @return array All errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check for errors.
     * Returns false if they are no errors.
     *
     * @return bool False if no errors
     */
    public function failed(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Reset message and errors.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->message = null;
        $this->errors = [];
    }

    /**
     * Return message with all errors as array.
     *
     * @return array Result
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'errors' => $this->errors
        ];
    }
}
