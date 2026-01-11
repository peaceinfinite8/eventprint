<?php
// app/helpers/Validation.php

class Validation
{
    /**
     * Validate data sesuai rules, return array error:
     *
     * [
     *   'field_name' => ['Error 1', 'Error 2'],
     *   ...
     * ]
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value    = $data[$field] ?? null;
            $rulesArr = array_filter(explode('|', $ruleString));
            $isNullable = in_array('nullable', $rulesArr, true);

            if (is_string($value)) {
                $value = trim($value);
            }

            $isEmpty = ($value === null || $value === '');

            foreach ($rulesArr as $rule) {
                if ($rule === 'nullable') {
                    continue;
                }

                if ($isNullable && $isEmpty && $rule !== 'required') {
                    break;
                }

                if ($rule === 'required') {
                    if ($isEmpty) {
                        $errors[$field][] = 'Wajib diisi.';
                    }
                    continue;
                }

                if ($isEmpty) {
                    break;
                }

                $name  = $rule;
                $param = null;

                if (strpos($rule, ':') !== false) {
                    [$name, $param] = explode(':', $rule, 2);
                }

                switch ($name) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = 'Format email tidak valid.';
                        }
                        break;

                    case 'min':
                        $min = (int)$param;
                        if (mb_strlen((string)$value) < $min) {
                            $errors[$field][] = "Minimal {$min} karakter.";
                        }
                        break;

                    case 'max':
                        $max = (int)$param;
                        if (mb_strlen((string)$value) > $max) {
                            $errors[$field][] = "Maksimal {$max} karakter.";
                        }
                        break;

                    case 'integer':
                        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                            $errors[$field][] = 'Harus berupa angka bulat.';
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[$field][] = 'Harus berupa angka.';
                        }
                        break;

                    case 'min_value':
                        $minVal = (float)$param;
                        if (!is_numeric($value) || (float)$value < $minVal) {
                            $errors[$field][] = "Nilai minimal {$minVal}.";
                        }
                        break;

                    case 'boolean':
                    // Checkbox bisa:
                    // - tidak dikirim → field tidak ada
                    // - dikirim "on" → kalau dicentang
                    // - atau "0"/"1" kalau lu pakai value manual di HTML

                    if (!array_key_exists($field, $data)) {
                        // Field tidak ada di POST → anggap valid (optional)
                        break;
                    }

                    $value = $data[$field];

                    // Normalisasi: checkbox HTML default kirim "on" kalau dicentang
                    if ($value === 'on') {
                        break; // valid, nanti di controller lu convert pakai !empty()
                    }

                    // Kalau kosong string juga anggap valid (optional)
                    if ($value === '') {
                        break;
                    }

                    // Terima bentuk boolean lain yang wajar
                    if (in_array($value, ['0', '1', 0, 1, true, false, 'true', 'false'], true)) {
                        break;
                    }

                    // ⬇⬇⬇ DI SINI GANTI $this->addError MENJADI LANGSUNG KE ARRAY ERROR
                    $errors[$field][] = 'Harus berupa nilai boolean (0/1).';
                    break;

                    case 'in':
                        $options = array_map('trim', explode(',', (string)$param));
                        if (!in_array((string)$value, $options, true)) {
                            $errors[$field][] = 'Nilai tidak valid.';
                        }
                        break;

                    case 'date':
                        if (!self::isValidDate($value)) {
                            $errors[$field][] = 'Format tanggal tidak valid (YYYY-MM-DD).';
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Validate + kalau gagal → simpan ke session dan redirect.
     * Return data yang sudah di-trim.
     */
    public static function validateOrRedirect(array $data, array $rules, string $backUrl): array
    {
        $clean = $data;

        foreach ($clean as $k => $v) {
            if (is_string($v)) {
                $clean[$k] = trim($v);
            }
        }

        $errors = self::validate($clean, $rules);

        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['old_input']         = $clean;

            header('Location: ' . $backUrl);
            exit;
        }

        return $clean;
    }

    public static function errors(): array
    {
        return $_SESSION['validation_errors'] ?? [];
    }

    public static function old(string $key, $default = '')
    {
        $old = $_SESSION['old_input'] ?? [];
        return array_key_exists($key, $old) ? $old[$key] : $default;
    }

    public static function clear(): void
    {
        unset($_SESSION['validation_errors'], $_SESSION['old_input']);
    }

    protected static function isValidDate(string $value): bool
    {
        $parts = explode('-', $value);
        if (count($parts) !== 3) {
            return false;
        }
        [$y, $m, $d] = $parts;
        return ctype_digit($y) && ctype_digit($m) && ctype_digit($d) && checkdate((int)$m, (int)$d, (int)$y);
    }
}
