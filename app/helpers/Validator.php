<?php
namespace App\Helpers;

use App\Config\Database;
use PDO;

class Validator {
    private array $data;
    private array $errors = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    public function validate(array $rules): void {
        foreach ($rules as $field => $ruleString) {
            $rulesList = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $val = $this->data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $this->applyRule($field, $val, $ruleName, $params);
            }
        }
    }

    private function applyRule(string $field, $val, string $rule, array $params): void {
        $fieldName = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($val === null || (is_string($val) && trim($val) === '') || (is_array($val) && empty($val))) {
                    $this->addError($field, "{$fieldName} is required.");
                }
                break;

            case 'email':
                if (!empty($val) && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Please provide a valid email address.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (!empty($val) && strlen((string)$val) < $min) {
                    $this->addError($field, "{$fieldName} must be at least {$min} characters.");
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 255);
                if (!empty($val) && strlen((string)$val) > $max) {
                    $this->addError($field, "{$fieldName} may not exceed {$max} characters.");
                }
                break;

            case 'numeric':
                if (!empty($val) && !is_numeric($val)) {
                    $this->addError($field, "{$fieldName} must be a number.");
                }
                break;

            case 'mobile':
                if (!empty($val) && !preg_match('/^[6-9]\d{9}$/', preg_replace('/[^\d]/', '', (string)$val))) {
                    $this->addError($field, "Please enter a valid 10-digit Indian mobile number.");
                }
                break;

            case 'pan':
                if (!empty($val) && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', strtoupper(trim((string)$val)))) {
                    $this->addError($field, "Please enter a valid 10-character PAN number (e.g. ABCDE1234F).");
                }
                break;

            case 'aadhaar':
                $cleanAadhaar = preg_replace('/\D/', '', (string)$val);
                if (!empty($val) && (strlen($cleanAadhaar) !== 12 || !preg_match('/^[2-9]\d{11}$/', $cleanAadhaar))) {
                    $this->addError($field, "Please enter a valid 12-digit Aadhaar number.");
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                $confirmVal = $this->data[$confirmField] ?? null;
                if ($val !== $confirmVal) {
                    $this->addError($field, "{$fieldName} confirmation does not match.");
                }
                break;

            case 'in':
                if (!empty($val) && !in_array($val, $params, true)) {
                    $this->addError($field, "Selected {$fieldName} is invalid.");
                }
                break;

            case 'unique':
                // params: table,column,except_id
                $table = $params[0] ?? 'users';
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;

                if (!empty($val)) {
                    $db = Database::getConnection();
                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val";
                    if ($exceptId) {
                        $sql .= " AND `id` != :exceptId";
                    }
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':val', $val);
                    if ($exceptId) {
                        $stmt->bindValue(':exceptId', $exceptId);
                    }
                    $stmt->execute();
                    if ((int)$stmt->fetchColumn() > 0) {
                        $this->addError($field, "This {$fieldName} is already registered.");
                    }
                }
                break;
        }
    }

    public function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function first(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }

    public function firstError(): ?string {
        foreach ($this->errors as $errs) {
            if (!empty($errs[0])) {
                return $errs[0];
            }
        }
        return null;
    }
}
