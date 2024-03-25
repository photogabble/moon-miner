<?php declare(strict_types=1);
/**
 * Moon Miner, a Free & Opensource (FOSS), web-based 4X space/strategy game forked
 * and based upon Black Nova Traders.
 *
 * @copyright 2024 Simon Dann
 * @copyright 2001-2014 Ron Harwood and the BNT development team
 *
 * @license GNU AGPL version 3.0 or (at your option) any later version.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


use App\Types\WaypointType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waypoints', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('system_id');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('primary_id')->nullable();

            $table->json('properties')->nullable();
            $table->enum('type', array_from_enum(WaypointType::cases()));
            $table->string('name');

            // Position and Orbit
            $table->decimal('angle');
            $table->decimal('distance');
            $table->decimal('eccentricity');
            $table->decimal('inclination');

            // No two waypoints can share the same orbit
            $table->unique(['primary_id', 'primary_type', 'angle', 'distance'], 'unique_orbit');

            $table->foreign('system_id')
                ->references('id')
                ->on('systems')
                ->onDelete('cascade');

            $table->foreign('primary_id')
                ->references('id')
                ->on('waypoints')
                ->onDelete('cascade');

            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waypoints');
    }
};
