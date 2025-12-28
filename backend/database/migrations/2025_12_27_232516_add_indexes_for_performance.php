<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = Schema::getConnection();
        
        // Add indexes to transactions table (only if they don't exist)
        $this->addIndexIfNotExists($connection, 'transactions', 'status');
        $this->addIndexIfNotExists($connection, 'transactions', 'created_at');
        $this->addIndexIfNotExists($connection, 'transactions', ['status', 'created_at'], 'status_created_at');

        // Add indexes to orders table
        $this->addIndexIfNotExists($connection, 'orders', 'status');
        $this->addIndexIfNotExists($connection, 'orders', 'created_at');
        $this->addIndexIfNotExists($connection, 'orders', ['status', 'created_at'], 'status_created_at');
        $this->addIndexIfNotExists($connection, 'orders', 'user_id');

        // Add indexes to products table
        $this->addIndexIfNotExists($connection, 'products', 'is_active');
        $this->addIndexIfNotExists($connection, 'products', 'is_bundle');
        $this->addIndexIfNotExists($connection, 'products', ['is_active', 'is_bundle'], 'is_active_is_bundle');
    }

    private function addIndexIfNotExists($connection, string $table, $columns, ?string $indexName = null): void
    {
        $columnsArray = is_array($columns) ? $columns : [$columns];
        $indexName = $indexName ?? $columnsArray[0];
        $fullIndexName = $table . '_' . $indexName . '_index';

        try {
            $columnsString = is_array($columns) ? implode('`, `', $columns) : $columns;
            $connection->statement("ALTER TABLE `{$table}` ADD INDEX `{$fullIndexName}` (`{$columnsString}`)");
        } catch (\Exception $e) {
            // Index already exists, ignore
            if (str_contains($e->getMessage(), 'Duplicate key name')) {
                return;
            }
            throw $e;
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
            if ($this->hasIndex('orders', 'user_id')) {
                $table->dropIndex(['user_id']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_bundle']);
            $table->dropIndex(['is_active', 'is_bundle']);
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        $tableName = $table;
        $indexName = $connection->getTablePrefix() . $tableName . '_' . $index . '_index';

        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$databaseName, $tableName, $indexName]
        );

        return $result[0]->count > 0;
    }
};
