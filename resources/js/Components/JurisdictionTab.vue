<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { watch, ref } from 'vue';

const props = defineProps({
    jurisdictions: Object,
    active: Number
});

</script>

<template>
    <nav>
        <div class="navbar nav nav-tabs" id="nav-tab" role="tablist">
            <div v-for="jurisdiction in props.jurisdictions" :key="jurisdiction.id">
                <Link class="nav-link" :class="{ 'active': jurisdiction.id == props.active }" as="button"
                    v-html="jurisdiction.name" :value="jurisdiction.id"
                    @click="$emit('filter-juridiction', jurisdiction.id)">
                </Link>
            </div>
        </div>
    </nav>
</template>

