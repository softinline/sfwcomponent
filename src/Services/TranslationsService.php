<?php

namespace Softinline\SfwComponent\Services;

use Illuminate\Support\Facades\Request;
use Softinline\SfwComponent\Models\SfwLanguage;
use Softinline\SfwComponent\Models\SfwTranslation;

class TranslationsService
{
    /**
     * Get translations for one or more fields.
     *
     * @param mixed $object
     * @param string $table
     * @param string $rowId
     * @param string|array $fields
     * @param bool $edit
     * @return array
     */
    public static function getTranslation($object, $table, $rowId, $fields = [], $edit = true): array
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $languages = SfwLanguage::getAll();
        $data = [];

        foreach ($fields as $field) {
            $data[$field] = [];
            foreach ($languages as $language) {
                if ($edit) {
                    $data[$field][$language->id] = '';
                } else {
                    $data[$field][$language->id] = self::fieldValue($object, $field);
                }
            }
        }

        if ($rowId !== null && $rowId !== '') {
            $translations = SfwTranslation::query()
                ->where('table', '=', $table)
                ->where('row_id', '=', $rowId)
                ->get();

            foreach ($translations as $translation) {
                $temp = json_decode($translation->translation, true);
                if (!is_array($temp)) {
                    $temp = [];
                }

                $field = $translation->field;
                if (!array_key_exists($field, $data)) {
                    continue;
                }

                foreach ($languages as $language) {
                    if (isset($temp[$language->id])) {
                        if (($temp[$language->id] == '') && (!$edit)) {
                            $data[$field][$language->id] = self::fieldValue($object, $field);
                        } else {
                            $data[$field][$language->id] = $temp[$language->id];
                        }
                    } elseif (!$edit) {
                        $data[$field][$language->id] = self::fieldValue($object, $field);
                    }
                }
            }
        }

        if (count($fields) == 1) {
            return $data[$fields[0]] ?? [];
        }

        return $data;
    }

    /**
     * Get a field value from an object for non-edit translation fallback.
     *
     * @param mixed $object
     * @param string $field
     * @return mixed
     */
    private static function fieldValue($object, $field)
    {
        if (is_object($object) && isset($object->{$field})) {
            return $object->{$field};
        }

        if (is_array($object) && array_key_exists($field, $object)) {
            return $object[$field];
        }

        return '';
    }

    /**
     * set translation into DB
     * @return object
     */
    public static function setTranslation($table, $rowId, $aFields)
    {
        // get languages
        $languages = SfwLanguage::getAll();

        foreach ($aFields as $field) {

            // prepare array of translations
            $aTranslations = [];
            foreach ($languages as $language) {
                $key = $field . '_' . $language->id;
                if (Request::has($key)) {
                    $value = Request::get($key);
                    if ($value !== null && $value !== '') {
                        $aTranslations[$language->id] = $value;
                    }
                }
            }

            // check if exists
            $translation = SfwTranslation::query()
                ->where('table', '=', $table)
                ->where('row_id', '=', $rowId)
                ->where('field', '=', $field)
                ->first();

            if (count($aTranslations) > 0) {

                if (!$translation) {
                    $translation = new SfwTranslation();
                    $translation->id = uniqid("TR");
                    $translation->table = $table;
                    $translation->row_id = $rowId;
                    $translation->field = $field;
                }

                $translation->translation = json_encode($aTranslations);
                $translation->save();

            } else {

                // if exists but array is empty, delete
                if ($translation) {
                    $translation->delete();
                }

            }

        }

    }

    /**
     * Delete all translations for a record.
     *
     * @param string $table
     * @param string $rowId
     * @return void
     */
    public static function deleteTranslations($table, $rowId)
    {
        SfwTranslation::query()
            ->where('table', '=', $table)
            ->where('row_id', '=', $rowId)
            ->delete();
    }

    /**
     * copyTranslation
     * copy translation from original row to another row
     */
    public static function copyTranslation($tableT, $originId, $destinationId)
    {
        // pass translations
        $translations = SfwTranslation::query()
            ->where('table', '=', $tableT)
            ->where('row_id', '=', $originId)
            ->get();

        foreach ($translations as $translation) {
            $newTranslation = $translation->replicate();
            $newTranslation->id = uniqid("TR");
            $newTranslation->row_id = $destinationId;
            $newTranslation->save();
        }

    }

}
