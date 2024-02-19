<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import DatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    jurisdictions: Object,
    clauses: Object,
    subclauses: {
        type: Object,
        default: () => ({})
    },
    award_content: Object,
    tags: Object,
    award: {
        type: Object,
        default: () => ({}),
    },
    pdf: String,
    filters: Object,
    errors: Object,
});

let selected_clause = ref(props.filters.clause ? props.filters.clause : "");
let selected_subclause = ref(props.filters.subclause ? props.filters.subclause : "");
let awardContent = ref(props.award_content ? props.award_content.content : null);
let preview = ref(false);

let form = useForm({
    _method: 'put',
    title: props.award.title,
    jurisdiction_id: props.award.jurisdiction_id,
    start_date: props.award.start_date,
    end_date: props.award.end_date,
    file: props.award.pdf
});

let award_content_form = useForm({
    id: props.award_content == null ? "" : props.award_content.id,
    content: props.award_content == null ? "" : props.award_content.content,
    award_id: props.award_content == null ? props.award.id : props.award_content.award_id,
    subclause_id: props.award_content == null ? selected_subclause.value : props.award_content.subclause_id,
    tags: props.award_content == null ? [] : props.award_content.tags_array,
});

watch([selected_subclause, selected_clause], value => {
    Inertia.get('/awards/' + props.award.id + '/edit', {
        clause: selected_clause.value,
        subclause: selected_subclause.value,
    }, {
        replace: true,
        preserveScroll: true
    });
});

let submit = () => {
    // console.log(form);
    form.post(route("awards.update", props.award.id));
}

let addAwardContent = () => {
    award_content_form.content = awardContent.value;
    Inertia.post('/awards/content', award_content_form, {
        onSuccess: () => {
            // award_content_form.reset();
            selected_clause.value = "";
            selected_subclause.value = "";
            // console.log(award_content_form);
        }
    });
}

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}
</script>

