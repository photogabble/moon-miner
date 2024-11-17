import type {DrawableObj} from "@/SectorMap/types";
import {SectorMapLinkResource} from "@/types/resources/sector";

export default class WarpLink implements DrawableObj {
    readonly resource: SectorMapLinkResource;
    private readonly x1: number;
    private readonly y1: number;
    private readonly x2: number;
    private readonly y2: number;

    constructor(resource: SectorMapLinkResource, x1: number, y1: number, x2: number, y2: number) {
        this.resource = resource;
        this.x1 = x1;
        this.y1 = y1;
        this.x2 = x2;
        this.y2 = y2;
    }

    draw(ctx: CanvasRenderingContext2D): void {
        ctx.beginPath();
        ctx.moveTo(this.x1, this.y1);
        ctx.lineTo(this.x2, this.y2);

        ctx.lineWidth = 2;

        if (this.resource.is_route) {
            ctx.setLineDash([]);
            ctx.strokeStyle = '#ffcd4b';
        } else if (this.resource.is_internal && this.resource.has_visited) {
            ctx.setLineDash([]);
            ctx.strokeStyle = '#fc8437';
        } else if (this.resource.is_internal) {
            ctx.setLineDash([]);
            ctx.strokeStyle = '#FC843780';
        } else {
            ctx.setLineDash([2, 4, 6]);
            ctx.strokeStyle = '#FFF';
        }

        ctx.stroke();
    }
}
