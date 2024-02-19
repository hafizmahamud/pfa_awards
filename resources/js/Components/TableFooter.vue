<script setup>
import PaginationLink from '@/Components/PaginationLink.vue';

const props = defineProps(['items'])
</script>
<template>
<div class="container">
    <div class="page-view mb-5">
        <div class="row align-items-center g-0 mb-5">
            <div class="col-12 col-md-4 mb-4 d-flex align-items-center">
                <div class="d-flex w-100 align-items-center me-4">
                    <label class="w-100 me-3" for="tag">Result per page</label>
                    <select class="form-select form-inline mw-s"
                        aria-label=".form-select-sm example"
                        @change.prevent="$emit('update:modelValue', $event.target.value)">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <div class="col-12 mb-2 col-md-8">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center d-flex align-items-center justify-content-md-end flex-wrap">
                        <PaginationLink v-for="(item, $index) in items.links"
                            :key="$index"
                            :href="item.url"
                            :active="item.active"
                            :label="item.label" >
                        </PaginationLink>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</template>
