<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
import PaginationLink from '@/Components/PaginationLink.vue';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import JurisdictionTab from '@/Components/JurisdictionTab.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';

const props = defineProps({
    posts: {
        type: Object,
        default: () => ({}),
    },
    filters: Object,
    jurisdictions: Object,
    tags: Object,
    authors: Object
});

// console.log(props.authors);
const form = useForm();

function destroy(id) {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route('posts.destroy', id));
    }
}

let search = ref(props.filters.search);
let selected_author = ref(props.filters.author ? props.filters.author : "");
let selected_tag = ref(props.filters.tag ? props.filters.tag : "");
let sort_by = ref(props.filters.sort_by ? props.filters.sort_by : "latest");
let per_page = ref(props.filters.per_page ? props.filters.per_page : 3);
let active_jurisdiction = props.filters.jurisdiction ? props.filters.jurisdiction : 1;

function filterPosts() {
    Inertia.get('/posts', {
        search: search.value,
        jurisdiction: active_jurisdiction,
        author: selected_author.value,
        tag: selected_tag.value,
        sort_by: sort_by.value,
        per_page: per_page.value,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}

watch([search, selected_author, selected_tag, sort_by, per_page], value => {
    filterPosts();
});

function selectJurisdiction(id) {
    active_jurisdiction = id;
    filterPosts();
}

</script>

<template>
    <AppLayout title="Posts">
        <template #header>
            <AddOnHeader prefix="posts" title="Posts" />
            <JurisdictionTab :jurisdictions="jurisdictions" :active="active_jurisdiction"
                @filter-juridiction="selectJurisdiction">
            </JurisdictionTab>
        </template>

        <div class="mb-5 pb-3 app-cont">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-federal" role="tabpanel"
                    aria-labelledby="nav-federal-tab">
                    <div class="container-fluid pb-4 mb-5 px-0 border-bottom border-color-third">
                        <div class="row align-items-center g-0 c-width">
                            <div class="col-12 col-md-4 col-lg-3 my-2 mt-3 pe-3 d-flex align-items-center">
                                <div class="d-flex align-items-center  w-100">
                                    <label class="me-3" for="tag">Author</label>
                                    <select class="form-select form-inline" aria-label=".form-select-sm example"
                                        v-model="selected_author">
                                        <option value="" selected>Select author</option>
                                        <option v-for="author in props.authors" :key="author.id" :value="author.id">
                                            {{ author.name }}
                                        </option>
                                    </select>
                                </div>


                            </div>

                            <div class="col-12 col-md-4 col-lg-3 my-2 mt-3 pe-3 d-flex align-items-center">
                                <div class="d-flex align-items-center w-100">
                                    <label class="me-3" for="tag">Topic</label>
                                    <select class="form-select form-inline" aria-label=".form-select-sm example"
                                        v-model="selected_tag">
                                        <option value="" selected>Select tag</option>
                                        <option v-for="tag in props.tags" :key="tag.id" :value="tag.id">
                                            {{ tag.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 col-lg-3 my-2 mt-3 pe-3 d-flex align-items-center">
                                <div class="d-flex align-items-center w-100">
                                    <label class="me-3" for="tag">Sort</label>
                                    <select class="form-select form-inline" aria-label=".form-select-sm example"
                                        v-model="sort_by">
                                        <option value="latest" selected>Latest</option>
                                        <option value="oldest">Oldest</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-12 col-md-12 col-lg-3 banner fil-search text-end pe-3">
                                <div class="row height d-flex mt-3 justify-content-end align-items-end">
                                    <div class="input-group position-relative">
                                        <input v-model="search" placeholder="Search"
                                            class="form-control border-color-third rounded-pill" type="search"
                                            id="search-input">
                                        <span class="input-group-append position-absolute">
                                            <button
                                                class="btn btn-outline-secondary bg-white border-0 ms-n5 rounded-pill font-secondary"
                                                type="button"><i class="bi bi-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="my-5">
                            <div class="col-12 mb-5 border-bottom border-color-third" v-for="post in posts.data"
                                :key="post.id">
                                <div class="user-post">
                                    <p class="card-date">{{ post.created_at }}</p>
                                    <div class="row g-0">
                                        <div class="col-12 col-md-6 ">
                                            <h5 class="post-title">{{ post.title }}</h5>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <p class="text-lg-end text-start"> Posted by: {{ post.author.name }}</p>
                                        </div>
                                    </div>
                                    <p class="post-text" v-html="post.content">
                                    </p>
                                    <div class="mb-5">
                                        <button type="button" class="tags" title="tags" v-for="tag in post.tags"
                                            @click="selected_tag = tag.id">
                                            {{ tag.name }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="page-view mb-5">
                            <div class="row align-items-center justify-content-between g-0 mb-5">
                                <div class="col-12 col-md-4 mb-4 d-flex  align-items-center">
                                    <div class="d-flex align-items-center w-100 me-4">
                                        <label class="w-100 me-3" for="tag">Result per page</label>
                                        <select class="form-select form-inline" aria-label=".form-select-sm example"
                                            v-model="per_page">
                                            <option value="3" selected>3</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 mb-2 col-md-8 ">
                                    <nav aria-label="Page navigation">
                                        <ul
                                            class="pagination justify-content-center d-flex align-items-center justify-content-md-end">
                                            <PaginationLink v-for="link in posts.links" :href="link.url"
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
