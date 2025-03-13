<?php
/* PHP 7.4 compatiable */

class FileSearch {
    private $directory;
    private $pattern;
    private $recursive;
    
    public function __construct($directory = ".", $pattern = null, $recursive = true) {
        $this->directory = $directory;
        $this->pattern = $pattern;
        $this->recursive = $recursive;
    
    }
    
    public function deleteFiles() {
        // Initialize the search class
        $fileSearch = new FileSearch();
            
        // Example 1: Find all original image files (excluding resized versions)
        $pattern = '/^(?!.*-\d+x\d+).*\.(jpe?g|png|gif|bmp|ico)$/i';
        $fileSearch->setPattern($pattern);
        
        
        //FIND ORIGINAL IMAGES
        // echo "Finding original image files:\n";
        $files = $fileSearch->findFiles();
        foreach ($files as $file) {
            echo $file . "\n";

            // Usage example:  unlink($file);
            if ($fileSearch->safeDeleteFile($file)) {
                echo "File successfully deleted";
            } else {
                echo "Failed to delete file";
            }
            }
        }

    public function safeDeleteFile($file) {
        try {
            // Check if file exists
            if (!file_exists($file)) {
                throw new Exception("File does not exist: $file");
            }
    
            // Check if we have permission to delete
            if (!is_writable($file)) {
                throw new Exception("No permission to delete file: $file");
            }
    
            // Optional: Create backup before deletion
            // copy($file, $file . '.backup');
    
            // Delete the file
            if (unlink($file)) {
                return true;
            } else {
                throw new Exception("Failed to delete file: $file");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
   
    
    
    public function findFiles() {
        try {
            // Validate directory
            if (!is_dir($this->directory)) {
                throw new Exception("Directory does not exist: " . $this->directory);
            }
            
            // Create Directory Iterator
            $iterator = new RecursiveDirectoryIterator($this->directory);
            
            // Make it recursive if needed
            if ($this->recursive) {
                $iterator = new RecursiveIteratorIterator($iterator);
            }
            
            // Create Regex Iterator
            $files = new RegexIterator($iterator, $this->pattern, RegexIterator::MATCH);
            
            $results = [];
            foreach ($files as $file) {
                $results[] = $file->getPathname();
            }
            
            return $results;
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return [];
        }
    }
    
    public function setPattern($pattern) {
        $this->pattern = $pattern;
    }
    
    public function setDirectory($directory) {
        $this->directory = $directory;
    }
}

// Example usage
try {
    // Initialize the search class
    $fileSearch = new FileSearch();
    
    // Example 1: Find all original image files (excluding resized versions)
    $pattern = '/^(?!.*-\d+x\d+).*\.(jpe?g|png|gif|bmp|ico)$/i';
    $fileSearch->setPattern($pattern);
    
    
    //FIND ORIGINAL IMAGES
    echo "Finding original image files:\n";
    $files = $fileSearch->findFiles();
    foreach ($files as $file) {
        echo $file . "\n";
    }
    
    //DELETE ORIGINAL IMAGES
    echo "Deleting original image files:\n";
    $files = $fileSearch->deleteFiles();
    // foreach ($files as $file) {
    //     echo $file . "\n";
    // }
    
    // Example 2: Find resized image files
    $pattern = '/.*-\d+x\d+\.(jpe?g|png|gif|bmp|ico)$/i';
    $fileSearch->setPattern($pattern);
    
    //FIND RESIZED IMAGES
    echo "\nFinding resized image files:\n";
    $files = $fileSearch->findFiles();
    foreach ($files as $file) {
        echo $file . "\n";
    }
    
    // Example 3: Find specific file types
    $pattern = '/\.(pdf|doc|docx)$/i';
    $fileSearch->setPattern($pattern);
    
    echo "\nFinding documents:\n";
    $files = $fileSearch->findFiles();
    foreach ($files as $file) {
        echo $file . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example with specific directory
function searchInDirectory($directory, $pattern) {
    $fileSearch = new FileSearch($directory, $pattern);
    return $fileSearch->findFiles();
}

// Usage examples:
$imagePattern = '/^(?!.*-\d+x\d+).*\.(jpe?g|png|gif|bmp|ico)$/i';
$directory = '../10';

$files = searchInDirectory($directory, $imagePattern);
foreach ($files as $file) {
    echo $file . "\n";
}
