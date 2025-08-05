<?php
// fix_encoding.php - Fix the CSV encoding issue

function fixCSVEncoding($inputFile, $outputFile = null) {
    if (!$outputFile) {
        $outputFile = str_replace('.csv', '_fixed.csv', $inputFile);
    }
    
    echo "Reading file: $inputFile\n";
    
    if (!file_exists($inputFile)) {
        echo "Error: File does not exist!\n";
        return false;
    }
    
    // Read the raw file content
    $content = file_get_contents($inputFile);
    
    if ($content === false) {
        echo "Error: Could not read file!\n";
        return false;
    }
    
    echo "Original file size: " . strlen($content) . " bytes\n";
    
    // Detect and convert encoding
    $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true);
    echo "Detected encoding: " . ($encoding ?: 'Unknown') . "\n";
    
    if ($encoding && $encoding !== 'UTF-8') {
        echo "Converting from $encoding to UTF-8...\n";
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
    }
    
    // Save the converted content
    if (file_put_contents($outputFile, $content) !== false) {
        echo "Fixed file saved as: $outputFile\n";
        
        // Now test reading it as CSV
        echo "\nTesting CSV reading...\n";
        testCSVReading($outputFile);
        return true;
    } else {
        echo "Error: Could not save fixed file!\n";
        return false;
    }
}

function testCSVReading($filePath) {
    $row = 0;
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
            $row++;
            
            if ($row == 1) {
                // Show headers (first 15 columns)
                echo "HEADERS (first 15 columns):\n";
                for ($i = 0; $i < min(15, count($data)); $i++) {
                    $value = trim($data[$i]);
                    echo "  Column " . chr(65 + $i) . " ($i): '$value'\n";
                }
                echo "\n";
            } else if ($row <= 3) {
                // Show first few data rows (only non-empty columns)
                echo "ROW $row (non-empty columns):\n";
                for ($i = 0; $i < min(15, count($data)); $i++) {
                    $value = trim($data[$i]);
                    if (!empty($value)) {
                        echo "  Column " . chr(65 + $i) . " ($i): '$value'\n";
                    }
                }
                echo "\n";
            }
            
            if ($row >= 3) {
                echo "... (showing first 3 rows only)\n";
                break;
            }
        }
        fclose($handle);
        echo "\nTotal rows processed: $row\n";
        echo "Total columns in first row: " . count($data) . "\n";
    } else {
        echo "Error: Could not open file for CSV reading!\n";
    }
}

// Fix your CSV file
echo "=== CSV Encoding Fixer ===\n\n";
fixCSVEncoding('DUMMY DATA.csv');
?>