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
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name', 30)->nullable()->default(null);

            // Galactic Map Position
            $table->decimal('angle');
            $table->decimal('distance');

            $table->unsignedBigInteger('zone_id')->nullable()->default(null);
            $table->unsignedBigInteger('sector_id');

            // TODO: Move ports to being waypoint with a port trait
            $table->string('port_type', 8)->default('none');
            $table->integer('port_organics')->default(0);
            $table->integer('port_ore')->default(0);
            $table->integer('port_goods')->default(0);
            $table->integer('port_energy')->default(0);

            // Sector Beacons are able to be set by owners of a sectors zone.
            $table->string('beacon', 50)->nullable()->default(null);
//            $table->decimal('angle1')->default(0);
//            $table->decimal('angle2')->default(0);
//            $table->integer('distance')->default(0);
            $table->integer('fighters')->default(0);

            $table->index('port_type');

            $table->foreign('sector_id')
                ->references('id')
                ->on('sectors')
                ->onDelete('cascade');

            $table->foreign('zone_id')
                ->references('id')
                ->on('zones')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
