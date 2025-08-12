<?php

namespace Core;

class Validation
{
    protected array $errors = [];
    protected array $data = [];
    public function validate(array $data, array $rules)
    {
        $this->data = $data;
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);

            foreach ($rulesArray as $rule) {
                $params = [];

                if (strpos($rule, ':') !== false) {
                    [$rule, $paramString] = explode(':', $rule);
                    $params = explode(',', $paramString);
                }

                $method = "validate" . ucfirst($rule);

                if (method_exists($this, $method)) {
                    $this->$method($field, $data[$field] ?? null, ...$params);
                }
            }
        }
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    // 📌 Métodos de validación
    private function validateRequired(string $field, $value)
    {
        if (empty($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp is required.");
            } else {
                $this->addError($field, "El campo $camp es obligatorio.");
            }
        }
    }

    private function validateEmail(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be a valid email.");
            } else {
                $this->addError($field, "El campo $camp debe ser un correo válido.");
            }
        }
    }

    private function validateMin(string $field, $value, $minLength)
    {
        if (strlen($value) < $minLength) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must have at least $minLength characters.");
            } else {
                $this->addError($field, "El campo $camp debe tener al menos $minLength caracteres.");
            }
        }
    }

    private function validateMax(string $field, $value, $maxLength)
    {
        if (strlen($value) > $maxLength) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must not exceed $maxLength characters.");
            } else {
                $this->addError($field, "El campo $camp no debe superar los $maxLength caracteres.");
            }
        }
    }

    private function validateString(string $field, $value)
    {
        if (!is_string($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be a string.");
            } else {
                $this->addError($field, "El campo $camp debe ser una cadena de texto.");
            }
        }
    }
    private function validateBool(string $field, $value)
    {
        if (!is_bool($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be a boolean value.");
            } else {
                $this->addError($field, "El campo $camp debe ser un valor booleano.");
            }
        }
    }
    private function validateFloat(string $field, $value)
    {
        if (!is_float($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be a float value.");
            } else {
                $this->addError($field, "El campo $camp debe ser un valor flotante.");
            }
        }
    }
    private function validateInt(string $field, $value)
    {
        if (!is_int($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be an integer value.");
            } else {
                $this->addError($field, "El campo $camp debe ser un valor entero.");
            }
        }
    }
    private function validateArray(string $field, $value)
    {
        if (!is_array($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be an array.");
            } else {
                $this->addError($field, "El campo $camp debe ser un arreglo.");
            }
        }
    }
    private function validateObject(string $field, $value)
    {
        if (!is_object($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be an object.");
            } else {
                $this->addError($field, "El campo $camp debe ser un objeto.");
            }
        }
    }
    private function validateNumeric(string $field, $value)
    {
        if (!is_numeric($value)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be a numeric value.");
            } else {
                $this->addError($field, "El campo $camp debe ser un valor numérico.");
            }
        }
    }
    private function validateUrl(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp must be a valid URL.");
            } else {
                $this->addError($field, "El campo $camp debe ser una URL válida.");
            }
        }
    }
    private function validateConfirmed(string $field, $value)
    {
        $getConfirmation = $field . '_confirmation';
        if ($value !== $this->data[$getConfirmation]) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp does not match the confirmation.");
            } else {
                $this->addError($field, "El campo $camp no coincide con la confirmación.");
            }
        }
    }
    private function validateUnique(string $field, $value, $table, $column)
    {
        // validamos si table es un modelo
        $table = '\App\Models\\' . $table;
        if (class_exists($table)) {
            $datos = $table::where($column, '=', $value)->get();
            if (count($datos) > 0) {
                $camp = traducir($field);
                if (getLanguage() == 'en') {
                    $this->addError($field, "The field $camp is already registered try another one.");
                } else {
                    $this->addError($field, "El campo $camp ya está registrado intente con otro.");
                }
            }
        }
    }
    private function validateIn(string $field, $value, ...$params)
    {
        if (!in_array($value, $params)) {
            $camp = traducir($field);
            if (getLanguage() == 'en') {
                $this->addError($field, "The field $camp is not valid.");
            } else {
                $this->addError($field, "El campo $camp no es válido.");
            }
        }
    }
    private function addError(string $field, string $message)
    {
        $this->errors[$field][] = $message;
    }
}
