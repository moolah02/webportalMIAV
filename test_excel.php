<?php
// Create this file as a temporary test script: test_excel.php
// Put this in your Laravel root directory and run it to test Excel reading

require_once 'vendor/autoload.php';

function testExcelReading($filePath) {
    try {
        echo "Testing Excel file: $filePath\n";
        
        if (!file_exists($filePath)) {
            echo "File does not exist!\n";
            return;
        }

        // Use PHPExcel to read the file
        $inputFileType = PHPExcel_IOFactory::identify($filePath);
        echo "File type detected: $inputFileType\n";
        
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filePath);
        
        $worksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        
        echo "Highest Row: $highestRow\n";
        echo "Highest Column: $highestColumn\n";
        
        // Read first 5 rows
        for ($row = 1; $row <= min(5, $highestRow); $row++) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                $rowData[] = $cellValue;
            }
            echo "Row $row: " . implode(' | ', $rowData) . "\n";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Test your Excel file
testExcelReading('C:\Users\hp\Downloads\DUMMY DATA.xlsx');
?>