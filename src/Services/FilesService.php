<?php

    namespace Softinline\SfwComponent\Services;
    
    use Softinline\SfwComponent;
        
    class FilesService
    {

        /**
         * upload         
         */
        public static function upload() {
        }

        /**
         * setMain
         */
        public static function setMain($table, $rowId) {
        }

        /**
         * delete
         * delete files from table and rowId
         */
        public static function delete($table, $rowId) {            
        }

        /**
         * deleteById
         * @param id: id of the file row
         * @param deleteForRelatedObject: set if delete for the same object all files 
         */
        public static function deleteById($id, $deleteForRelatedObject = false) {
        }

        /**
         * view
         * view image by Id
         */
        public static function view($id) {
        }

        /**
         * viewByRowId
         * find images(s) for the table and rowId, can get only main image, 
         * or images filter by category
         */
        public static function viewByRowId($table, $rowId, $main = true, $category = null) {
        }

    }