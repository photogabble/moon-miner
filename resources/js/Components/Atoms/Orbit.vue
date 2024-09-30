<script setup lang="ts">
import {computed} from "vue";
import type {WaypointResource, WaypointType} from "@/types/resources/waypoint";

const props = withDefaults(defineProps<{
    waypoint: WaypointResource<WaypointType>
    name: string,
    planetRadius: number,
    radius: number,
    angle: number,
    selected?: boolean,
}>(), {
    selected: false,
});

// TODO obtain values from props.waypoint and scale orbit so it displays correctly (will need a scale prop passing)

// Planet radius, this larps the planets radius
const planetRadius = props.planetRadius;

// (x,y), note the last -1 is to subtract the border width of the drawn circle.
const x = computed(() => (props.radius / 2) + ((props.radius / 2) * Math.cos(props.angle)) - (planetRadius / 2) - 1);
const y = computed(() => (props.radius / 2) + ((props.radius / 2) * Math.sin(props.angle)) - (planetRadius / 2) - 1);

</script>

<template>
    <div class="orbit" :class="{'orbit--selected': selected}" :style="`width: ${radius}px; height: ${radius}px;`">
        <div class="absolute" :style="`left: ${x}px; top: ${y}px; height: ${planetRadius}px`">
            <span class="orbital__description text-xs text-ui-grey-900 bg-ui-orange-500 px-1" :style="`left: ${planetRadius + 5}px`">{{ name }}</span>
            <button class="orbital" :style="`width: ${planetRadius}px; height: ${planetRadius}px;`" />
        </div>
    </div>
</template>

<style scoped>
.orbital {
    background: #000008;
    border: 1px solid #fc8437;
    border-radius: 50%;
}

.orbital__description {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    display: none;
}

.orbit {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);

    border: 1px dashed rgba(252, 132, 55, 0.50);
    border-radius: 50%;
}

.orbit:hover{
    cursor: pointer;
    border-color: #fc8437;
}

.orbit:hover .orbital {
    background: #fc8437;
}

.orbit:hover .orbital__description {
    display: block;
}

.orbit--selected {
    border-color: white;
}

.orbit--selected .orbital {
    background: white;
    border-color: white;
}
</style>
