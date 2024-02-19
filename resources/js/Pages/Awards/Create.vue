<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import DatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';


const props = defineProps({
    jurisdictions: Object,
    tags: Object,
    awards: {
        type: Object,
        default: () => ({}),
    },
    filters: Object,
    errors: Object,
});

let form = useForm({
    title: '',
    jurisdiction_id: '',
    start_date: '',
    end_date: '',
    file: ''
});

let submit = () => {
    Inertia.post('/awards', form);
}

let selected_jurisdiction = ref(props.filters.jurisdiction ? props.filters.jurisdiction : "");
let selected_award = ref("");
let edit_award = ref("");

watch(selected_jurisdiction, value => {
    Inertia.get('/awards/create', {
        jurisdiction: selected_jurisdiction.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
});

watch(selected_award, value => {
    edit_award = '/awards/' + selected_award.value + '/edit';
});

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}
</script>

<template>
    <AppLayout title="Create Award">
        <template #header>
            <AddOnHeader prefix="awards" title="Add or edit Awards" @jump-to="jumpTo" />
        </template>

        <div class="container">
            <div class="p-3">
                <div class="row g-0 justify-content-around">

                    <div class="col-12 col-md-5 my-3 border p-3 ">
                        <form @submit.prevent="submit">
                            <label for="title" class="form-label">Add new award</label>
                            <div class="my-3">
                                <label for="title" class="form-label">Title: </label>
                                <div class="col-12">
                                    <input type="text" class="form-control" id="title" v-model="form.title" />
                                    <div v-if="errors.title" class="text-danger text-xs mt-1">{{ errors.title }}</div>
                                </div>

                            </div>
                            <div class="col-12">
                                <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm "
                                    aria-placeholder="Select jurisdiction..." v-model="form.jurisdiction_id">
                                    <option selected value="">Jurisdiction</option>
                                    <option v-for="jurisdiction in jurisdictions" :key="jurisdiction.id"
                                        :value="jurisdiction.id">{{ jurisdiction.name }}</option>
                                </select>
                                <div v-if="errors.jurisdiction_id" class="text-danger text-xs mt-1">{{
                                        errors.jurisdiction_id
                                }}</div>
                            </div>
                            <div class="my-3">
                                <label for="start-date" class="form-label">Start date: </label>
                                <div class="col-12">
                                    <!-- <div class="input-group date" id="datepicker"> -->
                                    <DatePicker type="text" class="form-control" id="start-date"
                                        v-model="form.start_date" />

                                    <!-- </div> -->
                                </div>
                                <div v-if="errors.start_date" class="text-danger text-xs mt-1">{{ errors.start_date }}
                                </div>
                            </div>
                            <div class="my-3">
                                <label for="end-date" class="form-label">End date: </label>
                                <div class="col-12">
                                    <!-- <div class="input-group date" id="datepicker"> -->
                                    <DatePicker type="text" class="form-control" id="end-date"
                                        v-model="form.end_date" />

                                    <!-- </div> -->
                                </div>
                                <div v-if="errors.end_date" class="text-danger text-xs mt-1">{{ errors.end_date }}</div>
                            </div>

                            <div class="my-3">
                                <input type="file" @input="form.file = $event.target.files[0]" />
                                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                    {{ form.progress.percentage }}%
                                </progress>
                                <div v-if="errors.file" class="text-danger text-xs mt-1">{{ errors.file }}</div>
                            </div>

                            <div class="my-3 d-md-flex justify-content-md-end">
                                <button class="btn btn-primary" type="submit">Save award</button>
                            </div>
                        </form>
                    </div>




                    <div class="col-12 col-md-5 my-3 ps-3 border p-3 ">
                        <label for="jurisdiction" class="form-label">Jurisdiction</label>
                        <div class="col-12 col-md-12 mb-3">
                            <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                                aria-placeholder="Select jurisdiction" v-model="selected_jurisdiction">
                                <option selected value="">Jurisdiction</option>
                                <option v-for="jurisdiction in jurisdictions" :key="jurisdiction.id"
                                    :value="jurisdiction.id">{{ jurisdiction.name }}</option>
                                <option value="3"></option>
                            </select>
                            <div v-if="form.errors.selected_jurisdiction" v-text="form.errors.selected_jurisdiction"
                                class="text-danger text-xs mt-1"></div>
                        </div>

                        <label for="title" class="form-label">View award</label>
                        <div class="col-12 col-md-12 mb-3">
                            <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                                aria-placeholder="Select awards" v-model="selected_award">
                                <option selected value="">Award</option>
                                <option v-for="award in awards" :key="award.id" :value="award.id">{{ award.title }}
                                </option>
                            </select>
                            <div v-if="form.errors.selected_award" v-text="form.errors.selected_award"
                                class="text-danger text-xs mt-1"></div>
                        </div>

                        <div v-if="edit_award != ''" class="col-12 col-md-12 mb-3 d-md-flex justify-content-md-end">
                            <Link :href="edit_award" class="btn btn-primary">View</Link>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </AppLayout>
</template>
