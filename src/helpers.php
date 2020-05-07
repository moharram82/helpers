<?php

if (!function_exists('getMySQLEnumValues')) {
    /**
     * Extracts values from MySQL's enum field definition: enum('male','female')
     *
     * @param mysqli|PDO|Illuminate\Database\MySqlConnection $connection object
     * @param string $table name
     * @param string $enumCol enum field name
     * @return false|array the values of the column definition
     */
    function getMySQLEnumValues($connection, $table, $enumCol)
    {
        $enumType = '';

        $sql = "SHOW COLUMNS FROM {$table} LIKE '{$enumCol}'";

        if($connection instanceof PDO || $connection instanceof Illuminate\Database\MySqlConnection) {
            if ($connection instanceof Illuminate\Database\MySqlConnection) {
                $connection = $connection->getPdo();
            }

            $enumType = $connection->query($sql)->fetchColumn(1);
        } elseif ($connection instanceof mysqli) {
            $enumType = $connection->query($sql)->fetch_assoc()['Type'];
        } else {
            throw new InvalidArgumentException("The connection instance you provided is not supported.");
        }

        if(! $enumType) {
            throw new InvalidArgumentException("Column '{$enumCol}' does not exist in '{$table}' table.");
        }

        if(is_string($enumType) && false === strpos($enumType, "enum(")) {
            throw new RuntimeException("Column '{$enumCol}' is not of type enum");
        }

        $output = str_replace("enum('", "", $enumType);
        $output = str_replace("')", "", $output);

        return explode("','", $output);
    }
}

if (!function_exists('validUsername')) {
    /**
     * Checks if the supplied string is a valid username that:
     * - is in English only characters
     * - contains no spaces
     * - starts with letters only
     * - contains only letters, numbers and underscores
     * - does not end with underscores
     *
     * @param string $username the string to be checked
     * @return bool if the string is english returns true else returns false
     */
    function validUsername($username)
    {
        if(preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]+[a-zA-Z0-9]$/', $username)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('isPasswordStrong')) {
    /**
     * Checks if the supplied password is strong, which must be:
     * - contain at least 1 uppercase letter
     * - contain at least 1 lowercase letter
     * - contain at least 1 number
     * - at least 8 characters long
     *
     * @param string $password the password to be tested
     * @return bool true if the password is strong, false if it is weak
     */
    function isPasswordStrong($password)
    {
        if(preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $password)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('isEnglish')) {
    /**
     * Checks if the supplied string is:
     * - English letters only
     * - may contain spaces in between words but not at the end
     *
     * @param string $string the string to be checked
     * @return bool true or false
     */
    function isEnglish($string)
    {
        if(preg_match('/^[a-zA-Z]+\s?[a-zA-Z]+$/', $string)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('validTimestamp')) {
    /**
     * Checks if a string is a valid timestamp.
     *
     * @param  string $timestamp Timestamp to be validated.
     * @return bool true if TIMESTAMP or false if not.
     */
    function validTimestamp($timestamp)
    {

        $check = (is_int($timestamp) OR is_float($timestamp)) ? $timestamp : (string) (int) $timestamp;

        return  ($check === $timestamp)	AND ((int) $timestamp <=  PHP_INT_MAX)	AND ((int) $timestamp >= ~PHP_INT_MAX);
    }
}

if (!function_exists('removeHttpProtocol')) {
    /**
     * Removes protocol prefix (http://) or (https://) from a link
     *
     * @param string $url the URL to be cleaned from protocol prefix
     * @return string $url the URL without the protocol
     */
    function removeHttpProtocol($url)
    {

        $disallowed = array('http://', 'https://');

        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }

        return $url;
    }
}

if (!function_exists('trimExtraSpace')) {
    /**
     * Removes any extra white spaces from the beginning, end, and in between words.
     *
     * @param string $string the text to be cleared from extra white spaces.
     * @return string the cleaned text.
     */
    function trimExtraSpace($string)
    {
        return trim(preg_replace('/\s\s+/', ' ', $string));
    }
}

if (!function_exists('cleanText')) {
    /**
     * Cleans text from any html and duplicate spaces or new lines.
     *
     * @param string $str the string to be cleaned
     * @return mixed|string the cleaned string
     */
    function cleanText($str)
    {
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        $str = strip_tags($str); // erases any html markup
        $str = preg_replace('/\s\s+/', ' ', $str); // erases possible duplicated white spaces
        $str = str_replace(array('\r\n', '\n', '+'), ',', $str); // erases new lines

        return $str;
    }
}

if (!function_exists('validEmail')) {
    /**
     * Checks if the string is a valid email address.
     *
     * @param string $email e-mail address to be validated.
     * @return boolean true or false
     */
    function validEmail($email)
    {
        $isValid = true;
        $atIndex = strrpos($email, "@");

        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                    $isValid = false;
                }
            }

            if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }

        return $isValid;
    }
}

