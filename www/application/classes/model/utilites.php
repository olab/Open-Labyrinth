<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
defined('SYSPATH') or die('No direct script access.');

class Model_Utilites extends Model {

    private $hashMethod = 'sha256'; // Your hash method (must be the same with main configuration (config/auth.php))
    private $hashKey = '1, 3, 4, 6, 9, 13, 17, 20, 25, 30, 32, 40, 61'; // Your hash string (must be the same with main configuration (config/auth.php))
    private $chasher1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "_", "-", "?", "#", "<", ">", "&", "=", ".", " ");
    private $chasher2 = array("YSFT", "P8TS", "QNIS", "XJA6", "GQUA", "IAMO", "7WPB", "SK8Z", "NA00", "X_T5", "Z__H", "A192", "BB97", "ALZ1", "1ZHA", "BAAA", "N98&", "XUAS", "776A", "YZ8A", "9_Y-", "ASVF", "AHH7", "577P", "BSM-", "ZHAG", "S98T", "QQW8", "CIO1", "9ADP", "VBZ9", "CBAJ", "166H", "HDAO", "Sl9k", "__US", "SAU7", "9823", "H772", "KDSP", "C091", "5-NN", "7SG1", "HSS7", "IO06", "4WW6");

    public function hashPassword($message) {
        if ($message != '') {
            $hashMessage = hash_hmac($this->hashMethod, $message, $this->hashKey);

            $chasher = array();
            for ($i = 0; $i < count($this->chasher1); $i++) {
                $chasher[$this->chasher1[$i]] = $this->chasher2[$i];
            }

            $out = '';
            for ($i = 0; $i < strlen($hashMessage); $i++) {
                $out .= $chasher[$hashMessage[$i]];
            }

            return $out;
        }

        return '';
    }
    
    public function hashUsername($message) {
        if ($message != '') {
            $chasher = array();
            for ($i = 0; $i < count($this->chasher1); $i++) {
                $chasher[$this->chasher1[$i]] = $this->chasher2[$i];
            }

            $out = '';
            for ($i = 0; $i < strlen($message); $i++) {
                $out .= $chasher[$message[$i]];
            }

            return $out;
        }

        return '';
    }

    public function deHash($message) {
        if ($message != '') {
            $chasher = array();
            for ($i = 0; $i < count($this->chasher2); $i++) {
                $chasher[$this->chasher2[$i]] = $this->chasher1[$i];
            }

            $out = '';
            for ($i = 0; $i < strlen($message) - 2; $i += 4) {
                $block = $message[$i] . $message[$i + 1] . $message[$i + 2] . $message[$i + 3];
                $out .= $chasher[$block];
            }

            return $out;
        }

        return '';
    }

}

?>