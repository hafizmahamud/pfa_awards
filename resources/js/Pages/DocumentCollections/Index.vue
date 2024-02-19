<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import JurisdictionTab from '@/Components/JurisdictionTab.vue';
import TableFooter from '@/Components/TableFooter.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import Modal from '@/Components/Modal.vue';
import PaginationLink from '@/Components/PaginationLink.vue';

const props = defineProps({
    documentCollections: {
        type: Object,
        default: () => ({}),
    },
    filters: Object,
    jurisdictions: Object,
    tags: Object,
    authors: Object
});

const form = useForm();

let search = ref(props.filters.search);
let selected_author = ref(props.filters.author ? props.filters.author : "");
let selected_tag = ref(props.filters.tag ? props.filters.tag : "");
let sort_by = ref(props.filters.sort_by ? props.filters.sort_by : "latest");
let per_page = ref(props.filters.per_page ? props.filters.per_page : 10);
//let active_jurisdiction = props.filters.jurisdiction ? ref(parseInt(props.filters.jurisdiction)) : ref(1);
let active_jurisdiction = props.filters.jurisdiction ? props.filters.jurisdiction : 1;
let showModal = ref(false);
let modalTitle = ref("");
let modalDocument = ref(null);

function filterPosts() {
    Inertia.get('/document-collections', {
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

let showModaltrue = (id) => {
    modalDocument.value = props.documentCollections.data.filter((doc) => doc.id == id)
    showModal.value = true;
}
</script>

<template>
    <AppLayout title="Documents">
        <Teleport to="body">
            <!-- use the modal component, pass in the prop -->
            <modal :show="showModal" @close="showModal = false">
                <template #header>
                    <h3>Documents</h3>
                </template>

                <template #body>
                    <table class="documents table table-borderless">
                        <tbody>
                            <tr v-for="documents in modalDocument" :key="documents.id">
                            <tr v-for="doc in documents.documents" v-if="documents.documents.length > 0">
                                <td>
                                    {{ doc.unique_file_name }}
                                </td>
                                <td>
                                    <ul class="list-unstyled list-inline">
                                        <li class="list-inline-item me-3">
                                            <a class="py-3" :href="'/storage/documents/' + doc.unique_file_name"
                                                target="_blank" alt="notification">
                                                <svg class="me-3" width="30" height="31" viewBox="0 0 30 31" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_42_3119)">
                                                        <path
                                                            d="M15 18.2334C16.3807 18.2334 17.5 17.1141 17.5 15.7334C17.5 14.3527 16.3807 13.2334 15 13.2334C13.6193 13.2334 12.5 14.3527 12.5 15.7334C12.5 17.1141 13.6193 18.2334 15 18.2334Z"
                                                            stroke="#032262" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M27.5 15.7334C24.1662 21.5671 20 24.4834 15 24.4834C10 24.4834 5.83375 21.5671 2.5 15.7334C5.83375 9.89965 10 6.9834 15 6.9834C20 6.9834 24.1662 9.89965 27.5 15.7334Z"
                                                            stroke="#032262" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_42_3119">
                                                            <rect width="30" height="30" fill="white"
                                                                transform="translate(0 0.733398)"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>View
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a class="py-3" :href="route('documents.pdf', doc)" target="_blank"
                                                alt="notification"><i
                                                    class="bi bi-cloud-download pe-3 py-3"></i>Download</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr v-else>
                                <td>No documents found.</td>
                            </tr>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </modal>
        </Teleport>

        <template #header>
            <AddOnHeader prefix="document-collections" title="Document" />
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
                            <div class="col-12 col-lg-3 my-2 d-flex align-items-center">
                                <div class="d-flex align-items-center me-4 w-100">
                                    <label class="me-3" for="tag">Author</label>
                                    <select class="form-select form-inline " aria-label=".form-select-sm example"
                                        v-model="selected_author">
                                        <option value="" selected>
                                            Select author
                                        </option>
                                        <option v-for="author in props.authors" :key="author.id" :value="author.id">
                                            {{ author.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-lg-3 my-2 d-flex align-items-center">
                                <div class="d-flex align-items-center me-4 w-100">
                                    <label class="me-3" for="tag">Topic</label>
                                    <select class="form-select form-inline " aria-label=".form-select-sm example"
                                        v-model="selected_tag">
                                        <option value="" selected>
                                            Select tag
                                        </option>
                                        <option v-for="tag in props.tags" :key="tag.id" :value="tag.id">
                                            {{ tag.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 my-2 d-flex align-items-center">
                                <div class="d-flex align-items-center me-4 w-100">
                                    <label class="me-3" for="tag">Sort</label>
                                    <select class="form-select form-inline" aria-label=".form-select-sm example"
                                        v-model="sort_by">
                                        <option value="latest" selected>
                                            Latest
                                        </option>
                                        <option value="oldest">Oldest</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 banner fil-search">
                                <div class="row height d-flex justify-content-end align-items-end">
                                    <div class="col-sm-3 input-group position-relative me-4">
                                        <input v-model="search" placeholder="Search"
                                            class="form-control border-color-third rounded-pill" type="search"
                                            id="search-input" />
                                        <span class="input-group-append position-absolute">
                                            <button
                                                class="btn btn-outline-secondary bg-white border-0 ms-n5 rounded-pill font-secondary"
                                                type="button">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="my-5">
                            <div class="row g-0 border-bottom border-color-third"
                                v-for="documentCollection in documentCollections.data" :key="documentCollection.id">
                                <div class="col-12 col-md-8 mb-5 mt-3 ">
                                    <div class="border-end border-color-third">
                                        <div class="user-download">
                                            <p class="card-date">{{ documentCollection.create_at }}</p>
                                            <h5 class="card-title">{{ documentCollection.title }}</h5>
                                            <p class="card-text">{{ documentCollection.content.substr(0, 200) }}</p>
                                            <!-- <div class="mb-5"> -->
                                            <p> Posted by: {{ documentCollection.author.name }}</p>
                                            <button type="button" class="tags" title="tags"
                                                v-for="tag in documentCollection.tags" :key="tag.id">
                                                {{ tag.name }}
                                            </button>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-3 mb-5 mt-5">
                                    <div class="user-download">
                                        <div class="row g-0">
                                            <div class="col-12 col-md-12 mb-3 justify-content-center text-center">
                                                <a class="btn btn-light"
                                                    :href="route('documents.zipAllFile', documentCollection.id)"
                                                    alt="Dowload All"><i class="bi bi-cloud-download me-2"></i>Download
                                                    All
                                                </a>
                                            </div>
                                            <div class="col-12 col-md-12 mb-3 justify-content-center text-center">
                                                <!--<button id="show-modal" value="{{ documentCollection->id }}"
                                                    @click="showModaltrue(documentCollection.id)"><img class="me-2"
                                                        src="/storage/images/documents.svg">Documents</button>-->
                                                <a class="btn btn-light"
                                                    @click=showModaltrue(documentCollection.id)><img class="me-2"
                                                        src="/storage/images/documents.svg">Documents</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <template #footer>
            <TableFooter :items="documentCollections" v-model="per_page" />
        </template>

    </AppLayout>
</template>
