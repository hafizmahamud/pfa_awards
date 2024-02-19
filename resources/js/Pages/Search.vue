<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationLink from '@/Components/PaginationLink.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    search_results: {
        type: Object,
        default: () => ({}),
    },
    jurisdictions: Object,
    filters: Object
});

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

// console.log(props.search_results);
let s = ref(props.filters.s ? props.filters.s : "");
let selected_jurisdiction = ref(props.filters.jurisdiction ? props.filters.jurisdiction : "");
let selected_checkboxes = ref(props.filters.in ? props.filters.in : ['all']);

let search = () => {
    Inertia.get('/search', {
        s: s.value,
        jurisdiction: selected_jurisdiction.value,
        in: selected_checkboxes.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}

</script>

<template>
    <AppLayout title="Search">
        <template #header>
            <div class="row g-0">
                <div class="col 12 col-md-8 my-5">
                    <h2>Search</h2>
                </div>
            </div>
        </template>

        <div class="mt-5 app-cont">
            <div class="tab-content" id="nav-tabContent">

                <div class="tab-pane fade show active" id="nav-federal" role="tabpanel"
                    aria-labelledby="nav-federal-tab">
                    <div class="container-fluid pb-5 mb-5 px-0 border-bottom border-color-third">
                        <div class="row align-items-center g-0 c-width">
                            <div class="col-12 col-md-9 d-flex align-items-center">

                                <div class="banner fil-search me-5">
                                    <div class="row height d-flex justify-content-end align-items-end">
                                        <div class="input-group position-relative">
                                            <input v-model="s" placeholder=""
                                                class="form-control border-color-third rounded-pill" type="search"
                                                id="search-keyword">
                                            <span class="input-group-append position-absolute">
                                                <button
                                                    class="btn btn-outline-secondary bg-white border-0 ms-n5 border rounded-pill font-secondary"
                                                    type="button"><i class="bi bi-search"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center me-5">
                                    <label class="me-3" for="tag">Jurisdicions</label>
                                    <select v-model="selected_jurisdiction" class="form-select form-inline mw-s2"
                                        aria-label=".form-select-sm example">
                                        <option value="" selected>All</option>
                                        <option v-for="jurisdiction in jurisdictions" :value="jurisdiction.id">{{
                                                jurisdiction.name
                                        }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="c-width">
                                <div class="row g-0 mt-3 align-md-items-center">
                                    <div
                                        class="col-7 d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-lg-content-between">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                                v-model="selected_checkboxes" value="all" control-id="ControlID-4">
                                            <label class="form-check-label" for="inlineCheckbox1">All</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                                v-model="selected_checkboxes" value="clauses" control-id="ControlID-6">
                                            <label class="form-check-label" for="inlineCheckbox3">Clause</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox4"
                                                v-model="selected_checkboxes" value="conditions"
                                                control-id="ControlID-7">
                                            <label class="form-check-label" for="inlineCheckbox4">Conditions</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox5"
                                                v-model="selected_checkboxes" value="posts" control-id="ControlID-8">
                                            <label class="form-check-label" for="inlineCheckbox5">Posts</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="inlineCheckbox6"
                                                v-model="selected_checkboxes" value="documents"
                                                control-id="ControlID-9">
                                            <label class="form-check-label" for="inlineCheckbox6">Documents</label>
                                        </div>
                                    </div>
                                    <div class="col-5 search-btn text-end">
                                        <button @click.prevent="search" type="button" class="btn btn-primary"
                                            control-id="ControlID-10"> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="col-sm-12 my-3 ps-3">
                <div class="new-dashboard">
                    <template v-for="result in search_results.data">
                        <div class="card-news">
                            <div class="row g-0">
                                <div class="col-sm-2 mb-3 mt-2 ">
                                    <img src="/storage/images/image2.png" class="img-thumbnail" alt="image">
                                </div>
                                <div class="col-sm-8">
                                    <div class="card-body">
                                        <h4 class="card-title mt-2 mb-3">{{ result.title }}
                                        </h4>
                                        <p v-if="result.type == 'CLAUSE'"><strong>Award:</strong> {{ result.award
                                        }}</p>
                                        <p class="card-text mt-3 mb-3" v-html="result.content"></p>
                                        <button v-for="jurisdiction in result.jurisdictions" type="button"
                                            class="tags mt-3">{{ jurisdiction.name }}</button>
                                        <button type="button" class="tags">{{ result.type }}</button>
                                    </div>
                                </div>
                                <div class="col-sm-2 text-center border-start border-color-third">
                                    <a v-if="result.link" :href="result.link"><i class="me-3 bi bi-info-circle"
                                            toggle="tooltip" data-bs-placement="top"
                                            title="Click for more information"></i></a>
                                    <template v-if="(result.attachment != null) && (result.type == 'DOCUMENT')">
                                        <template v-for="attachment in result.attachment">
                                            <a :href="'/storage/' + attachment.path" target="_blank"><i toggle="tooltip"
                                                data-bs-placement="top" class="bi bi-file-earmark-text" style="font-size: 30px;"
                                                :title="attachment.file_name"></i></a>
                                        </template>
                                        <br><br>
                                        <a :href="route('documents.zipAllFile', result.id)"><i toggle="tooltip"
                                            data-bs-placement="top" class="bi bi-cloud-arrow-down"
                                            title="Download attachment"></i></a>
                                    </template>
                                    <a v-if="(result.attachment != null) && (result.type == 'POST')"
                                        :href="route('posts.pdf', result.id)"><i toggle="tooltip"
                                            data-bs-placement="top" class="bi bi-cloud-arrow-down"
                                            title="Download attachment"></i></a>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                    </template>
                </div>
            </div>
        </div>

        <div v-if="search_results.last_page != 1" class="container">
            <div class="page-view mb-5">
                <div class="row align-items-center g-0 mb-3">
                    <div class="col-12 col-md-12  mt-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center d-flex align-items-center flex-wrap">
                                <PaginationLink v-for="link in search_results.links" :href="link.url"
                                    :active="link.active" :label="link.label"></PaginationLink>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
