<?php

	abstract class Helper
	{
		//format text by turning newlines into double <br />'s
		public static function escapes2html ($string)
		{
			$out = '';
			for ($i = 0; $i<strlen($string); $i++)
			{
				$code = ord(substr($string, $i, $i+1));
				if ($code==13)
					$out .= '<br /><br />';
				else
					$out .= chr($code);
			}
			return $out;
		}
		
		public static function alphaParse ($string, $allowNums)
		{
			$theWord = strtolower($string);
			$parsedWord = '';
			$noNums = true;
			for ($j=0; $j<strlen($theWord); $j++)
			{
				$theChar = substr($theWord, $j, 1);
				if ((ord($theChar)>=97 && ord($theChar)<=122) || ($allowNums && (ord($theChar>=48 && ord($theChar<=57)))) || ord($theChar)==32)
				{
					$parsedWord .= $theChar;
				}
				
				$noNums &= ord($theChar)<48 || ord($theChar)>57;
			}
			
			return !($allowNums || $noNums) ? false : $parsedWord;
		}

		public static function croppedImageSizeString($imageSize, $h, $w)
		{
			return ($imageSize[0]/$imageSize[1] < $w/$h ? 'width="'.$w.'"' : 'height="'.$h.'"');
		}
		
		public static function constrainedImageWidth($imageSize, $h, $w)
		{
			return ($imageSize[0]/$imageSize[1] >= $w/$h ? min($w, $imageSize[0]) : $imageSize[0]/$imageSize[1]*min($h, $imageSize[1]));
		}
		
		public static function constrainedImageHeight($imageSize, $h, $w)
		{
			return ($imageSize[0]/$imageSize[1] >= $w/$h ? $imageSize[1]/$imageSize[0]*min($w, $imageSize[0]) : min($h, $imageSize[1]));
		}
		
		public static function croppedDivSizeString ($imageSize, $h, $w)
		{
			return 'class="imgWrapper" style="width: '.$w.'px; height: '.$h.'px; overflow: hidden"';
		}
		
		//return the first integer to appear in a string, or 0 if not integer is found
		public static function firstInt ($string)
		{
			for ($i=0; $i<strlen($string); $i++)
			{
				if (intval(substr($string, $i)) > 0)
				{
					return intval(substr($string, $i));
				}
			}
			return 0;
		}
	}
?>