<?php

namespace Core;

use App\Models\User;

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

    public function getErrors(): array
    {
        // Devolver errores organizados por campo para acceso directo en vistas
        return $this->errors;
    }

    public function getFlatErrors(): array
    {
        // Aplanar el array de errores cuando se necesite una lista simple
        $flatErrors = [];
        foreach ($this->errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $flatErrors[] = $error;
            }
        }
        return $flatErrors;
    }

    // 📌 Métodos de validación
    private function validateRequired(string $field, $value)
    {
        $isEmpty = false;

        if ($value === null || $value === '') {
            $isEmpty = true;
        } elseif (is_array($value) && empty($value)) {
            $isEmpty = true;
        }

        if ($isEmpty) {
            $camp = $field;
            $this->addError($field, "El campo $camp es obligatorio.");
        }
    }

    private function validateEmail(string $field, $value)
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser un correo válido.");
        }
    }

    private function validateMin(string $field, $value, $minLength)
    {
        if ($value !== null) {
            // Si es un array, validar el número de elementos
            if (is_array($value)) {
                if (count($value) < $minLength) {
                    $camp = $field;
                    $this->addError($field, "El campo $camp debe tener al menos $minLength elemento(s).");
                }
            } else {
                // Si es string, validar la longitud
                if (strlen($value) < $minLength) {
                    $camp = $field;
                    $this->addError($field, "El campo $camp debe tener al menos $minLength caracteres.");
                }
            }
        }
    }

    private function validateMax(string $field, $value, $maxLength)
    {
        if ($value !== null) {
            // Si es un array, validar el número de elementos
            if (is_array($value)) {
                if (count($value) > $maxLength) {
                    $camp = $field;
                    $this->addError($field, "El campo $camp no debe superar los $maxLength elemento(s).");
                }
            } else {
                // Si es string, validar la longitud
                if (strlen($value) > $maxLength) {
                    $camp = $field;
                    $this->addError($field, "El campo $camp no debe superar los $maxLength caracteres.");
                }
            }
        }
    }

    private function validateString(string $field, $value)
    {
        if (!is_string($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser una cadena de texto.");
        }
    }
    private function validateBool(string $field, $value)
    {
        if (!is_bool($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser un valor booleano.");
        }
    }
    private function validateFloat(string $field, $value)
    {
        if (!is_float($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser un valor flotante.");
        }
    }
    private function validateInt(string $field, $value)
    {
        if (!is_int($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser un valor entero.");
        }
    }
    private function validateArray(string $field, $value)
    {
        if ($value !== null && !is_array($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser un arreglo.");
        }
    }
    private function validateObject(string $field, $value)
    {
        if (!is_object($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser un objeto.");
        }
    }
    private function validateNumeric(string $field, $value)
    {
        if (!is_numeric($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser un valor numérico.");
        }
    }
    private function validateUrl(string $field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser una URL válida.");
        }
    }
    private function validateConfirmed(string $field, $value)
    {
        $getConfirmation = $field . '_confirmation';
        if ($value !== $this->data[$getConfirmation]) {
            $camp = $field;
            $this->addError($field, "El campo $camp no coincide con la confirmación.");
        }
    }
    private function validateUnique(string $field, $value, $table, $column)
    {
        // validamos si table es un modelo
        $table = '\\App\\Models\\' . $table;
        if (class_exists($table)) {
            $datos = $table::where($column, '=', $value)->get();
            if (count($datos) > 0) {
                $camp = $field;
                $this->addError($field, "El campo $camp ya está registrado intente con otro.");
            }
        }
    }
    private function validateIn(string $field, $value, ...$params)
    {
        if (!in_array($value, $params)) {
            $camp = $field;
            $this->addError($field, "El campo $camp no es válido.");
        }
    }

    private function validateSame(string $field, $value, string $otherField)
    {
        if ($value !== ($this->data[$otherField] ?? null)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe coincidir con $otherField.");
        }
    }

    private function validateDate(string $field, $value)
    {
        if ($value !== null && $value !== '' && !strtotime($value)) {
            $camp = $field;
            $this->addError($field, "El campo $camp debe ser una fecha válida.");
        }
    }

    private function validateNullable(string $field, $value)
    {
        // Esta validación permite valores nulos/vacíos
        // No hace nada, solo existe para compatibilidad
        return true;
    }

    private function validateSecurePassword(string $field, $value)
    {
        if ($value === null || $value === '') {
            return; // Si está vacío, validateRequired se encargará
        }

        $errors = [];

        // Verificar longitud mínima
        if (strlen($value) < 8) {
            $errors[] = "debe tener al menos 8 caracteres";
        }

        // Verificar que tenga al menos una mayúscula
        if (!preg_match('/[A-Z]/', $value)) {
            $errors[] = "debe contener al menos una letra mayúscula";
        }

        // Verificar que tenga al menos un número
        if (!preg_match('/[0-9]/', $value)) {
            $errors[] = "debe contener al menos un número";
        }

        // Verificar que tenga al menos una minúscula
        if (!preg_match('/[a-z]/', $value)) {
            $errors[] = "debe contener al menos una letra minúscula";
        }
        // obtenemos el email
        $email = $this->data['email'] ?? null;
        if ($email) {
            // verificar si el usario esta registrado
            $existingUser = User::where('email', '=', $email)->first();
            if ($existingUser && $existingUser->checkPasswordHistory($value)) {
                $errors[] = "no debe haber sido utilizada en las últimas 5 contraseñas";
            }
        }
        if (!empty($errors)) {
            $camp = $field;
            $errorMessage = "La contraseña " . implode(', ', $errors) . ".";
            $this->addError($field, $errorMessage);
        }
    }

    private function addError(string $field, string $message)
    {
        $this->errors[$field][] = $message;
    }
}
