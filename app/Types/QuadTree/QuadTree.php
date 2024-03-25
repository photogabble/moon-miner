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

namespace App\Types\QuadTree;

use App\Models\ToCartesian;
use App\Types\Geometry\Point;
use App\Types\Geometry\Bounds;

class QuadTree
{
    /**
     * @var Insertable[]
     */
    private array $items = [];

    public ?QuadTree $nw = null;
    public ?QuadTree $ne = null;
    public ?QuadTree $sw = null;
    public ?QuadTree $se = null;

    public function __construct(
        public Bounds $bounds,
        public int    $capacity = 4,
    )
    {
        //
    }

    /**
     * Returns total number of items contained within the tree.
     * @return int
     */
    public function getSize(): int
    {
        if (is_null($this->nw)) return count($this->items);
        return $this->nw->getSize()
            + $this->ne->getSize()
            + $this->sw->getSize()
            + $this->se->getSize();
    }

    public function getItems(): array
    {
        if (is_null($this->nw)) return $this->items;

        return array_merge(
            $this->nw->getItems(),
            $this->ne->getItems(),
            $this->sw->getItems(),
            $this->se->getItems()
        );
    }

    /**
     * Will only insert an item if it fits within this QuadTree's bounds and
     * does not collide with an existing item. This has a useful effect of
     * allowing you to filter stars based upon being too near to one another.
     *
     * @param Insertable $item
     * @return bool
     */
    public function insert(Insertable $item): bool
    {
        // Check if item fits within my bounds, return false if not
        if (!$this->bounds->intersects($item->toBounds())) return false;

        // Check if item collides with an existing item, return false if so
        if ($this->collidesWithItems($item)) return false;

        // If not split and item can fit within capacity then insert and return
        if (is_null($this->nw) && count($this->items) < $this->capacity) {
            $this->items[] = $item;
            return true;
        }

        // Split before attempting to insert into one of the subdivisions.
        if (count($this->items) >= $this->capacity) $this->split();

        return $this->nw->insert($item)
            || $this->ne->insert($item)
            || $this->se->insert($item)
            || $this->sw->insert($item);
    }

    /**
     * Returns true if the provided item for insertion collides with any
     * of the existing items.
     * @param Insertable $item
     * @return bool
     */
    public function collidesWithItems(Insertable $item): bool
    {
        $bounds = $item->toBounds();

        // Check if item fits within my bounds, return false if not
        if (!$this->bounds->intersects($bounds)) return false;

        if (is_null($this->nw)) {
            foreach ($this->items as $existing) {
                if ($existing->toBounds()->intersects($bounds)) return true;
            }
            return false;
        }

        return $this->nw->collidesWithItems($item)
            || $this->ne->collidesWithItems($item)
            || $this->se->collidesWithItems($item)
            || $this->nw->collidesWithItems($item);
    }

    /**
     * Splits the current bounding area into four new subdivisions.
     * @return void
     */
    private function split(): void
    {
        $center = $this->bounds->getCenter();
        $width = $this->bounds->width / 2;
        $height = $this->bounds->height / 2;

        $this->nw = new QuadTree(new Bounds(new Point($center->x - $width, $center->y - $height), $width, $height), $this->capacity);
        $this->ne = new QuadTree(new Bounds(new Point($center->x, $center->y - $height), $width, $height), $this->capacity);
        $this->sw = new QuadTree(new Bounds(new Point($center->x - $width, $center->y), $width, $height), $this->capacity);
        $this->se = new QuadTree(new Bounds(new Point($center->x, $center->y), $width, $height), $this->capacity);

        foreach ($this->items as $item) {
            if ($this->nw->insert($item)) continue;
            if ($this->ne->insert($item)) continue;
            if ($this->sw->insert($item)) continue;
            if ($this->se->insert($item)) continue;
        }

        $this->items = [];
    }

}
