<script setup lang="ts">
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

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import MainPanel from "@/Components/Atoms/MainPanel.vue";
import {usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref} from "vue";
import Camera from "@/Helpers/camera";
import WarpLink from "@/SectorMap/WarpLink";
import System from "@/SectorMap/System";
import type {SectorMapPageProps} from "@/types/sector-map";
import type {SectorMapSystemResource} from "@/types/resources/sector";
import SectorMapActionList from "@/Components/Organisms/SectorMapActionList.vue";

const dragging = ref(false);
const hovering = ref<SectorMapSystemResource|undefined>(undefined);
const activeSystem = ref<SectorMapSystemResource|undefined>(undefined);
const map = ref<HTMLCanvasElement>();
const {stats, sector, systems, links, usable_links} = usePage<SectorMapPageProps>().props;

const mouseMode = computed(() => {
    if (hovering.value !== undefined) return 'hovering';
    if (dragging.value === true) return 'dragging';
    return 'default';
});

// Mouse coordinates, relative to iWidth and iHeight, this is used for
// knowing if the mouse is hovering over a system.
const miXY = {x: 0, y: 0};
const mXY = {x: 0, y: 0};

onMounted(() => {
    const canvas = map.value;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const boundingBox = canvas.getBoundingClientRect();
    canvas.width = boundingBox.width;
    canvas.height = boundingBox.height;

    // TODO: ...
    // x and y scale factor should be to our board size (4000x4000), the viewport is then to equal the canvas dimensions and
    // our camera will only render what can be seen through the viewport. I will need to work out how to translate the
    // viewport mouse coordinates to the board coordinates so I know what is being hovered over or clicked.
    //
    // - [x] Panning Camera with click and drag / touch and drag
    // - [ ] Panning Camera with cursor keys
    // - [x] Zooming Camera with scroll wheel
    // - [ ] Zooming Camera with +/- keys
    // - [ ] Highlight current system by surrounding with a square
    // - [ ] Clicking on a system will goto NavCom detail page for system
    // - [ ] For links to systems in other sectors, add system in correct position and label as sector jump
    // - [ ] Clicking system in other sector should load sector map for that sector, add a from query param
    //       so that the sector we navigated from is the highlighted system
    // - [ ] Make keyboard accessible via tabbing to change focus on which system is highlighted
    // - [ ] Return on highlighted system to act the same as clicking
    // - [ ] Limit panning to ensure > 30% of the board remains in view

    const iWidth = 1000;
    const iHeight = 1000;

    const xScaleFactor = iWidth / 100;
    const yScaleFactor = iHeight / 100;

    const linkList: Array<WarpLink> = links.map(link => {
        return new WarpLink(
            link,
            link.from.x * xScaleFactor,
            link.from.y * yScaleFactor,
            link.to.x * xScaleFactor,
            link.to.y * yScaleFactor,
        )
    });

    const systemList: Array<System> = systems.map(system => {
        return new System(
            system,
            system.coords.x * xScaleFactor,
            system.coords.y * yScaleFactor
        );
    });

    const draw = () => {
        linkList.forEach(link => link.draw(ctx));
        systemList.forEach(system => system.draw(ctx));
    };

    const currentSystem = systems.find(system => system.is_current_system);
    let initialPosition : [number, number] = [iWidth / 2, iHeight / 2];
    let distance = iWidth * 1.5;
    if (currentSystem) {
        initialPosition = [
            currentSystem.coords.x * xScaleFactor,
            currentSystem.coords.y * yScaleFactor
        ];
        distance = iWidth / 2;
    }

    const camera = new Camera(ctx, {
        initialPosition,
        distance,
        distanceLimit: [300, iWidth * 2],
    });

    // Event Handlers

    canvas.addEventListener('wheel', (e) => {
        e.preventDefault();

        let zoomLevel = camera.distance - (e.deltaY * 20);
        if (zoomLevel <= 1) zoomLevel = 1;
        camera.zoomTo(zoomLevel);
    });

    // Reset camera when r key pressed
    window.addEventListener('keydown', (e) => {
        if (e.key === 'r') {
            camera.reset();
        }
    });

    let mX = 0, mY = 0;
    canvas.addEventListener('mousedown', (e) => {
        if (e.button === 0) {
            dragging.value = true;
            mX = e.x;
            mY = e.y;
        }
    });

    canvas.addEventListener('mouseup', () => {
        if (hovering.value !== undefined) {
            activeSystem.value = hovering.value; // Display action menu for this system
        } else if (activeSystem.value !== undefined) {
            activeSystem.value = undefined; // Click off to cancel action menu
        }

        dragging.value = false;
    });

    canvas.addEventListener('mousemove', (e) => {
        if (dragging.value === true) {
            const x = camera.lookAt[0] + ((mX - e.x) * 2);
            const y = camera.lookAt[1] + ((mY - e.y) * 2);
            camera.moveTo(x, y);

            mX = e.x;
            mY = e.y;

            return;
        }

        mXY.x = e.x - boundingBox.left;
        mXY.y = e.y - boundingBox.top;

        camera.screenToWorld(
            e.x - boundingBox.left,
            e.y - boundingBox.top,
            miXY
        );

        let intersecting = undefined;
        systemList.forEach(system => {
            system.hover = system.resource.has_knowledge && system.intersects(miXY.x, miXY.y);
            if (system.hover) intersecting = system.resource;
        });
        hovering.value = intersecting;
    });

    // Main Animation Loop

    const main = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        camera.begin();
        draw();
        camera.end();

        // Debug, output mouse posistion

        // ctx.font = "14px Monospace";
        // ctx.fillStyle = '#FF0';
        // ctx.fillText(`Mouse World Position: (${Math.floor(miXY.x)},${Math.floor(miXY.y)})`, 10, 50);
        // ctx.fillText(`Mouse Position: (${Math.floor(mXY.x)},${Math.floor(mXY.y)})`, 10, 70);

        requestAnimationFrame(main);
    };

    requestAnimationFrame(main);
});
</script>

<template>
    <authenticated-layout>
        <main-panel class="relative">
            <template #top-left>
                <span class="text-white">Sector</span> {{ sector.id }}
            </template>
            <template #top-right>
                <span class="text-white">Visited Systems</span> {{ stats.visited }}, <span class="text-white">Known Systems</span>
                {{ stats.known }} ({{ stats.discovery_percentage }}%)
                <span class="text-white"></span>
            </template>

            <sector-map-action-list
                v-if="activeSystem"
                :usableLinks="usable_links"
                :system="activeSystem"
            />

            <canvas ref="map" :class="[
                {
                    'cursor-grabbing': mouseMode === 'dragging',
                    'cursor-pointer': mouseMode === 'hovering',
                    'cursor-grab': mouseMode === 'default'
                }, 'w-full h-full']"
            />
        </main-panel>
    </authenticated-layout>
</template>
