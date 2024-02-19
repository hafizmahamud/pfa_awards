<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
import PaginationLink from '@/Components/PaginationLink.vue';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import JurisdictionTab from '../../Components/JurisdictionTab.vue';
import TableFooter from '@/Components/TableFooter.vue';
// import Welcome from '@/Components/Welcome.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';

const props = defineProps({
    projects: {
        type: Object,
        default: () => ({}),
    },
    filters: Object,
    jurisdictions: Object,
    tags: Object,
    authors: Object
});

const form = useForm();

function destroy(id) {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route('projects.destroy', id));
    }
}

let search = ref(props.filters.search);
let selected_tag = ref(props.filters.tag ? props.filters.tag : "");
let per_page = ref(props.filters.per_page ? props.filters.per_page : 10);
let selected_jurisdiction = ref(props.filters.jurisdiction ? props.filters.jurisdiction : "");

function filterPosts() {
    Inertia.get('/projects', {
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

watch(search, value => {
    filterPosts();
});

watch(selected_tag, value => {
    filterPosts();
});

watch(selected_jurisdiction, value => {
    filterPosts();
});

watch(per_page, value => {
    filterPosts();
});

</script>

<template>
    <AppLayout title="Projects">
        <template #header>
            <AddOnHeader prefix="projects" title="Projects" />
        </template>
        <div class="mt-5 app-cont">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-federal" role="tabpanel"
                    aria-labelledby="nav-federal-tab">
                    <div class="container-fluid pb-5 mb-5 px-0 border-bottom border-color-third">
                        <div class="row align-items-center g-0 c-width">
                            <div class="col-12 mb-5 col-md-6 col-lg-3 d-flex align-items-center ">
                                <div class="d-flex align-items-center w-100">
                                    <label class="me-3" for="tag">Tag</label>
                                    <select class="form-select form-inline me-md-3" aria-label=".form-select-sm example"
                                        v-model="selected_tag">
                                        <option value="" selected>Select tag</option>
                                        <option v-for="tag in tags" :key="tag.id" :value="tag.id">
                                            {{ tag.name }}
                                        </option>
                                    </select>
                                </div>


                            </div>

                            <div class="col-12 mb-5 col-md-6 col-lg-4 d-flex align-items-center ">
                                <div class="d-flex align-items-center w-100 ">
                                    <label class="me-3" for="tag">Jurisdiction</label>
                                    <select class="form-select form-inline me-md-3" aria-label=".form-select-sm example"
                                        v-model="selected_jurisdiction">
                                        <option value="" selected>Select Jurisdiction</option>
                                        <option v-for="jurisdiction in jurisdictions" :key="jurisdiction.id"
                                            :value="jurisdiction.id">
                                            {{ jurisdiction.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>



                            <div class="col-12 mb-5 col-lg-5 search-btn d-flex align-items-center justify-content-around gap-2">
                                <div class="banner fil-search">
                                    <div class="row height d-flex justify-content-end align-items-end">
                                        <div class="input-group position-relative">
                                            <input placeholder="Keywords"
                                                class="form-control border-color-third rounded-pill" type="search"
                                                id="search-keywoed" v-model="search">
                                            <span class="input-group-append position-absolute">
                                                <button
                                                    class="btn btn-outline-secondary bg-white border-0 ms-n5 rounded-pill font-secondary"
                                                    type="button"><i class="bi bi-search"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary"> Search </button>
                            </div>

                        </div>
                    </div>

                    <div class="container">
                        <div class="my-5">
                            <div class="col-12 my-3 c-width border-bottom border-color-third"
                                v-for="project in projects.data" :key="project.id">
                                <div class="user-post">
                                    <p class="card-date">{{ project.created_at }}</p>
                                    <div class="row g-0">
                                        <div class="col-12 col-md-6">
                                            <Link :href="route('projects.show', project)">
                                            <h5 class="post-title">{{ project.title }}</h5>
                                            </Link>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <p class="text-md-end"> Members: <strong>{{ project.members.length }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="post-text">{{ project.content.substr(0, 200) }}</p>
                                    <div class="mb-5">
                                        <button type="button" class="tags" title="tags" v-for="tag in project.tags"
                                            :key="tag.id">
                                            {{ tag.name }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <TableFooter :items="projects" v-model="per_page" />
        </template>
    </AppLayout>
</template>
