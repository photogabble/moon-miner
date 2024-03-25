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
use App\Types\ZonePermission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name', 40);

            $table->unsignedBigInteger('owner_id')->nullable()->default(null);

            $table->boolean('is_team_zone')->default(false);

            $defaults = array_from_enum(ZonePermission::cases());
            $table->enum('allow_beacon', $defaults)->default(ZonePermission::Allow->value);
            $table->enum('allow_attack', $defaults)->default(ZonePermission::Allow->value);
            $table->enum('allow_planetattack', $defaults)->default(ZonePermission::Allow->value);
            $table->enum('allow_warpedit', $defaults)->default(ZonePermission::Allow->value);
            $table->enum('allow_planet', $defaults)->default(ZonePermission::Allow->value);
            $table->enum('allow_trade', $defaults)->default(ZonePermission::Allow->value);
            $table->enum('allow_defenses', $defaults)->default(ZonePermission::Allow->value);

            $table->integer('max_hull')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
