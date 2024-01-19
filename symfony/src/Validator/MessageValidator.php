<?php

namespace App\Validator;

class MessageValidator {

    public function Validate(Array $data) : Array | Bool 
    {
        $valid = true;
        $errors = [
            'name' => '',
            'last_name' => '',
            'email' => '',
            'message' => ''
        ];

        if(isset($data['name']) && isset($data['last_name']) && isset($data['email']) && isset($data['message'])) {
            if($this->validTextInput($data['name']) !== false) {
                $errors['name'] = $this->validTextInput($data['name']); 
                $valid = false;
            }
            if($this->validTextInput($data['last_name']) !== false) {
                $errors['last_name'] = $this->validTextInput($data['last_name']); 
                $valid = false;
            }
            if($this->validEmail($data['email']) !== false) {
                $errors['email'] = $this->validEmail($data['email']); 
                $valid = false;
            }
            if($this->validTextInput($data['message']) !== false) {
                $errors['message'] = $this->validTextInput($data['message']); 
                $valid = false;
            }
        } else {
            return false;
        }

        if($valid) return true;

        return $errors;
    }

    private function validTextInput($text) : String | Bool
    {
        if($text === '') return 'To pole jest wymagane';
        if(strlen($text) > 250) return 'Za dużo znaków (Max 250)';

        return false;
    }

    private function validEmail($email) : String | Bool
    {
        $pattern = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';
        if($email === '') return 'To pole jest wymagane';
        if(strlen($email) > 250) return 'Za dużo znaków (Max 250)';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'E-mail jest nieprawidłowy';
        if (!preg_match($pattern, $email)) return 'E-mail jest nieprawidłowy';

        return false;
    }
}
