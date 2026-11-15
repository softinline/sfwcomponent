<?php

namespace Softinline\SfwComponent\Services;

use Illuminate\Support\Facades\Request;
use Softinline\SfwComponent\Models\SfwLanguage;
use Softinline\SfwComponent\Models\SfwTranslation;

class TranslationsService
{
    /**
     * return array of translation
     * @return object
     */
    public static function getTranslation($object, $table, $rowId, $fields = [], $edit = true)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        // get languages
        $languages = SfwLanguage::getAll();

        $data = [];

        // init
        foreach ($fields as $field) {
            $data[$field] = [];
            foreach ($languages as $language) {
                // editing init value
                if ($edit) {
                    $data[$field][$language->id] = '';
                }
                // not editing set def value
                else {
                    $data[$field][$language->id] = $object->{$field};
                }
            }
        }

        // get translation
        $translations = SfwTranslation::query()
            ->where('table', '=', $table)
            ->where('row_id', '=', $rowId)
            ->get();

        // add translations found
        foreach ($translations as $translation) {
            $temp = json_decode($translation->translation, true);
            $field = $translation->field;
            foreach ($languages as $language) {
                // have translation
                if (isset($temp[$language->id])) {
                    // if data is blank and not editing, add default
                    if (($temp[$language->id] == '') && (!$edit)) {
                        $data[$field][$language->id] = $object->{$field};
                    } else {
                        $data[$field][$language->id] = $temp[$language->id];
                    }
                }
                // dont have translation
                else {
                    if (!$edit) {
                        $data[$field][$language->id] = $object->{$field};
                    }
                }
            }
        }

        // only one field, return this directly
        if (count($fields) == 1) {
            return $data[$fields[0]];
        }
        // return all fields requested
        else {
            return $data;
        }
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
