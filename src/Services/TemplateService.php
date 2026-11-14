<?php

    namespace Softinline\SfwComponent\Services;

    class TemplateService
    {
        /**
         * Replace all placeholders found between two chars with values from data.
         *
         * @param string $str
         * @param string $startDelimiter
         * @param string $endDelimiter
         * @param array $data
         * @return string
         */
        public static function replaceAllBetweenChars($str, $startDelimiter, $endDelimiter, $data = [])
        {
            $occurrences = CommonService::findAllBetweenChars($str, $startDelimiter, $endDelimiter);

            foreach ($occurrences as $occurrence) {
                $value = self::resolvePlaceholderValue($occurrence, $data);

                if ($value === null || $value === '') {
                    continue;
                }

                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }

                $str = str_replace($startDelimiter.$occurrence.$endDelimiter, (string) $value, $str);
            }

            return $str;
        }

        /**
         * Resolve a placeholder value from data, supporting dot notation.
         *
         * @param string $key
         * @param array $data
         * @return mixed
         */
        protected static function resolvePlaceholderValue($key, $data)
        {
            if (is_array($data) && array_key_exists($key, $data)) {
                return $data[$key];
            }

            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key);
                $cur = $data;

                foreach ($parts as $p) {
                    if (is_array($cur) && array_key_exists($p, $cur)) {
                        $cur = $cur[$p];
                    } elseif (is_object($cur) && isset($cur->{$p})) {
                        $cur = $cur->{$p};
                    } else {
                        return null;
                    }
                }

                return $cur;
            }

            if (is_object($data) && isset($data->{$key})) {
                return $data->{$key};
            }

            return null;
        }
    }
