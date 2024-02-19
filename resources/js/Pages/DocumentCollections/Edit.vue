<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AddOnHeader from '@/Components/AddOnHeader.vue';

const props = defineProps({
    documents: {
        type: Object,
        default: () => ({}),
    },
    jurisdictions: Object,
    tags: Object,
    selected_jurisdictions: Array,
    selected_tags: Array,
    errors: Object,
});


const form = useForm({
    _method: 'put',
    title: props.documents.title,
    description: props.documents.content,
    jurisdictions: props.selected_jurisdictions,
    tags: props.selected_tags,
    files: [],
    documents: []
});


let submit = () => {
    form.post(route("document-collections.update", props.documents.id), {
        onSuccess: () => form.documents.reset()
    });
}

const triggerFileUpload = () => {
    document.getElementById('file-upload').click();
};

const fileUpload = (event) => {
    if (event.target.files.length > 0) {
        handleFile(event.target.files);
    }
};

const handleFile = (files) => {
    "use strict";

    Array.from(files).forEach(file => {
        form.files.push(file);
    });

    Array.from(files).forEach(file => {
        form.documents.push({
            type: file.type,
            name: file.name,
            size: file.size,
        })
    });
}

const formDelete = useForm({})
function destroy(id) {
    if (confirm("Are you sure you want to delete this file?")) {
        formDelete.delete(route('document-collections.destroy', id));
    }
}

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}
</script>


<template>
    <AppLayout title="Documents">
        <template #header>
            <AddOnHeader prefix="document-collections" title="Edit Document" @jump-to="jumpTo" />
        </template>

        <div class="container">
            <form @submit.prevent="submit">
                <div class="row g-0 mt-5">
                    <div class="col-12 col-lg-6 mb-4">
                        <div class="form-group row">
                            <label for="collection-title" class="col-sm-4 mb-4 col-form-label">Collection title :
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="collection-title" class="form-control" v-model="form.title">
                                <div v-if="form.errors.title" v-text="form.errors.title"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="project-description" class="col-sm-4 mb-4 col-form-label">Description : </label>
                            <div class="col-sm-8 mb-4">
                                <textarea type="text" id="project-title" class="form-control"
                                    v-model="form.description"></textarea>
                                <div v-if="form.errors.description" v-text="form.errors.description"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 mb-4">
                                <button type="button" class="btn border-0" @click.prevent="triggerFileUpload">
                                    <i class="bi bi-plus-lg me-1"></i> Add new document
                                </button>
                                <input type="file" id="file-upload" name="files" style="display: none" v-on="form.files"
                                    @change.prevent="fileUpload" accept="application/pdf" multiple>
                                <div v-if="form.errors.files" v-text="form.errors.files"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                        <div class="form-group row" v-show="form.documents.length != 0">
                            <div class="col-sm-8 offset-sm-4 mb-4">
                                <table>
                                    <tbody>
                                        <tr v-for="(document, $index) in form.documents" :key="$index" colspan="3">
                                            <td class="me-2" colspan="2">
                                                <p>{{ document.name }}</p>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger"
                                                    @click.prevent="form.documents.splice($index, 1)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                            <div v-if="form.errors.documents" v-text="form.errors.documents"
                                                class="text-danger text-xs mt-1"></div>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="form-group row" v-show="form.documents.documents != 0">
                            <div class="col-sm-8 mb-4">
                                <table>
                                    <tbody>
                                        <tr v-for="document in documents.documents" colspan="3">
                                            <td class="me-2" colspan="2">
                                                <p>{{ document.file_name }}</p>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger"
                                                    @click="destroy(document.id)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4 ps-5">
                        <div class="Jurisdiction mb-4">
                            <legend class="mb-3"><b>Jurisdiction</b></legend>
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12" v-for="jurisdiction in jurisdictions"
                                    :key="jurisdiction.id">
                                    <div class="mb-2 form-check">
                                        <input v-model="form.jurisdictions" class="form-check-input" type="checkbox"
                                            v-bind:id="'inlineCheckbox' + jurisdiction.id" :value="jurisdiction.id">
                                        <label class="form-check-label" v-bind:for="'inlineCheckbox' + jurisdiction.id">
                                            {{ jurisdiction.name }}</label>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="topic-tags mb-4">
                            <legend class="mb-3"><b>Topic tags</b></legend>
                            <div class="row">

                                <div v-for="tag in tags" class="col-lg-6 col-md-12" :key="tag.id">
                                    <div class="mb-2 form-check">
                                        <input v-model="form.tags" class="form-check-input" type="checkbox"
                                            v-bind:id="'inlineCheckbox' + tag.id" :value="tag.id">
                                        <label class="form-check-label" v-bind:for="'inlineCheckbox' + tag.id">{{
                                            tag.name
                                        }}</label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center text-lg-end d-grid gap-2 d-lg-block mb-5">
                    <button id="publish" class="btn-primary">Publish</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
