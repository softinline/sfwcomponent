<?php

    namespace Softinline\SfwComponent;
    
    use Illuminate\Http\Request;

    class SfwUtils
    {

        /**
		 * replaceUrlParams
		 * replace all params in rul {id}, {userId} etc... with their values in url
		 */
        public static function replaceUrlParams($url) {

            // replace dynamic {id} params on final redirect
            $occurences = \Softinline\SfwComponent\SfwUtils::findAllBetween($url, '{', '}');
            foreach($occurences as $occurence) {        
                if(\Request::route($occurence)) {
                    $url = str_replace('{'.$occurence.'}', \Request::route($occurence), $url);
                }
            }
			return $url;

        }

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
