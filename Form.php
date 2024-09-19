<?php

class Form {
    private $action;
    private $elements = array();
    private $errors = array();

    public function __construct($action) {
        $this->action = $action;
    }

    public function addElement($element) {
        $this->elements[] = $element;
    }

    public function render() {
        echo '<form id="loginForm" method="post" action="' . $this->action . '">';
        foreach ($this->elements as $element) {
            echo $element->render();
            // Display error message if exists
            if($element instanceof Input && isset($this->errors[$element->getName()])) {
                echo '<div class="error-message">' . $this->errors[$element->getName()] . '</div>';
            }
        }
        echo '</form>';
    }

    public function validateLogin($username, $password) {
        // Basic validation
        if(empty($username) || empty($password)) {
            $this->errors["email"] = "Username and password are required.";
        }
        // You can add more complex validation here, such as checking username format, password strength, etc.
        // For simplicity, let's assume no additional validation for now.
    }

    public function getErrors() {
        return $this->errors;
    }
}

class Input {
    private $type;
    private $name;
    private $placeholder;
    private $iconClass;
    private $required;
    private $error;

    public function __construct($type, $name, $placeholder, $iconClass, $required = true) {
        $this->type = $type;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->iconClass = $iconClass;
        $this->required = $required;
        $this->error = '';
    }

    public function getName() {
        return $this->name;
    }

    public function setError($error) {
        $this->error = $error;
    }
    
    public function render() {
        $requiredAttr = $this->required ? 'required' : '';
        $errorClass = $this->error ? 'input-error' : '';
        $errorMessage = $this->error ? '<div class="error-message" style="color: red;">'.$this->error.'</div>' : '';
        return '<div class="input_box '.$errorClass.'">
                  <input type="'.$this->type.'" name="'.$this->name.'" placeholder="'.$this->placeholder.'" '.$requiredAttr.' />
                  <i class="'.$this->iconClass.'"></i>
                  '.$errorMessage.'
                </div>';
    }
}

class Button {
    private $text;

    public function __construct($text) {
        $this->text = $text;
    }

    public function render() {
        return '<button class="button" type="submit">'.$this->text.'</button>';
    }
}
?>
