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

export type CameraSettings = {
    distance?: number,
    distanceLimit?: [number|undefined, number|undefined], // Min, Max
    initialPosition?: [number, number], // x, y
    fieldOfView?: number,
    scaleX?: number,
    scaleY?: number,
}

/**
 * Original Camera code by Rob Ashton, modified and converted to TypeScript by Simon Dann
 * @see https://github.com/robashton/camera
 */
export default class Camera {
    public distance: number;
    public lookAt: [number, number];
    private readonly initialDistance: number;
    private readonly distanceLimit: [number|undefined, number|undefined]
    private readonly initialLookAt: [number, number];
    private readonly fieldOfView: number;
    private readonly context: CanvasRenderingContext2D;
    private aspectRatio: number;
    private viewport: {
        top: number;
        left: number;
        bottom: number;
        width: number;
        scale: [number, number];
        right: number;
        height: number;
    };


    constructor(context: CanvasRenderingContext2D, settings : CameraSettings = {}) {
        this.distance = settings.distance || 1000.0;
        this.initialDistance = JSON.parse(JSON.stringify(this.distance));
        this.distanceLimit = settings.distanceLimit || [undefined, undefined];
        this.lookAt = settings.initialPosition || [0, 0];
        this.initialLookAt = JSON.parse(JSON.stringify(this.lookAt));
        this.context = context;
        this.fieldOfView = settings.fieldOfView || Math.PI / 4.0;
        this.aspectRatio = 0;
        this.viewport = {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0,
            width: 0,
            height: 0,
            scale: [settings.scaleX || 1.0, settings.scaleY || 1.0]
        };
        this.updateViewport();
    }

    /**
     * Applies to canvas context the parameters:
     *  -Scale
     *  -Translation
     */
    begin() {
        this.context.save();
        this.applyScale();
        this.applyTranslation();
    }

    /**
     * 2d Context restore() method
     */
    end() {
        this.context.restore();
    }

    /**
     * 2d Context scale(Camera.viewport.scale[0], Camera.viewport.scale[0]) method
     */
    applyScale() {
        this.context.scale(this.viewport.scale[0], this.viewport.scale[1]);
    }

    /**
     * 2d Context translate(-Camera.viewport.left, -Camera.viewport.top) method
     */
    applyTranslation() {
        this.context.translate(-this.viewport.left, -this.viewport.top);
    }

    /**
     * Camera.viewport data update
     */
    updateViewport() {
        this.aspectRatio = this.context.canvas.width / this.context.canvas.height;
        this.viewport.width = this.distance * Math.tan(this.fieldOfView);
        this.viewport.height = this.viewport.width / this.aspectRatio;
        this.viewport.left = this.lookAt[0] - (this.viewport.width / 2.0);
        this.viewport.top = this.lookAt[1] - (this.viewport.height / 2.0);
        this.viewport.right = this.viewport.left + this.viewport.width;
        this.viewport.bottom = this.viewport.top + this.viewport.height;
        this.viewport.scale[0] = this.context.canvas.width / this.viewport.width;
        this.viewport.scale[1] = this.context.canvas.height / this.viewport.height;
    }

    /**
     * Zooms to certain z distance
     */
    zoomTo(z: number) {
        const [min, max] = this.distanceLimit;

        if (typeof min !== 'undefined' && z < min) z = min;
        else if (typeof max !== 'undefined' && z > max) z = max;

        this.distance = z;
        this.updateViewport();
    }

    /**
     * Moves the centre of the viewport to new x, y coords (updates Camera.lookAt)
     */
    moveTo(x: number, y: number) {
        this.lookAt[0] = x;
        this.lookAt[1] = y;
        this.updateViewport();
    }

    /**
     * Reset zoom and re-centers the viewport to the original x,y coords set upon construction.
     */
    reset() {
        this.distance = this.initialDistance;
        this.lookAt[0] = this.initialLookAt[0];
        this.lookAt[1] = this.initialLookAt[1];
        this.updateViewport();
    }

    /**
     * Transform a coordinate pair from screen coordinates (relative to the canvas) into world coordinates (useful for intersection between mouse and entities)
     * Optional: obj can supply an object to be populated with the x/y (for object-reuse in garbage collection efficient code)
     */
    screenToWorld(x: number, y: number, obj = {x:0, y:0}) {
        obj.x = (x / this.viewport.scale[0]) + this.viewport.left;
        obj.y = (y / this.viewport.scale[1]) + this.viewport.top;
        return obj;
    }

    /**
     * Transform a coordinate pair from world coordinates into screen coordinates (relative to the canvas) - useful for placing DOM elements over the scene.
     * Optional: obj can supply an object to be populated with the x/y (for object-reuse in garbage collection efficient code).
     */
    worldToScreen(x: number, y: number, obj = {x:0, y:0}) {
        obj.x = (x - this.viewport.left) * (this.viewport.scale[0]);
        obj.y = (y - this.viewport.top) * (this.viewport.scale[1]);
        return obj;
    }
};
