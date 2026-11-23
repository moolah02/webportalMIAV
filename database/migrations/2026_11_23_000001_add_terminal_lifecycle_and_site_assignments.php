<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gives a device a life beyond the one site it was first installed at.
 *
 * Until now a terminal's site lived on the terminal's own row, so moving a
 * device to a replacement, or taking it back into stock and sending it out
 * again, meant either overwriting where it had been or creating a second
 * record for the same physical device. Neither keeps a history.
 *
 * Two additions:
 *
 *  - `pos_terminals.lifecycle_status` - whether the device is at a site, back
 *    in stock, or out of service. Deliberately NOT the existing `status` or
 *    `current_status` columns: those say whether a device WORKS and are driven
 *    by what technicians record on visits. Where a device IS is an office
 *    fact, and a device sitting in stock has no site for anyone to visit.
 *
 *  - `terminal_site_assignments` - one row per spell at a site, holding a
 *    snapshot of that site. The device row keeps showing its current site, so
 *    nothing that reads it breaks; the history is what the snapshot is for.
 *
 * Visits are untouched: they point at pos_terminals.id, and because a move
 * never creates a second device row, a device's visit history stays whole and
 * can be split by site using these date ranges.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pos_terminals', 'lifecycle_status')) {
            Schema::table('pos_terminals', function (Blueprint $table) {
                $table->enum('lifecycle_status', ['deployed', 'collected', 'retired'])
                    ->default('deployed')
                    ->after('status')
                    ->comment('Where the device is: at a site, in stock, or out of service. Not its condition.');
                $table->index('lifecycle_status');
            });
        }

        if (! Schema::hasTable('terminal_site_assignments')) {
            Schema::create('terminal_site_assignments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('pos_terminal_id')
                    ->constrained('pos_terminals')
                    ->cascadeOnDelete();

                // Where it was. Snapshotted, because the client or the merchant
                // record may be edited or removed later and this has to keep
                // saying where the device actually stood at the time.
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->string('merchant_id')->nullable();
                $table->string('merchant_name')->nullable();
                $table->text('physical_address')->nullable();
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->string('province')->nullable();
                $table->string('area')->nullable();

                $table->dateTime('started_at');
                // Null means this is where the device is now.
                $table->dateTime('ended_at')->nullable();
                $table->enum('ended_reason', ['collected', 'migrated', 'retired'])->nullable();

                // When a replacement took this device's place, which one.
                $table->unsignedBigInteger('replaced_by_terminal_id')->nullable()->index();

                $table->unsignedBigInteger('recorded_by')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->index(['pos_terminal_id', 'started_at']);
                $table->index(['pos_terminal_id', 'ended_at']);
            });
        }

        $this->backfill();
    }

    /**
     * Give every device that already exists an open assignment at the site it
     * is already recorded against, so the history does not start empty and
     * "where has this device been" has an answer from day one.
     */
    private function backfill(): void
    {
        if (! Schema::hasTable('terminal_site_assignments')) {
            return;
        }

        $already = DB::table('terminal_site_assignments')->distinct()->pluck('pos_terminal_id')->all();

        DB::table('pos_terminals')
            ->when($already !== [], fn ($q) => $q->whereNotIn('id', $already))
            ->orderBy('id')
            ->chunkById(200, function ($terminals) {
                $rows = [];
                foreach ($terminals as $t) {
                    $rows[] = [
                        'pos_terminal_id' => $t->id,
                        'client_id' => $t->client_id,
                        'merchant_id' => $t->merchant_id,
                        'merchant_name' => $t->merchant_name,
                        'physical_address' => $t->physical_address,
                        'city' => $t->city,
                        'region' => $t->region,
                        'province' => $t->province,
                        'area' => $t->area,
                        // Best available start: when it was installed, else
                        // when the record was made.
                        'started_at' => $t->installation_date ?: ($t->created_at ?: now()),
                        'ended_at' => null,
                        'ended_reason' => null,
                        'replaced_by_terminal_id' => null,
                        'recorded_by' => null,
                        'notes' => 'Opened automatically from the device record when site history was introduced.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($rows !== []) {
                    DB::table('terminal_site_assignments')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminal_site_assignments');

        if (Schema::hasColumn('pos_terminals', 'lifecycle_status')) {
            Schema::table('pos_terminals', function (Blueprint $table) {
                $table->dropIndex(['lifecycle_status']);
                $table->dropColumn('lifecycle_status');
            });
        }
    }
};
