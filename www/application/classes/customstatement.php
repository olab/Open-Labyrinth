<?php

use TinCan\Statement;
use TinCan\VersionableInterface;

class CustomStatement extends Statement
{
    /**
     * Collects defined object properties for a given version into an array
     *
     * @param  mixed $version
     * @return array
     */
    public function asVersion($version) {
        $result = array();

        foreach (get_object_vars($this) as $property => $value) {
            //
            // skip properties that start with an underscore to allow
            // storing information that isn't included in statement
            // structure etc. (see Attachment.content for example)
            //
            if (strpos($property, '_') === 0) {
                continue;
            }

            if ($value instanceof VersionableInterface) {
                $value = $value->asVersion($version);
            }
            if (isset($value)) {
                $result[$property] = $value;
            }
        }

        $this->_asVersion($result, $version);

        return $result;
    }

    private function _asVersion(&$result, $version) {
        foreach ($result as $property => $value) {
            if ((is_array($value) || is_object($value) || is_null($value)) && empty($value)) {
                unset($result[$property]);
            }
            elseif (is_array($value)) {
                $this->_asVersion($value, $version);
                $result[$property] = $value;
            }
            elseif ($value instanceof VersionableInterface) {
                $result[$property] = $value->asVersion($version);
            }
        }
        if (isset($result['target'])) {
            $result['object'] = $result['target'];
            unset($result['target']);
        }
    }
}
