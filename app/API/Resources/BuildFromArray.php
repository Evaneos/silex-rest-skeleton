<?php

namespace Evaneos\REST\API\Resources;

trait BuildFromArray
{
    /**
     * Build a resource from array.
     *
     * @param array $data
     *
     * @return \Evaneos\REST\Http\ApiResources\BuildFromArray
     */
    public static function buildFromArray(array $data = array())
    {
        $object = new self();

        $properties = array_keys(get_class_vars(self::class));

        foreach ($properties as $property) {
            if (isset($data[$property])) {
                $object->{$property} = $data[$property];
            }
        }

        return $object;
    }
}
