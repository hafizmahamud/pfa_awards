<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    label: String
});

</script>

<template>
    <li class="page-item" :class="{ 'active': props.active, 'd-none': !props.href }">
        <Link :href="href" class="page-link" v-html="label" preserve-scroll />
    </li>
</template>