<template>
    <AppLayout title="Edit Award">
        <template #header>
            <AddOnHeader prefix="awards" title="Edit Award" @jump-to="jumpTo" />
        </template>


        <div class="container">
            <h2>Award details</h2>
            <div class="container g-0">
                <div class="col-12 col-md-12 my-5">
                    <div class="border p-3">
                        <form @submit.prevent="submit">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4 col-lg-2 d-flex align-items-center">
                                            <label for="jurisdiction" class="form-label">Jurisdiction:</label>
                                        </div>
                                        <div class="col-sm-8 col-lg-10 mb-3 pe-3">
                                            <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                                                v-model="form.jurisdiction_id">
                                                <option v-for="jurisdiction in jurisdictions" :value="jurisdiction.id">{{jurisdiction.name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 col-lg-2 d-flex align-items-center">
                                            <label for="title" class="form-label">Title:</label>
                                        </div>
                                        <div class="col-sm-8 col-lg-10 mb-3 pe-3">
                                            <input type="text" class="form-control" id="title" v-model="form.title">
                                        </div>
                                        <div v-if="form.errors.title" v-text="form.errors.title"
                                            class="text-danger text-xs mt-1">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 col-lg-2 d-flex align-items-center">
                                            <label for="pdf" class="form-label">Attachment:</label>
                                        </div>
                                        <div class="col-sm-8 col-lg-10 mb-3 pe-3">
                                            <input type="file" @input="form.file = $event.target.files[0]" />
                                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                                {{ form.progress.percentage }}%
                                            </progress>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="row">
                                        <div class="col-sm-4 col-lg-2 d-flex align-items-center">
                                            <label for="start_date" class="form-label">Start date:</label>
                                        </div>
                                    
                                        <div class="col-sm-8 col-lg-10 mb-3 pe-3">
                                            <DatePicker type="text" id="start_date"
                                                v-model="form.start_date" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 col-lg-2 d-flex align-items-center">
                                            <label for="end_date" class="form-label">End date:</label>
                                        </div>
                                        <div class="col-sm-8 col-lg-10 mb-3 pe-3">
                                            <DatePicker type="text" id="end_date"
                                                v-model="form.end_date" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <div class="col-12 col-lg-2 mb-3 gap-2">
                                    <button type="submit" class="btn btn-success mt-4 w-100"
                                        :disabled="form.processing">Update</button>
                                </div> 
                            </div>
                            <!-- <div class="row mb-3 g-0">
                                <div class="col-md-1">
                                    <label for="jurisdiction" class="form-label">Jurisdiction:</label>
                                </div>
                                <div class="col-md-5 mb-3 pe-3">
                                    <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                                        v-model="form.jurisdiction_id">
                                        <option v-for="jurisdiction in jurisdictions" :value="jurisdiction.id">{{jurisdiction.name}}</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label for="start_date" class="form-label">Start date:</label>
                                </div>
                                <div class="col-md-5">
                                    <DatePicker type="text" class="form-control" id="start_date"
                                        v-model="form.start_date" />
                                </div>
                            </div>
                            <div class="row mb-3 g-0">
                                <div class="col-md-1">
                                    <label for="title" class="form-label">Title:</label>
                                </div>
                                <div class="col-md-5 mb-3 pe-3">
                                    <input type="text" class="form-control" id="title" v-model="form.title">
                                </div>
                                <div v-if="form.errors.title" v-text="form.errors.title"
                                    class="text-danger text-xs mt-1">
                                </div>
                                    <div class="col-md-1">
                                    <label for="end_date" class="form-label">End date:</label>
                                </div>
                                <div class="col-md-5">
                                    <DatePicker type="text" class="form-control" id="end_date"
                                        v-model="form.end_date" />
                                </div>
                            </div>
                            
                            <div class="row g-0">
                                <div class="col-md-1">
                                    <label for="pdf" class="form-label">Attachment:</label>
                                </div>
                                <div class="col-md-11">
                                    <input type="file" @input="form.file = $event.target.files[0]" />
                                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                        {{ form.progress.percentage }}%
                                    </progress>
                                </div>
                            </div>                        
            
                            <div class="row g-0">
                                <div class="col-12 col-lg-2 float-right mb-3 d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-success mt-4"
                                    :disabled="form.processing">Update</button>
                                </div> 
                            </div>-->
                        </form>
                    </div>
                </div>

                <div class="col-12 col-md-12 my-5 border p-3">
                    <iframe :src="pdf" width="100%" height="600" frameborder="0">
                        This browser does not support PDFs. Please download the PDF to view it: Download PDF
                    </iframe>
                </div>
            </div>
            <div class="container mb-5">
                <h2>Clauses and Subclauses</h2>
                <div class="g-0 mb-2 mt-4 mx-2 row">
                    <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                        v-model="selected_clause">
                        <option selected value="">Select clause</option>
                        <option v-for="clause in clauses" :value="clause.id">{{
        clause.title
}}</option>
                    </select>

                </div>
                <div class="g-0 mx-2 row">
                    <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                        v-model="selected_subclause">
                        <option selected value="">Select subclause</option>
                        <option v-for="subclause in subclauses" :key="subclause.id" :value="subclause.id">{{
        subclause.title
}}</option>
                    </select>
                    <div v-if="errors.subclause_id" class="text-danger text-xs mt-1">{{ errors.subclause_id }}</div>
                </div>
                <div v-if="selected_subclause != null" class="g-0 mx-2 row">
                    <form @submit.prevent="addAwardContent">
                        <div class="c-width">
                            <div class="col-12 col-md-2 my-3">
                                <label>Tags:</label>
                            </div>
                            <div class="col-12 col-md-10 my-5 d-flex flex-column flex-lg-row justify-content-between flex-wrap">
                                <div v-for="tag in tags" class="form-check form-check-inline" :key="tag.id">
                                    <input v-model="award_content_form.tags" class="form-check-input" type="checkbox"
                                        v-bind:id="'inlineCheckbox' + tag.id" :value="tag.id">
                                    <label class="form-check-label" v-bind:for="'inlineCheckbox' + tag.id">{{ tag.name
}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-5" :class="!preview ? 'd-block' : 'd-none'">
                            <QuillEditor v-model:content="awardContent" class="form-control" theme="snow" toolbar="full"
                                content-type="html" rows="10"></QuillEditor>
                            <div v-if="errors.content" class="text-danger text-xs mt-1">{{ errors.content }}</div>
                        </div>
                        <div class="border container my-5 p-3" v-html="awardContent"
                            :class="preview ? 'd-block' : 'd-none'">

                        </div>
                        <div class="col-12 col-md-1 mb-3 d-grid gap-2 d-md-flex">
                            <button v-if="!preview" class="btn btn-primary"
                                @click.prevent="preview = !preview">Preview</button>
                            <button v-if="preview" class="btn btn-primary"
                                @click.prevent="preview = !preview">Edit</button>
                            <button class="btn btn-primary" type="submit"
                                :disabled="award_content_form.processing">Update</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
