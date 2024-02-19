<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
import AddOnHeader from '../../Components/AddOnHeader.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps(['jurisdictions', 'tags']);

const form = useForm({
    title: '',
    description: '',
    jurisdictions: [],
    tags: [],
    documents: [],
    files: [],
});

const selectJurisdictions = (event) => {
    event.target.checked ?
        form.jurisdictions.push(event.target.value) :
        form.jurisdictions = form.jurisdictions.filter(jurisdiction => jurisdiction !== event.target.value);
};

const selectTags = (event) => {
    event.target.checked ?
        form.tags.push(event.target.value) :
        form.tags = form.tags.filter(tag => tag !== event.target.value);
};

const submit = () => {
    form.post(route('document-collections.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('title', 'description', 'jurisdictions', 'tags', 'documents');
        },
    });
};

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

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}

</script>
<template>
    <AppLayout title="Documents">
        <template #header>
            <AddOnHeader prefix="document-collections" title="Add Document" @jump-to="jumpTo" />
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
                                <input type="file" id="file-upload" style="display: none" @change.prevent="fileUpload"
                                    accept="application/pdf" multiple>
                                <div v-if="form.errors.files" v-text="form.errors.files"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                        <div class="form-group row" v-show="form.documents.length != 0">
                            <div class="col-sm-8 mb-4">
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
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-if="form.errors.documents" v-text="form.errors.documents"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4 ps-5">
                        <div class="Jurisdiction mb-4">
                            <legend class="mb-3"><b>Jurisdiction</b></legend>
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12" v-for="juridiction in jurisdictions"
                                    :key="juridiction.id">
                                    <div class="mb-2 form-check">
                                        <input type="checkbox" class="form-check-input"
                                            :id="'juridiction-' + juridiction.id" :value="juridiction.id"
                                            @input.prevent="selectJurisdictions">
                                        <label class="form-check-label" :for="'juridiction-' + juridiction.id">{{
                                            juridiction.name
                                        }}</label>
                                    </div>
                                    <div v-if="form.errors.jurisdictions" v-text="form.errors.jurisdictions"
                                        class="text-danger text-xs mt-1"></div>
                                </div>
                            </div>
                        </div>

                        <div class="topic-tags mb-4">
                            <legend class="mb-3"><b>Topic tags</b></legend>
                            <div class="row">
                                <div class="col-lg-6 col-md-12" v-for="tag in tags" :key="tag.id">
                                    <div class="mb-2 form-check">
                                        <input type="checkbox" class="form-check-input" :id="'tag-' + tag.id"
                                            :value="tag.id" @input.prevent="selectTags">
                                        <label class="form-check-label text-wrap" :for="'tag-' + tag.id">{{
                                            tag.name
                                        }}</label>
                                    </div>
                                    <div v-if="form.errors.tags" v-text="form.errors.tags"
                                        class="text-danger text-xs mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center text-lg-end d-grid gap-2 d-md-block mb-5">
                    <button id="publish" class="btn-primary">Publish</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
