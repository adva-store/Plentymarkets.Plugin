<?php

namespace Advastore\Helper\Data;

/**
 * Class CSVGenerator
 *
 * A class for generating CSV strings from arrays.
 */
class CSVGenerator
{
    /**
     * @var string The delimiter used to separate fields in the CSV.
     */
    const DELIMITER = ';';

    /**
     * Convert an array of arrays into a CSV string.
     *
     * @param array $data An array of arrays containing the data to be converted.
     * @return string Returns the generated CSV string.
     */
    public function createFromArrays(array $data): string
    {
        $csvString = '';

        foreach ($data as $row) {
            $csvString .= $this->arrayToCsvString($row) . "\n";
        }

        return $csvString;
    }

    /**
     * Convert an array to a CSV string.
     *
     * @param array $row The array containing the data to be converted to CSV.
     * @return string Returns the generated CSV string for the given array.
     */
    private function arrayToCsvString(array $row): string
    {
        $row = array_map(
            function ($item) {
                // Quote the item and escape any existing quotes
                return '"' . str_replace('"', '""', $item) . '"';
            },
            $row
        );

        return implode(self::DELIMITER, $row);
    }
}
