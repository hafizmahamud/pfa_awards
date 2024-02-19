<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationLink from '@/Components/PaginationLink.vue';
import AddOnHeader from '../../Components/AddOnHeader.vue';
import { Link } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    projects: {
        type: Object,
        default: () => ({}),
    },
    filters: Object,
    jurisdictions: Object,
    tags: Object
});

// console.log(props.documentCollections);

let search = ref(props.filters.search);
let selected_tag = ref(props.filters.tag ? props.filters.tag : "");
let per_page = ref(props.filters.per_page ? props.filters.per_page : 10);
let selected_jurisdiction = ref(props.filters.jurisdiction ? props.filters.jurisdiction : "");

function filterPosts() {
    Inertia.get('/admin/projects', {
        search: search.value,
        jurisdiction: selected_jurisdiction.value,
        tag: selected_tag.value,
        per_page: per_page.value,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}

watch([search, selected_jurisdiction, selected_tag, per_page], value => {
    filterPosts();
});

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}

function destroy(id) {
    if (confirm("Are you sure you want to Delete")) {
        Inertia.delete(route('projects.destroy', id));
    }
}
</script>

<template>
    <AppLayout title="Add or Edit Projects">
        <template #header>
            <AddOnHeader prefix="admin" title="Add or Edit Projects" selected="admin.projects" @jump-to="jumpTo">
            </AddOnHeader>
        </template>

        <div class="mt-5 mb-5 pb-5">
            <div class="container-fluid pb-4 mb-5 px-0 border-bottom border-color-third">
                <div class="app-cont">
                    <div class="c-width">
                        <div class="row align-items-center g-0 mt-3">
                            <div class="col-12 col-md-3 mb-3 me-4 align-center">
                                <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                                    v-model="selected_jurisdiction">
                                    <option value="" selected>Jurisdiction</option>
                                    <option v-for="jurisdiction in props.jurisdictions" :key="jurisdiction.id"
                                        :value="jurisdiction.id">
                                        {{ jurisdiction.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 mb-3 me-4 align-center">
                                <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                                    v-model="selected_tag">
                                    <option value="" selected>Tag</option>
                                    <option v-for="tag in props.tags" :key="tag.id" :value="tag.id">
                                        {{ tag.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3 align-center">
                                <div class="c-search row height d-flex align-items-center">
                                    <div class="input-group position-relative">
                                        <input placeholder="Search" class="form-control border-0 rounded-pill"
                                            type="search" id="search-input" v-model="search">
                                        <span class="input-group-append position-absolute">
                                            <button
                                                class="btn btn-outline-secondary bg-white border-0 ms-n5 border rounded-pill font-secondary"
                                                type="button"><i class="bi bi-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-cont">
                <div class="container">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="table-responsive  add-edit border border-color-secondary">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Edited</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="project in projects.data" :key="project.id">
                                            <td>{{ project.updated_at }}</td>
                                            <td>{{ project.title }}</td>
                                            <td>
                                                <Link :href="project.edit_url" id="edit" class="btn-none">
                                                Edit</Link>
                                                <Link as="button" id="edit" class="btn-none"
                                                    @click="destroy(project.id)">
                                                Delete</Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-4">
                                <Link as="button" id="new-post" class="btn-primary" :href="route('projects.create')">New
                                Project</Link>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="page-view mb-5">
                            <div class="row align-items-center g-0 mb-5">
                                <div class="col-12 col-md-4 mb-3 d-flex align-items-center">
                                    <div class="d-flex w-100 align-items-center me-4">
                                        <label class="me-3" for="tag">Result per page</label>
                                        <select class="form-select form-inline mw-s"
                                            aria-label=".form-select-sm example" v-model="per_page">
                                            <option value="10" selected>10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-8">
                                    <nav aria-label="Page navigation">
                                        <ul
                                            class="pagination justify-content-md-end justify-content-center d-flex align-items-center flex-wrap">
                                            <PaginationLink v-for="link in projects.links" :href="link.url"
                                                :active="link.active" :label="link.label"></PaginationLink>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
