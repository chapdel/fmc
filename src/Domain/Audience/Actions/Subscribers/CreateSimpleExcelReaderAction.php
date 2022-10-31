<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use OpenSpout\Common\Type;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use Spatie\SimpleExcel\SimpleExcelReader;
use SplFileObject;

class CreateSimpleExcelReaderAction
{
    public function execute(string $path): SimpleExcelReader
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $type = Type::CSV;

        if ($extension === 'xlsx' || $extension === 'xls') {
            $type = Type::XLSX;
        }

        return SimpleExcelReader::create($path, $type)
            ->useDelimiter($this->getCsvDelimiter($path));
    }

    /**
     * @param  string  $filePath
     * @param  int  $checkLines
     * @return string
     */
    protected function getCsvDelimiter(string $filePath, int $checkLines = 3): string
    {
        $delimiters = [',', ';', "\t", '|'];

        $fileObject = new SplFileObject($filePath);
        $results = [];
        $counter = 0;

        while ($fileObject->valid() && $counter <= $checkLines) {
            $line = $fileObject->fgets();

            foreach ($delimiters as $delimiter) {
                $fields = explode($delimiter, $line);
                $totalFields = count($fields);
                if ($totalFields > 1) {
                    if (! empty($results[$delimiter])) {
                        $results[$delimiter] += $totalFields;
                    } else {
                        $results[$delimiter] = $totalFields;
                    }
                }
            }
            $counter++;
        }

        if (! empty($results)) {
            $results = array_keys($results, max($results));

            return $results[0];
        }

        return ',';
    }
}
