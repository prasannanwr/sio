<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transactions')) {
            return;
        }

        $this->dropForeignKeyIfExists('transactions', 'beneficiary_id');
        $this->dropForeignKeyIfExists('transactions', 'beneficiary_account_id');

        if (DB::getDriverName() === 'mysql') {
            $this->modifyMysqlColumns();
        } else {
            $this->modifyColumnsWithSchemaBuilder();
        }

        $this->addForeignKeyIfMissing('transactions', 'beneficiary_id', 'beneficiaries');
        $this->addForeignKeyIfMissing('transactions', 'beneficiary_account_id', 'beneficiary_accounts');
    }

    public function down(): void
    {
        // Non-transfer payment rows may now legitimately have null beneficiary fields.
    }

    private function modifyMysqlColumns(): void
    {
        $columns = [
            'transaction_type' => 'VARCHAR(255) NULL',
            'beneficiary_id' => 'BIGINT UNSIGNED NULL',
            'beneficiary_account_id' => 'BIGINT UNSIGNED NULL',
            'transaction_id' => 'VARCHAR(255) NULL',
            'transaction_amount' => 'DOUBLE NULL',
            'beneficiary_account_number' => 'VARCHAR(255) NULL',
            'beneficiary_account_name' => 'VARCHAR(255) NULL',
            'beneficiary_bank_name' => 'VARCHAR(255) NULL',
            'beneficiary_bank_code' => 'VARCHAR(255) NULL',
            'payment_provider' => 'VARCHAR(255) NULL',
            'payment_intent' => 'VARCHAR(255) NULL',
        ];

        foreach ($columns as $column => $definition) {
            if (Schema::hasColumn('transactions', $column)) {
                DB::statement(sprintf(
                    'ALTER TABLE %s MODIFY %s %s',
                    $this->quoteIdentifier('transactions'),
                    $this->quoteIdentifier($column),
                    $definition
                ));
            }
        }
    }

    private function modifyColumnsWithSchemaBuilder(): void
    {
        Schema::table('transactions', function ($table) {
            foreach ([
                'transaction_type',
                'transaction_id',
                'beneficiary_account_number',
                'beneficiary_account_name',
                'beneficiary_bank_name',
                'beneficiary_bank_code',
                'payment_provider',
                'payment_intent',
            ] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->string($column)->nullable()->change();
                }
            }

            foreach (['beneficiary_id', 'beneficiary_account_id'] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->unsignedBigInteger($column)->nullable()->change();
                }
            }

            if (Schema::hasColumn('transactions', 'transaction_amount')) {
                $table->double('transaction_amount')->nullable()->change();
            }
        });
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $constraint = $this->foreignKeyName($table, $column);

        if (! $constraint) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE %s DROP FOREIGN KEY %s',
            $this->quoteIdentifier($table),
            $this->quoteIdentifier($constraint)
        ));
    }

    private function addForeignKeyIfMissing(string $table, string $column, string $referencedTable): void
    {
        if (! Schema::hasColumn($table, $column)
            || ! Schema::hasTable($referencedTable)
            || $this->foreignKeyName($table, $column)) {
            return;
        }

        try {
            DB::statement(sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)',
                $this->quoteIdentifier($table),
                $this->quoteIdentifier($table . '_' . $column . '_foreign'),
                $this->quoteIdentifier($column),
                $this->quoteIdentifier($referencedTable),
                $this->quoteIdentifier('id')
            ));
        } catch (\Throwable $e) {
            // Some non-MySQL drivers cannot add FKs after table creation.
        }
    }

    private function foreignKeyName(string $table, string $column): ?string
    {
        if (DB::getDriverName() !== 'mysql') {
            return null;
        }

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
};
