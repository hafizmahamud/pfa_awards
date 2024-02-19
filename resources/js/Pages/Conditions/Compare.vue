<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AddOnHeader from '@/Components/AddOnHeader.vue';

const props = defineProps({
    compare: {
        type: Object,
        default: null,
    },
    category: Number,
    tags: Object,
    jurisdictions: Object,
    filters: Object
});

let selected_tag = ref(props.filters.tag ? props.filters.tag : '');
let selected_jurisdictions = ref(props.filters.jurisdictions ? props.filters.jurisdictions : []);
let showModal = ref(false);
let modalBody = ref("");
let modalTopic = ref("");
let subcategory_id = ref(props.filters.subcategory_id ? props.filters.subcategory_id : '');
// let topic = ref(null);
// console.log(props.filters.jurisdictions);
console.log(props.compare);

function filterPosts() {
    Inertia.get('/conditions/category/' + props.category, {
        jurisdictions: selected_jurisdictions.value,
        tag: selected_tag.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}

watch(selected_tag, value => {
    filterPosts();
});

watch(selected_jurisdictions, value => {
    filterPosts();
});

let showModaltrue = (jurisdictions, topic) => {
    // console.log(jurisdictions);
    modalBody.value = jurisdictions;
    modalTopic.value = topic.name;
    showModal.value = true;

    // topic.value = topic;
}
</script>

<template>
    <AppLayout title="Matrix Comparison">
        <Teleport to="body">
            <!-- use the modal component, pass in the prop -->
            <modal :show="showModal" @close="showModal = false">
                <template #header>
                    <h4>Attachments for {{ modalTopic }}</h4>
                </template>

                <template #body>
                    <table class="documents table table-borderless">
                        <tbody>
                            <template v-for="condition in modalBody">
                                <tr v-if="(condition.attachment != null)">
                                    <td>{{ modalTopic + ' - ' + condition.jurisdiction }}</td>
                                    <td>
                                        <ul class="list-unstyled list-inline">
                                            <li class="list-inline-item me-3">
                                                <a class="py-3" :href="condition.attachmentUrl" target="_blank"
                                                    alt="notification">
                                                    <svg class="me-3" width="30" height="31" viewBox="0 0 30 31"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_42_3119)">
                                                            <path
                                                                d="M15 18.2334C16.3807 18.2334 17.5 17.1141 17.5 15.7334C17.5 14.3527 16.3807 13.2334 15 13.2334C13.6193 13.2334 12.5 14.3527 12.5 15.7334C12.5 17.1141 13.6193 18.2334 15 18.2334Z"
                                                                stroke="#032262" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path
                                                                d="M27.5 15.7334C24.1662 21.5671 20 24.4834 15 24.4834C10 24.4834 5.83375 21.5671 2.5 15.7334C5.83375 9.89965 10 6.9834 15 6.9834C20 6.9834 24.1662 9.89965 27.5 15.7334Z"
                                                                stroke="#032262" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round"></path>
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
                                                <a class="py-3" :href="route('conditions.pdf', condition)"
                                                    target="_blank" alt="notification"><i
                                                        class="bi bi-cloud-download pe-3 py-3"></i>Download</a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </template>

                        </tbody>
                    </table>
                </template>
            </modal>
        </Teleport>
        <template #header>
            <AddOnHeader prefix="conditions" title="Matrixes" />
        </template>

        <div class="container">
            <div class="app-cont filter-cont border border-color-third m-3 pt-5 pb-4 px-3">
                <div class="c-width">
                    <div class="d-flex align-items-center mb-4">
                        <label class="me-3" for="tag">Tag</label>
                        <select class="form-select form-inline mw-s" aria-label=".form-select-sm example"
                            v-model="selected_tag">
                            <option value="" selected>Select tag</option>
                            <option v-for="tag in props.tags" :key="tag.id" :value="tag.id">
                                {{ tag.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="c-width">
                    <div class="row g-0 mb-4 align-items-center">
                        <div class="col-12 col-md-2">
                            <p class="mb-0">Jurisdictions</p>
                        </div>
                        <div class="col-12 col-md-10 ps-3 d-flex flex-column flex-lg-row flex-wrap">
                            <div class="form-check form-check-inline" v-for="jurisdiction in props.jurisdictions"
                                :key="jurisdiction.id">
                                <input :value="jurisdiction.id" class="form-check-input" type="checkbox"
                                    :id="'inlineCheckbox' + jurisdiction.id" v-model="selected_jurisdictions">
                                <label class="form-check-label" :for="'inlineCheckbox' + jurisdiction.id">{{
        jurisdiction.name
}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-cont mb-5 pb-5">
            <div class="container">
                <div v-if="props.compare" class="accordion" id="accordion">
                    <template v-for="subcategory in props.compare" :key="subcategory.id">
                        <div class="accordion-item">
                            <h4 class="accordion-header" :id="'heading' + subcategory.id">
                                <button class="accordion-button position-relative collapsed" type="button"
                                    data-bs-toggle="collapse" :data-bs-target="'#collapse' + subcategory.id"
                                    aria-expanded="true" :aria-controls="'collapse' + subcategory.id">
                                    {{ subcategory.name }}
                                    <span class="position-absolute label-a">Compare</span>
                                </button>
                            </h4>
                            <div :id="'collapse' + subcategory.id" class="accordion-collapse collapse"
                                :class="subcategory.id == subcategory_id ? 'show' : ''"
                                :aria-labelledby="'heading' + subcategory.id" data-bs-parent="#accordion">
                                <div class="accordion-body overflow-auto">
                                    <template v-for="topic in subcategory.topics" :key="topic.id">
                                        <div class="bg-grey comparison-item p-4 mb-5">
                                            <p class="h4">{{ topic.name }}</p>
                                            <p class="date">12/02/2022</p>


                                            <div class="accordion inner bg-grey" :id="'accordionInner' + topic.id">
                                                <template v-for="condition in topic.jurisdictions" :key="condition.id">
                                                    <div class="accordion-item bg-grey py-2">
                                                        <h4 class="accordion-header mb-3"
                                                            :id="'condition-' + condition.id">
                                                            <button class="accordion-button bg-grey py-2" type="button"
                                                                data-bs-toggle="collapse"
                                                                :data-bs-target="'#collapseInner' + condition.id"
                                                                aria-expanded="false"
                                                                :aria-controls="'collapseInner' + condition.id">
                                                                {{ condition.jurisdiction }}
                                                            </button>
                                                        </h4>
                                                        <div :id="'collapseInner' + condition.id"
                                                            class="accordion-collapse collapse show"
                                                            :aria-labelledby="'headingInner' + condition.id"
                                                            :data-bs-parent="'#accordionInner' + topic.id">
                                                            <div class="accordion-body overflow-auto">
                                                                <p>{{ condition.content }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                            </div>


                                            <div class="d-flex align-items-center justify-content-between my-4">
                                                <ul class="list-unstyled list-inline tags mb-0">
                                                    <li class="list-inline-item" v-for="tag in topic.tags"
                                                        :key="tag.id">{{ tag.name }}</li>
                                                </ul>
                                                <button id="show-modal"
                                                    @click="showModaltrue(topic.jurisdictions, topic)"><img class="me-2"
                                                        src="/storage/images/documents.svg">Documents</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
