<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import { jsPDF } from "jspdf";
import $ from "jquery";

const props = defineProps({
    awards: Boolean,
    comparison_content: Object,
    filters: Object,
    jurisdictions: Object,
    tags: Object,
    clauses: Object,
});

let test = props.comparison_content.jurisdiction;
let selected_tag = ref(props.filters.tag ? props.filters.tag : "");
let selected_topic = ref(props.filters.topic ? props.filters.topic : "");
let selected_jurisdictions = ref(props.filters.jurisdictions ? props.filters.jurisdictions : []);
selected_jurisdictions.value = selected_jurisdictions.value.map(function (str) {
    return parseInt(str);
});

let compare = () => {
    Inertia.get(route('awards-comparison.index'), {
        tag: selected_tag.value,
        jurisdictions: selected_jurisdictions.value,
        topic: selected_topic.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
};

//Print PDF from awards-comparison content
function generatePdf() {

    var doc = new jsPDF('p', 'mm', [210, 297]);
    var elementHTML = document.querySelector("#document");
    let data = props.comparison_content.jurisdiction;
    var col = data.map(x => x.name);
    const json = JSON.stringify(col).replace(/]|[[]/g, '');

    doc.html(elementHTML, {
        callback: function (doc) {
            doc.save(json);
        },
        x: 10,
        y: 10,
        width: 160, //target width in the PDF document
        windowWidth: 650 //window width in CSS pixels
    });
}
</script>

<template>
    <AppLayout title="Awards Comparison">
        <template #header>
            <AddOnHeader prefix="awards-comparison" title="Awards Comparison" />
        </template>

        <div class="mt-5 mb-5 pb-5 border-bottom border-color-third">
            <div class="container">
                <div class="app-cont filter-cont border border-color-third m-3 pt-5 pb-4 px-3">
                    <div class="c-width">
                        <div class="row align-items-center g-0 c-width">
                            <h3 class="mb-3"> Comparison Filter</h3>
                            {{ url }}
                            <div class="row mb-4 d-flex align-items-center">
                                <div class="col-12 col-md-5 d-flex align-items-center me-4 mb-2">
                                    <label class="me-3" for="tag">Tag</label>
                                    <select class="form-select form-inline" aria-label=".form-select-sm example"
                                        v-model="selected_tag">
                                        <option value="" selected>Select tag</option>
                                        <option v-for="tag in props.tags" :key="tag.id" :value="tag.id">
                                            {{ tag.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-5 d-flex align-items-center me-4 mb-2">
                                    <label class="me-3" for="tag">Clause</label>
                                    <select class="form-select form-inline" aria-label=".form-select-sm example"
                                        v-model="selected_topic">
                                        <option value="" selected>Select clause</option>
                                        <option v-for="topic in props.clauses" :key="topic.id" :value="topic.id">
                                            {{ topic.title }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="c-width">
                            <div class="row g-0 mb-4 align-items-center">
                                <div class="col-2">
                                    <p class="my-3 text-center">Jurisdictions</p>
                                </div>
                                <div class="col-12 col-lg-10 d-flex justify-content-between" style="overflow-x:auto">
                                    <!-- {{ selected_jurisdictions }} -->
                                    <div class="form-check form-check-inline" v-for="jurisdiction in jurisdictions"
                                        :key="jurisdiction.id">
                                        <!-- {{ jurisdiction.id }} -->
                                        <input :value="jurisdiction.id" class="form-check-input" type="checkbox"
                                            :id="'inlineCheckbox' + jurisdiction.id" v-model="selected_jurisdictions"
                                            :disabled="selected_jurisdictions.length > 1 && !selected_jurisdictions.includes(jurisdiction.id)">
                                        <label class="form-check-label" :for="'inlineCheckbox' + jurisdiction.id">{{
                                            jurisdiction.name
                                        }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 d-grid gap-2">
                                <button type="button" class="btn btn-primary" @click="compare"
                                    :disabled="selected_jurisdictions.length < 2"> Compare </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="props.awards?.length" id="document">
            <div class="award-comp container">
                <div class="row g-0">
                    <div class="col-12 col-md-6">
                        <div class="my-5 text-center"><img class="logo img-fluid mx-auto" alt="Logo"
                                :src="'/storage/' + props.comparison_content.jurisdiction[0].logo"></div>
                        <h3>{{ props.comparison_content.jurisdiction[0].name }} Police </h3>
                        <h4>{{ props.comparison_content.award_title[0] }}</h4>
                        <p><small>{{ props.comparison_content.terms[0] }}</small></p>

                    </div>
                    <div class="col-12 col-md-6">
                        <div class="my-5 text-center"><img class="logo img-fluid mx-auto" alt="Logo"
                                :src="'/storage/' + props.comparison_content.jurisdiction[1].logo"></div>
                        <h3>{{ props.comparison_content.jurisdiction[1].name }} Police </h3>
                        <h4>{{ props.comparison_content.award_title[1] }}</h4>
                        <p><small>{{ props.comparison_content.terms[1] }}</small></p>
                    </div>
                </div>
            </div>
            <!-- <template v-for="clauses in props.comparison_content.content_comparison"> -->
            <div v-for="(content, index) in props.comparison_content.content_comparison" class="award-comp"
                :class="{ 'bg-grey': index % 2 === 0, 'container': index % 2 !== 0 }">
                <div class="container">
                    <!-- {{ index }} -->
                    <div class="row g-0 mt-5 py-3">
                        <div class="col-12 col-md-2 mt-5">
                            <p class="label mb-0">Category</p>
                            <h3>{{ content.category }}</h3>
                        </div>
                        <div class="col-12 col-md-3 mt-5">
                            <p class="label mb-0">Provision</p>
                            <h3>{{ content.provision }}</h3>
                        </div>
                    </div>

                    <div class="row g-0 mb-5">
                        <div class="col-12 col-md-6 mb-5" v-html="content.content[0]">
                        </div>
                        <div class="col-12 col-md-6 mb-5" v-html="content.content[1]">
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="col-12 text-end mb-5">
                    <button id="print" onclick="window.print()" class="btn-outline-secondary edit-btn me-3">Print<i
                            class="bi bi-printer ms-3"></i></button>
                    <!--<button id="download" href="{{ route('award.downloadPdf') }}" class="btn-outline-secondary edit-btn me-3">Download PDF<i class="bi bi-file-earmark-pdf ms-3"></i></button>   -->
                    <button class="btn-outline-secondary edit-btn me-3" @click="generatePdf">Generate PDF<i
                            class="bi bi-file-earmark-pdf ms-3"></i></button>
                </div>
            </div>
            <!-- </template> -->
        </div>
        <div v-else>
            <div class="container my-3">
                No awards found for the jurisdictions.
            </div>
        </div>

    </AppLayout>
</template>
