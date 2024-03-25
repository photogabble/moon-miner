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
        Schema::create('planets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('system_id');
            $table->string('name', 15)->nullable()->default(null);

            $table->integer('organics')->default(0);
            $table->integer('ore')->default(0);
            $table->integer('goods')->default(0);
            $table->integer('energy')->default(0);
            $table->integer('colonists')->default(0);
            $table->integer('credits')->default(0);
            $table->integer('fighters')->default(0);
            $table->integer('torps')->default(0);

            $table->unsignedBigInteger('owner_id')->nullable()->default(null);
            $table->unsignedBigInteger('team_id')->nullable()->default(null);

            $table->boolean('base')->default(false);
            $table->boolean('sells')->default(false);
            $table->integer('prod_organics')->default(0);
            $table->integer('prod_ore')->default(0);
            $table->integer('prod_goods')->default(0);
            $table->integer('prod_energy')->default(0);
            $table->integer('prod_fighters')->default(0);
            $table->integer('prod_torp')->default(0);

            $table->boolean('defeated')->default(false);

            $table->foreign('system_id')
                ->references('id')
                ->on('systems')
                ->onDelete('cascade');

            // TODO: Add foreign keys for teams and owner once used
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planets');
    }
};