if (!function_exists('validDate')) {
    /**
     * Checks if string is a valid date.
     *
     * @param string $date the date to be validated...
     * @param string $format the format against which the date will be validated (YYYY-MM-DD) or (DD/MM/YYYY)
     * @return bool true or false.
     */
    function validDate($date, $format = 'YYYY-MM-DD') {
        $year = "";
        $month = "";
        $day = "";

        if (strlen($date) >= 8 && strlen($date) <= 10) {
            $separator_only = str_replace(array('M', 'D', 'Y'), '', $format);
            $separator = $separator_only[0];
            if ($separator) {
                $regexp = str_replace($separator, "\\" . $separator, $format);
                $regexp = str_replace('MM', '(0[1-9]|1[0-2])', $regexp);
                $regexp = str_replace('M', '(0?[1-9]|1[0-2])', $regexp);
                $regexp = str_replace('DD', '(0[1-9]|[1-2][0-9]|3[0-1])', $regexp);
                $regexp = str_replace('D', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
                $regexp = str_replace('YYYY', '\d{4}', $regexp);
                $regexp = str_replace('YY', '\d{2}', $regexp);
                if ($regexp != $date && preg_match('/' . $regexp . '$/', $date)) {
                    foreach (array_combine(explode($separator, $format), explode($separator, $date)) as $key => $value) {
                        if ($key == 'YY')
                            $year = '20' . $value;
                        if ($key == 'YYYY')
                            $year = $value;
                        if ($key[0] == 'M')
                            $month = $value;
                        if ($key[0] == 'D')
                            $day = $value;
                    }

                    if (checkdate($month, $day, $year))
                        return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('validUrl')) {
    /**
     * Checks if string is a valid URL address.
     *
     * @param string $url the URL address to be validated
     * @return bool true if valid URL or false if not
     */
    function validUrl($url)
    {
        if(preg_match("/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/", $url)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('calculateAge')) {
    /**
     * Calculates the age for a given birth date and returns the age in years, months, days, decimal.
     *
     * @param string $birthdate in (yyyy-mm-dd) format
     * @param string $unit ("years", "months", "days" or "decimal").
     * @return bool|mixed false if $birthdate is not a valid date, integer if the unit is (years, months or days), float if the unit is decimal.
     */
    function calculateAge($birthdate, $unit = 'years')
    {
        if(!validDate($birthdate)) {
            return false;
        }

        list($year,$month,$day) = explode("-",$birthdate);
        $year_diff  = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff   = date("d") - $day;

        if($month_diff < 0) {
            $year_diff--;
            $month_diff = 12 + $month_diff;
        }

        if($day_diff < 0) {
            $month_diff--;
            $day_diff = 30 + $day_diff;
        }

        $year_diff_in_months = $year_diff * 12;
        $year_diff_in_days = $year_diff_in_months * 30;
        $months_diff_in_days = $month_diff  * 30;

        $age_in_months = $year_diff_in_months + $month_diff;
        $age_in_days = $year_diff_in_days + $months_diff_in_days + $day_diff;
        $float_age = $age_in_days / 30 / 12;

        switch($unit) {
            case 'years':
                return $year_diff;
                break;

            case 'decimal':
                return round($float_age, 1);
                break;

            case 'months':
                return $age_in_months;
                break;

            case 'days':
                return $age_in_days;
                break;

            default:
                return $year_diff;
                break;
        }
    }
}

if (!function_exists('imgResize')) {
    /**
     * Resize an image.
     *
     * @param string $target absolute path of the original image to be re-sized
     * @param string $newCopy absolute path of the newly re-sized image
     * @param int $width of the newly created image
     * @param int $height of the newly created image
     * @param string $extension of the newly created image file
     */
    function imgResize($target, $newCopy, $width, $height, $extension)
    {
        list($w_orig, $h_orig) = getimagesize($target);

        $scale_ratio = $w_orig / $h_orig;

        if (($width / $height) > $scale_ratio) {
            $width = $height * $scale_ratio;
        } else {
            $height = $width / $scale_ratio;
        }

        $img = "";
        $extension = strtolower($extension);

        if ($extension == "gif") {
            $img = imagecreatefromgif($target);
        } else if ($extension == "png") {
            $img = imagecreatefrompng($target);
        } else {
            $img = imagecreatefromjpeg($target);
        }

        $tci = imagecreatetruecolor($width, $height);

        imagecopyresampled($tci, $img, 0, 0, 0, 0, $width, $height, $w_orig, $h_orig);
        imagejpeg($tci, $newCopy, 80);
    }
}

if (!function_exists('getIP')) {
    /**
     * Returns the IP address of the current internet session
     *
     * @return string the ip address
     */
    function getIP() {
        $ip = '';

        $client = (isset($_SERVER['HTTP_CLIENT_IP']) AND $_SERVER['HTTP_CLIENT_IP'] != "") ? $_SERVER['HTTP_CLIENT_IP'] : FALSE;
        $remote = (isset($_SERVER['REMOTE_ADDR']) AND $_SERVER['REMOTE_ADDR'] != "") ? $_SERVER['REMOTE_ADDR'] : FALSE;
        $forward = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND $_SERVER['HTTP_X_FORWARDED_FOR'] != "") ? $_SERVER['HTTP_X_FORWARDED_FOR'] : FALSE;

        if($client && $remote)	$ip = $client;
        elseif($remote)			$ip = $remote;
        elseif($client)			$ip = $client;
        elseif($forward)		$ip = $forward;

        if(strpos($ip, ',') !== FALSE)
        {
            $x = explode(',', $ip);
            $ip = end($x);
        }

        if(!filter_var($ip, FILTER_VALIDATE_IP))
        {
            $ip = '0.0.0.0';
        }

        unset($client);
        unset($remote);
        unset($forward);

        return $ip;
    }
}

if (!function_exists('getUseragent')) {
    /**
     * Returns the useragent string of the current internet session
     *
     * @return	string
     */
    function getUseragent()
    {
        return (! isset($_SERVER['HTTP_USER_AGENT'])) ? false : $_SERVER['HTTP_USER_AGENT'];
    }
}

if (!function_exists('randomString')) {
    /**
     * Generates random alpha-numeric string
     *
     * @param int $length length of the string
     * @return string generated random string
     */
    function randomString($length = 16)
    {
        $string = '';

        $keys = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

        for($i = 0; $i < $length; $i++) {
            $string .= $keys[array_rand($keys)];
        }

        return $string;
    }
}

if (!function_exists('isWindows')) {
    /**
     * Checks if the current environment is Windows based.
     *
     * @return bool
     */
    function isWindows()
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
    }
}

if (!function_exists('removeDir')) {
    /**
     * Recursively deletes a directory and its entire contents
     *
     * @param  string $dir absolute path to the directory to be deleted
     * @return bool true/false return true on success or false on error
     */
    function removeDir($dir)
    {
        if(is_dir($dir)) {
            $objects = scandir($dir);

            foreach($objects as $object) {
                if($object != "." && $object != "..") {
                    if(filetype($dir."/".$object) == "dir") removeDir($dir."/".$object); else unlink($dir."/".$object);
                }
            }

            reset($objects);

            return (rmdir($dir) === true) ? true : false;
        }

        return false;
    }
}

if (!function_exists('scanDirForFiles')) {
    /**
     * Scans a dir and return an array of files based on file extension (jpg|png|gif).
     *
     * @param string $dir absolute path of the directory to be scanned for images.
     * @param bool $images if should find files of type images, default true.
     * @param array $extensions of the images types to be found, default (jpg|png|gif).
     * @param bool $names if should return only file names not the full file path.
     * @param bool $sort if the found files should be sorted alphabetically.
     * @return array|bool returns false if no files were found, or an array containing found files.
     */
    function scanDirForFiles($dir, $images = true, $extensions = [], $names = false, $sort = false)
    {
        $dir = rtrim(str_replace("\\", "/", $dir), '/');
        $files = [];

        if($images) {
            if(empty($extensions)) {
                $extensions = ['jpg', 'png', 'gif'];
            }
        }

        if(is_dir($dir)) {
            // read dir
            $dir_handler = opendir($dir);

            while($file = readdir($dir_handler)) {
                if(! is_dir($file)) {
                    if(! empty($extensions)) {
                        foreach($extensions as $extension) {
                            if(strpos($file, '.' . strtoupper($extension)) || strpos($file, '.' . strtolower($extension))) {
                                if($names) {
                                    $files[] = $file;
                                } else {
                                    $files[] = $dir . '/'. $file;
                                }
                            }
                        }
                    } else {
                        if($names) {
                            $files[] = $file;
                        } else {
                            $files[] = $dir . '/'. $file;
                        }
                    }
                }
            }

            closedir($dir_handler);

            if ($sort) {
                sort($files);
            }
        }

        return (count($files) > 0) ? $files : false;
    }
}
