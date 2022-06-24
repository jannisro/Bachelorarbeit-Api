<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CsvBuildService
{

    public static function build(Collection $datarows, array $fieldNames): string
    {
        $file = fopen('php://temp', 'r+');
        fputcsv($file, $fieldNames);

        foreach ($datarows as $row) {
            $values = [];
            foreach ($fieldNames as $field) {
                $values[] = $row->$field;
            }
            fputcsv($file, $values);
        }

        rewind($file);
        $data = fread($file, 1048576);
        fclose($file);
        return rtrim($data, "\n");
    }

}