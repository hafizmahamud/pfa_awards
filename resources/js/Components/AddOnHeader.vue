<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps(['prefix', 'title', 'selected']);

const singular = (string) => {
    return string.replace(/s$/, '');
}

let selected = ref(props.selected);

const createOrAddRoute = route().current(`${props.prefix}.create`) || route().current(`${props.prefix}.edit`) || props.prefix == 'admin';
// console.log(createOrAddRoute);
const isAwardsComparison = route().current('awards-comparison.index')
</script>
<template>
    <div class="row g-0 d-flex justify-content-center">
        <div class="col-6 d-flex align-items-center my-5"
            :class="{ 'justify-content-start': (!createOrAddRoute && !isAwardsComparison) && !createOrAddRoute, 'col-12': (!(!createOrAddRoute && !isAwardsComparison) && !createOrAddRoute) || createOrAddRoute, 'col-md-7 col-lg-8': createOrAddRoute }">
            <h2>{{ title }}</h2>
        </div>
        <div class="col-6  my-5 text-end" v-if="!createOrAddRoute && !isAwardsComparison">
            <div class="btn btn-add  ">
                <Link :href="route(`${prefix}.create`)" class="btn btn-outline-secondary" v-if="$page.props.auth.admin">
                <i class="bi bi-plus-circle me-2"></i>
                Add new {{ (prefix == "document-collections") ? "document" : singular(prefix) }}
                </Link>
            </div>
        </div>
        <div class="col-12 col-md-5 col-lg-4 my-5" v-if="createOrAddRoute">
            <div class="d-flex jump-to align-items-center justify-content-end">
                <label class="text-center" style="min-width:70px" for="tag">Jump to:</label>
                <!-- {{ route().current('admin.posts') }} -->
                <select class="form-select form-inline" aria-label=".form-select-sm example"
                    @change="$emit('jump-to', selected)" v-model="selected">
                    <option value="admin.news_announcements">Manage News & Announcements
                    </option>
                    <option value="admin.posts">Manage Posts
                    </option>
                    <option value="admin.projects">Manage Projects
                    </option>
                    <option value="admin.documents">Manage Documents
                    </option>
                    <option value="admin.clauses">Manage Clauses</option>
                    <option value="admin.categories">Manage Categories
                    </option>
                    <option value="tag.index">Manage Tags</option>
                    <option value="admin.jurisdictions">Manage
                        Jurisdictions</option>
                </select>
            </div>
        </div>
    </div>
    <div v-if="$page.props.global.message" class="row g-0">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $page.props.global.message }}
        </div>
    </div>
</template>
