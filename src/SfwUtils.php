<?php

    namespace Softinline\SfwComponent;
    
    use Illuminate\Http\Request;

    class SfwUtils
    {

        /**
         * get all strings between two chars
         */
        public static function findAllBetween($str, $startDelimiter, $endDelimiter) {

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
