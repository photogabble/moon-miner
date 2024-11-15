import type {DrawableObj} from "@/SectorMap/types";
import type {SectorMapSystemResource} from "@/types/resources/sector";

export default class System implements DrawableObj {

    debug: boolean = false;
    hover: boolean = false;
    readonly resource: SectorMapSystemResource;
    private readonly x: number;
    private readonly y: number;

    constructor(resource: SectorMapSystemResource, x: number, y: number) {
        this.resource = resource;
        this.x = x;
        this.y = y;
    }

    draw(ctx: CanvasRenderingContext2D): void {
        ctx.beginPath();
        ctx.setLineDash([]);

        if (this.hover) {
            ctx.strokeStyle = '#F00';
            ctx.fillStyle = '#F00';
        } else if (this.resource.is_internal) {
            ctx.strokeStyle = '#FFFFFF';
            ctx.fillStyle = '#FFFFFF';
        } else {
            ctx.strokeStyle = '#ffcd4b';
            ctx.fillStyle = '#ffcd4b';
        }

        ctx.lineWidth = 2;

        // Draw Circle

        ctx.moveTo(this.x, this.y);
        ctx.arc(this.x, this.y, 2, 0, 2 * Math.PI, false);
        ctx.fill();
        ctx.stroke();

        // Draw Label

        ctx.font = "6px Monospace";
        ctx.fillStyle = '#FFFFFF';

        let label = this.resource.is_internal
            ? this.resource.name
            : `${this.resource.name} (sector: ${this.resource.sector_id})`;

        if (this.resource.is_current_system) {
            label = `[${label}]`;
            ctx.fillStyle = '#ffcd4b';
        } else if (this.resource.has_knowledge || this.resource.has_visited) {
            ctx.fillStyle = '#FFFFFF';
        } else {
            label = '??-???'
            ctx.fillStyle = '#FFFFFF';
        }

        ctx.fillText(label, this.x + 10, this.y);
    }

    intersects(x: number, y: number): boolean {
        // (x - center_x)² + (y - center_y)² < radius²
        return (Math.pow((x - this.x), 2) + Math.pow((y - this.y), 2)) < 10;
    }
}
