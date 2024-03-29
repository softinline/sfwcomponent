<?php

    namespace Softinline\SfwComponent\Services;
    
    use Softinline\SfwComponent;
        
    class CommonService
    {

        /**
         * findAllBetween
         * get all strings between two chars
         */
        public static function findAllBetweenChars($str, $startDelimiter, $endDelimiter) {

            $contents = array();
            $startDelimiterLength = strlen($startDelimiter);
            $endDelimiterLength = strlen($endDelimiter);
            $startFrom = $contentStart = $contentEnd = 0;

            while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {

              $contentStart += $startDelimiterLength;
              $contentEnd = strpos($str, $endDelimiter, $contentStart);

              if (false === $contentEnd) {
                break;
              }

              $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
              $startFrom = $contentEnd + $endDelimiterLength;
              
            }
          
            return $contents;
            
        }

    }