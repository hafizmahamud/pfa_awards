<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AddOnHeader from '../../Components/AddOnHeader.vue';

const props = defineProps({
    jurisdictions: Object,
    filters: Object,
    subcategories: Object,
    categories: Object,
    topics: Object,
    tags: Object,
    condition: Object
});

let selected_category = ref(props.filters.category_id ? props.filters.category_id : '');
let selected_subcategory = ref(props.filters.subcategory_id ? props.filters.subcategory_id : '');
let selected_topic = ref(props.filters.topic_id ? props.filters.topic_id : '');
let selected_jurisdiction = ref(props.filters.jurisdiction_id ? props.filters.jurisdiction_id : '');

let form = useForm({
    name: props.condition == null ? '' : props.condition.condition_topic.name,
    jurisdiction_id: selected_jurisdiction.value,
    subcategory_id: selected_subcategory.value,
    category_id: selected_category.value,
    condition_topic_id: selected_topic.value,
    tags: props.condition == null ? [] : props.condition.condition_topic.tags_array,
    content: props.condition == null ? '' : props.condition.content,
    file: props.condition == null ? '' : props.condition.attachment,
    condition_id: props.condition == null ? '' : props.condition.id
});

let submit = () => {
    form.post('/conditions');
}

watch([selected_category, selected_subcategory, selected_topic, selected_jurisdiction], value => {
    Inertia.get('/conditions/create', {
        category_id: selected_category.value,
        subcategory_id: selected_subcategory.value,
        topic_id: selected_topic.value,
        jurisdiction_id: selected_jurisdiction.value
    }, {
        // preserveState: true,
        replace: true,
        preserveScroll: true
    });
});

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}
</script>
<template>
    <AppLayout title="Add or edit Conditions">
        <template #header>
            <AddOnHeader prefix="conditions" title="Edit Conditions" @jump-to="jumpTo" />
        </template>

        <div class="mt-5 mb-5 pb-5">
            <div class="container-fluid pb-4 mb-5 px-0 border-bottom border-color-third">
                <div class="app-cont">
                    <div class="c-width">
                        <div class="row align-items-center g-0 mt-3">
                            <div class="col-12 col-md-3 mb-3 me-4 align-center">
                                <select v-model="selected_jurisdiction" class="form-select form-select-sm form-inline"
                                    aria-label=".form-select-sm">
                                    <option selected value="">Jurisdiction</option>
                                    <option v-for="jurisdiction in jurisdictions" :value="jurisdiction.id">{{
                                        jurisdiction.name
                                    }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 mb-3 me-4 align-center">
                                <select v-model="selected_category" class="form-select form-select-sm form-inline"
                                    aria-label=".form-select-sm">
                                    <option selected value="">Category</option>
                                    <option v-for="category in categories" :value="category.id">{{
                                        category.name
                                    }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3 mb-3 align-center">
                                <select v-model="selected_subcategory" class="form-select form-select-sm form-inline"
                                    aria-label=".form-select-sm">
                                    <option value="" selected>Subcategory</option>
                                    <option v-for="subcategory in subcategories" :value="subcategory.id">{{
                                        subcategory.name
                                    }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-cont">
                <div class="container">
                    <div class="row" :class="{ 'justify-content-center': condition == null }">
                        <div class="col-12 mb-4"
                            :class="{ 'col-lg-8': condition == null, 'col-lg-6': condition != null }">
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
                                        <tr v-for="topic in topics">
                                            <td>{{ topic.updated_at }}</td>
                                            <td>{{ topic.name }}</td>
                                            <td><button id="edit" class="btn-none"
                                                    @click="selected_topic = topic.id">Edit</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-4">
                                <!-- <button id="add-cond" class="btn-primary">New condition</button> -->
                            </div>
                        </div>

                        <div v-if="condition != null" class="col-12 col-lg-6">
                            <form @submit.prevent="submit">
                                <div class="add-edit border border-color-secondary p-3">
                                    <div class="wrapper">
                                        <h3 class="mt-2 mb-4">Publishing Topic
                                        </h3>
                                        <div class="mb-4 row g-3 align-items-center">
                                            <div class="col-1 me-4">
                                                <label for="name" class="col-form-label">Name:</label>
                                            </div>
                                            <div class="col-10">
                                                <input v-model="form.name" type="text" id="name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="topic-tags mb-4">
                                            <legend class="mb-3"><b>Topic tags</b></legend>
                                            <div class="row">
                                                <div v-for="tag in tags" class="col-4" :key="tag.id">
                                                    <div class="mb-2 form-check">
                                                        <input v-model="form.tags" type="checkbox"
                                                            class="form-check-input" :id="'topic-tag-' + tag.id"
                                                            :value="tag.id">
                                                        <label class="form-check-label d-inline"
                                                            :for="'topic-tag-' + tag.id">{{
                                                                tag.name
                                                            }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="description" class="form-label d-none">Description</label>
                                            <textarea v-model="form.content" class="form-control" id="description"
                                                rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 align-items-center justify-content-between row">
                                    <div class="col-2">
                                        <div class="col-4">
                                            <label for="pdf" class="form-label">Document:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="file" @input="form.file = $event.target.files[0]" />
                                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                                {{ form.progress.percentage }}%
                                            </progress>
                                        </div>
                                    </div>
                                    <div class="col-10 text-end">
                                        <!-- <button id="preview"
                                            class="btn-outline-secondary edit-btn me-3">Preview</button> -->
                                        <button type="submit" id="publish" class="btn-primary">Publish</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
