<?php

namespace Advastore\Helper\Data;

class CSVGenerator
{
    const DELIMITER = ';';

    public function __construct()
    {}

    public function createFromArrays(array $data): string
    {
        $csvString = '';

        foreach ($data as $row) {
            $csvString .= $this->arrayToCsvString($row) . "\n";
        }

        return $csvString;
    }

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
