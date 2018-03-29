<?php

namespace App\Utility;

/**
 * ValidationContext.
 *
 * Represents a container for the results of a validation request.
 */
class ValidationContext
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var string|null
     */
    protected $successMessage = null;

    /**
     * @var string|null
     */
    protected $errorMessage = null;

    /**
     * Get all errors.
     *
     * @return array Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get first error.
     *
     * @return mixed Error
     */
    public function getError()
    {
        return reset($this->errors);
    }

    /**
     * Get message.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->success() ? $this->getSuccessMessage() : $this->getErrorMessage();
    }

    /**
     * Get message.
     *
     * @return string|null
     */
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    /**
     * Set the default success message.
     *
     * @param string $successMessage The default success message
     *
     * @return self self
     */
    public function setSuccessMessage(string $successMessage)
    {
        $this->successMessage = $successMessage;

        return $this;
    }

    /**
     * Set the default error message.
     *
     * @param string $errorMessage The error message
     *
     * @return self self
     */
    public function setErrorMessage(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Returns the success of the validation.
     *
     * @return bool true if validation was successful; otherwise, false
     */
    public function success(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get validation failed status.
     *
     * @return bool Status
     */
    public function failed(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Clear errors and message.
     *
     * @return self
     */
    public function clear()
    {
        $this->successMessage = null;
        $this->errors = [];

        return $this;
    }

    /**
     * Add error message.
     *
     * @param string $field Field name
     * @param string $message Message
     *
     * @return self
     */
    public function addError(string $field, string $message)
    {
        $this->errors[] = ['field' => $field, 'message' => $message];

        return $this;
    }

    /**
     * Convert to array.
     *
     * @return array Data
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success(),
            'message' => $this->getMessage(),
            'errors' => $this->getErrors(),
        ];
    }
}
