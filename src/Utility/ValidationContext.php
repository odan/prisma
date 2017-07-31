<?php

namespace App\Utility;

class ValidationContext
{
    private $message;

    private $errors = [];

    /**
     * ValidationContext constructor.
     *
     * @param string|null $message Main message
     */
    public function __construct(string $message = null)
    {
        $this->message = $message;
    }

    /**
     * Set main message.
     *
     * @param string $message Main Message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get main .
     *
     * @return null|string Main message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Add new error.
     *
     * @param string|int $field Error field name
     * @param string $message Error message for $field
     */
    public function addError($field, $message)
    {
        $this->errors[] = [
            "field" => $field,
            "message" => $message
        ];
    }

    /**
     * Get all errors.
     *
     * @return array All errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check for errors.
     * Returns false if they are no errors.
     *
     * @return bool False if no errors
     */
    public function failed()
    {
        return !empty($this->errors);
    }

    /**
     * Reset class.
     */
    public function clear()
    {
        unset($this->message);
        $this->errors = [];
    }

    /**
     * Return main message with all errors as array.
     *
     * @return array Result
     */
    public function toArray()
    {
        $result = [
            "message" => $this->message,
            "errors" => $this->errors
        ];
        return $result;
    }
}