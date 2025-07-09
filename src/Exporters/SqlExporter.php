<?php 

namespace Kodventure\LaravelDataExporter\Exporters;

use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;

class SqlExporter extends BaseExporter
{
    protected string $tableName = 'exported_table';
    protected int $insertBatchSize = 500; // Varsayılan batch boyutu
    protected bool $useBatchInsert = true; // Batch insert kullanılsın mı?

    /**
     * Export işlemini gerçekleştirir
     */
    public function export(ExportSourceInterface $exportSource): ExportedFileDTO
    {
        $headers = $exportSource->getHeaders();
        $rows = $exportSource->getIterator();
    
        // Önce tablo oluşturma SQL'ini oluştur
        $sql = $this->generateCreateTableSql($headers, $rows) . "\n\n";
        
        if ($this->useBatchInsert) {
            $sql .= $this->generateBatchInserts($headers, $rows);
        } else {
            $sql .= $this->generateSingleInserts($headers, $rows);
        }
    
        return $this->exportFile($sql);
    }

    /**
     * INSERT INTO deyimlerini tek tek oluşturur
     */
    protected function generateSingleInserts(array $headers, iterable $rows): string
    {
        $sql = '';
        foreach ($rows as $row) {
            $rowArray = (array) $row;
            $columns = implode(', ', array_map(fn($col) => "`$col`", $headers));
            $values = implode(', ', array_map(fn($col) => $this->quote($rowArray[$col] ?? null), $headers));
            $sql .= "INSERT INTO `{$this->tableName}` ($columns) VALUES ($values);\n";
        }
        return $sql;
    }

    /**
     * INSERT INTO deyimlerini batch'ler halinde oluşturur
     */
    protected function generateBatchInserts(array $headers, iterable $rows): string
    {
        $sql = '';
        $batch = [];
        $columns = implode(', ', array_map(fn($col) => "`$col`", $headers));
        
        foreach ($rows as $row) {
            $rowArray = (array) $row;
            $values = implode(', ', array_map(fn($col) => $this->quote($rowArray[$col] ?? null), $headers));
            $batch[] = "($values)";
            
            if (count($batch) >= $this->insertBatchSize) {
                $sql .= "INSERT INTO `{$this->tableName}` ($columns) VALUES \n" . 
                       implode(",\n", $batch) . ";\n\n";
                $batch = [];
            }
        }
        
        // Kalan kayıtları da ekle
        if (!empty($batch)) {
            $sql .= "INSERT INTO `{$this->tableName}` ($columns) VALUES \n" . 
                   implode(",\n", $batch) . ";\n";
        }
        
        return $sql;
    }

    /**
     * Tablo oluşturma SQL'ini oluşturur
     */
    protected function generateCreateTableSql(array $headers, iterable $rows): string
    {
        $sql = "DROP TABLE IF EXISTS `{$this->tableName}`;\n\n";
        $sql .= "CREATE TABLE `{$this->tableName}` (\n";
        
        // Sütun tanımları
        $columnDefs = [];
        foreach ($headers as $index => $header) {
            // İlk sütun için UNSIGNED INTEGER, diğerleri için TEXT kullan
            $columnType = $index === 0 ? 'INTEGER' : 'TEXT';
            $columnDefs[] = "    `$header` $columnType";
        }
        
        // Primary key olarak ilk sütunu kullan
        if (count($headers) > 0) {
            $firstColumn = $headers[0];
            $columnDefs[] = "    PRIMARY KEY (`$firstColumn`)";
        }
        
        $sql .= implode(",\n", $columnDefs) . "\n);";
        
        return $sql;
    }

    /**
     * Tablo adını ayarlar
     */
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Batch insert kullanılıp kullanılmayacağını ayarlar
     */
    public function useBatchInsert(bool $useBatch = true): self
    {
        $this->useBatchInsert = $useBatch;
        return $this;
    }

    /**
     * Batch boyutunu ayarlar (sadece useBatchInsert true ise geçerli)
     */
    public function setBatchSize(int $size): self
    {
        $this->insertBatchSize = max(1, $size); // En az 1 olmalı
        return $this;
    }

    protected function quote($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }
    
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
    
        return "'" . addslashes((string) $value) . "'";
    }
}