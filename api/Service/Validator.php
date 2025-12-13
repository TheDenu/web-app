<?php
class Validator
{
    private $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            if (!isset($data[$field])) {
                $this->errors[$field] = "Поле обязательно";
                continue;
            }

            $value = $data[$field];
            $fieldErrors = [];

            foreach ((array)$fieldRules as $rule) {
                [$ruleName, $params] = $this->parseRule($rule);

                if (!$this->checkRule($field, $value, $ruleName, $params)) {
                    $fieldErrors[] = $this->getRuleMessage($field, $ruleName, $params);
                }
            }

            if ($fieldErrors) {
                $this->errors[$field] = implode(', ', $fieldErrors);
            }
        }

        return empty($this->errors);
    }

    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $params] = explode(':', $rule, 2);
            return [trim($ruleName), trim($params)];
        }
        return [$rule, null];
    }

    private function checkRule(string $field, $value, string $ruleName, ?string $params): bool
    {
        return match ($ruleName) {
            'required' => !empty(trim((string)$value)),
            'int' => filter_var($value, FILTER_VALIDATE_INT) !== false,
            'string' => is_string($value),
            'array' => is_array($value),
            'min' => $this->checkMin($value, (int)$params),
            'max' => $this->checkMax($value, (int)$params),
            'in' => $this->checkIn($value, $params),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL),
            'phone' => preg_match('/^\+?[78][\s\(\-]?\d{3}[\s\)\-]?[\d\s\-]{7,10}$/', $value),
            'alpha_num' => ctype_alnum($value),
            'image' => $this->checkImage($value),
            default => true
        };
    }

    private function checkMin($value, int $min): bool
    {
        return is_numeric($value) ? $value >= $min : strlen($value) >= $min;
    }

    private function checkMax($value, int $max): bool
    {
        return is_numeric($value) ? $value <= $max : strlen($value) <= $max;
    }

    private function checkIn($value, ?string $params): bool
    {
        if (!$params) return false;
        $allowed = array_map('trim', explode(',', $params));
        return in_array($value, $allowed);
    }

    private function checkImage($file): bool
    {
        if (!is_array($file) || !isset($file['name'])) return false;
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($ext, $allowed);
    }

    private function getRuleMessage(string $field, string $ruleName, ?string $params): string
    {
        return match ($ruleName) {
            'required' => "$field обязательно",
            'int' => "$field должно быть числом",
            'string' => "$field должно быть строкой",
            'array' => "$field должно быть массивом",
            'min' => "$field минимум " . ($params ?? '1') . " символов/значений",
            'max' => "$field максимум " . ($params ?? '255') . " символов/значений",
            'in' => "$field недопустимое значение",
            'email' => "$field неверный email",
            'phone' => "$field неверный телефон",
            default => "$field неверный формат"
        };
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
