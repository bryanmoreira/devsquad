<?php

declare(strict_types=1);

namespace Tests\Exam;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Migration Test
 * - On this test we will check if you know how to:
 *
 * 1. Create migration
 * 2. Setup Columns
 * 3. Create Relationships and Indexes
 *
 * @package Tests\Exam
 */
class a_MigrationTest extends TestCase
{
    /**
     * Create daily_logs table
     */
    #[Test]
    public function create_daily_logs_table(): void
    {
        $this->assertTrue(
            Schema::hasTable('daily_logs')
        );
    }

    /**
     * Add columns to your table:
     * user_id : int not null
     * log: text not null
     * day: date not null
     * created_at: date not null
     * updated_at: date not null
     */
    #[Test]
    public function create_columns(): void
    {
        $this->assertTrue(
            Schema::hasColumns('daily_logs', [
                'id',
                'user_id',
                'log',
                'day',
                'created_at',
                'updated_at',
            ])
        );
    }

    /**
     * Create a foreign key that will connect user_id with users table.
     * Make sure to create an index for this column.
     */
    #[Test]
    public function create_foreign_key_and_index(): void
    {
        $constraint = collect(DB::select('PRAGMA index_list(daily_logs)'))
            ->where('name', '=', 'daily_logs_user_id_index')
            ->first();

        $this->assertNotNull($constraint);
    }
}
