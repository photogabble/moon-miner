<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Types\QuadTree\Item;
use App\Types\Geometry\Point;
use App\Types\Geometry\Bounds;
use PHPUnit\Framework\TestCase;
use App\Types\QuadTree\QuadTree;


class QuadTreeTest extends TestCase
{
    public function test_quad_tree_size()
    {
        $tree = new QuadTree(new Bounds(new Point(0, 0), 10, 10));
        $this->assertEquals(0, $tree->getSize());

        $tree->insert(new Item(1, 1));
        $this->assertEquals(1, $tree->getSize());

        $tree->insert(new Item(1, 2));
        $this->assertEquals(2, $tree->getSize());
    }

    public function test_quad_tree_split()
    {
        $tree = new QuadTree(new Bounds(new Point(0, 0), 10, 10));
        $this->assertNull($tree->nw);

        $tree->insert(new Item(1, 1));
        $tree->insert(new Item(9, 1));
        $tree->insert(new Item(1, 9));
        $tree->insert(new Item(9, 9));

        $this->assertEquals(4, $tree->getSize());
        $this->assertNull($tree->nw);

        $tree->insert(new Item(5, 5));
        $this->assertEquals(5, $tree->getSize());
        $this->assertNotNull($tree->nw);
    }
}
