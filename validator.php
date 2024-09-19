<?php
class Validator {
    private $errors = [];

    public function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Invalid email format');
        }
    }

    public function validatePassword($password) {
        // You can add your password validation logic here
        if (strlen($password) < 6) {
            $this->addError('password', 'Password must be at least 6 characters long');
        }
    }

    public function hasErrors() {
        return count($this->errors) > 0;
    }

    public function getErrors() {
        return $this->errors;
    }

    private function addError($field, $message) {
        $this->errors[$field] = $message;
    }
}

?>
