<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
import PaginationLink from '@/Components/PaginationLink.vue';
import { computed, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import JurisdictionTab from '../../Components/JurisdictionTab.vue';
import moment from 'moment';
import AddOnHeader from '@/Components/AddOnHeader.vue';

const props = defineProps({
    content: Object,
    award: {
        type: Object,
        default: () => ({}),
    },
    awards: {
        type: Object,
        default: () => ({}),
    },
    filters: Object,
    jurisdictions: Object,
    tags: Object,
});

// console.log(props.authors);
const form = useForm();

function destroy(id) {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route('awards.destroy', id));
    }
}

let selected_tag = ref(props.filters.tag ? props.filters.tag : "");
let award_id = ref(props.filters.award_id ? props.filters.award_id : "");
let active_jurisdiction = ref(props.filters.jurisdiction ? props.filters.jurisdiction : 1);
let clause_id = ref(props.filters.clause_id ? props.filters.clause_id : "");

console.log(props.content);

function filterPosts() {
    Inertia.get('/awards', {
        tag: selected_tag.value,
        award_id: award_id.value,
        jurisdiction: active_jurisdiction.value,
        clause_id: clause_id.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}

watch(selected_tag, value => {
    filterPosts();
});

watch(award_id, value => {
    filterPosts();
});

function selectJurisdiction(id) {
    active_jurisdiction.value = id;
    award_id.value = "";
    filterPosts();
}

const startDate = computed(() => {
    return moment(String(props.award.start_date)).format('DD/MM/YYYY');
});

const endDate = computed(() => {
    return moment(String(props.award.end_date)).format('DD/MM/YYYY');
});

</script>

<template>
    <AppLayout title="Awards">
        <template #header>
            <AddOnHeader prefix="awards" title="Awards" />
            <JurisdictionTab :jurisdictions="jurisdictions" :active="active_jurisdiction"
                @filter-juridiction="selectJurisdiction">
            </JurisdictionTab>
        </template>

        <div class="mb-5 pb-5 app-cont">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-federal" role="tabpanel" aria-labelledby="nav-federal-tab">
                    <div class="container-fluid pb-4 mb-5 px-0 border-bottom border-color-third">
                        <div class="row  g-0 c-width justify-content-around">
                            <div class="col-12 col-md-3 mb-2 mt-2">
                                <div class="d-flex align-items-center ">
                                    <label class="me-3" for="tag">Tag</label>
                                    <select class="form-select form-inline " aria-label=".form-select-sm example"
                                        v-model="selected_tag">
                                        <option value="" selected>Select tag</option>
                                        <option v-for="tag in props.tags" :key="tag.id" :value="tag.id">
                                            {{ tag.name }}
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-12 col-md-4 mb-2 mt-2">
                                <div class="d-flex align-items-center">
                                    <label class="me-3" for="tag">Version</label>
                                    <select class="form-select form-inline " aria-label=".form-select-sm example"
                                        v-model="award_id">
                                        <option value="" selected>Select version</option>
                                        <option v-for="award in props.awards" :key="award.id" :value="award.id">{{
                                            award.title
                                        }}
                                        </option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-12 col-md-4 mb-2 text-end" v-if="award != null">
                                <div class="btn btn-download top-0 end-0">
                                    <a :href="route('awards.pdf', award.id)" class="btn btn-outline-secondary">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Download
                                        agreement
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <template v-if="award != null">
                        <div class="container">
                            <div class="col-12 col-md-12 my-3">
                                <div class="date-range mb-5">
                                    <p class="d-flex align-items-center justify-content-center mb-0 text-uppercase">START
                                        DATE: {{ startDate }}
                                        <img class="mx-3" src="/storage/images/date-sep.svg"> EXPIRY DATE: {{ endDate }}
                                    </p>
                                </div>
                                <h3 class="mb-5">{{ award.title }}</h3>
                            </div>
                        </div>

                        <div class="container my-3">
                            <div class="accordion" id="accordion" v-for="clause in content" :key="clause.id">
                                <div class="accordion-item ">
                                    <h4 class="accordion-header" :id="'heading' + clause.id">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            :data-bs-target="'#collapse' + clause.id" aria-expanded="true"
                                            :aria-controls="'collapse' + clause.id">{{ clause.title }}</button>
                                    </h4>
                                    <div :id="'collapse' + clause.id" class="accordion-collapse collapse"
                                        :class="clause.id == clause_id ? 'show' : ''"
                                        :aria-labelledby="'heading' + clause.id" data-bs-parent="#accordion">
                                        <div class="accordion-body overflow-auto" v-for="subclause in clause.subclauses"
                                            :key="subclause.id" :id="'view-' + subclause.id">
                                            <p><strong>{{ subclause.title }}</strong></p>
                                            <div v-html="subclause.content"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="container my-3">No award found.</div>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
