<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import moment from 'moment';
import { ref, watch } from 'vue';
import AddOnHeader from '../../Components/AddOnHeader.vue';
import { Inertia } from '@inertiajs/inertia';

let form = useForm({
    project_id: props.project.id,
    title: '',
    tags: [],
    content: '',
    file: ''
});

let submit = () => {
    form.content = content.value;
    form.post(route('projectposts.store', props.project.id));
}

const props = defineProps({
    project: {
        type: Object,
        default: () => ({}),
    },
    tags: Object
});

let preview = ref(false);
let content = ref(null);

const current_date = moment().format('Do-MM-YYYY');

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}

</script>
<template>
    <AppLayout title="Create Post">
        <template #header>
            <AddOnHeader prefix="projectposts" title="Add Post" @jump-to="jumpTo" />
        </template>

        <div class="container">
            <form @submit.prevent="submit">
                <div class="pt-3">
                    <div class="row g-0">
                        <div class="col-12 col-md-2 my-3">
                            <label for="title" class="form-label">Title :</label>
                        </div>
                        <div class="col-12 col-md-10 my-3">
                            <input v-model="form.title" type="title" class="form-control" id="title">
                            <div v-if="form.errors.title" v-text="form.errors.title" class="text-danger text-xs mt-1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    <div class="col-12 col-md-2 my-3">
                        <p>Topic Tags :</p>
                    </div>
                    <div class="col-12 col-md-10 my-3 d-flex justify-content-between flex-wrap">
                        <div v-for="tag in tags" class="form-check form-check-inline" :key="tag.id">
                            <input v-model="form.tags" class="form-check-input" type="checkbox"
                                v-bind:id="'inlineCheckbox' + tag.id" :value="tag.id">
                            <label class="form-check-label" v-bind:for="'inlineCheckbox' + tag.id">{{
                                tag.name
                            }}</label>
                        </div>
                    </div>
                </div>
                <div class="row g-0 mb-3" :class="(preview) ? 'd-none' : 'd-block'">
                    <div class="mb-3">
                        <label for="textarea" class="form-label"></label>
                        <QuillEditor v-model:content="content" class="form-control" theme="snow" toolbar="full"
                            contentType="html">
                        </QuillEditor>
                        <div v-if="form.errors.content" v-text="form.errors.content" class="text-danger text-xs mt-1">
                        </div>
                    </div>
                </div>

                <div class="row g-0 mb-3 border p-3" :class="(!preview) ? 'd-none' : 'd-block'">
                    <!-- Hardcoded Preview Post-->
                    <div class="user-post">
                        <p class="card-date">{{ current_date }}</p>
                        <div class="row g-0">
                            <div class="col-12 col-md-6">
                                <h5 class="post-title">{{ form.title }}</h5>
                            </div>
                            <div class="col-12 col-md-6">
                                <p class="text-end"> Posted by: {{ $page.props.auth.name }}</p>
                            </div>
                        </div>
                        <p class="post-text-preview" v-html="content">

                        </p>
                    </div>
                    <!-- Hardcoded Preview Post End-->
                </div>

                <div class="row g-0 mb-3">
                    <div class="col-12 col-md-8 mb-3">
                        <input type="file" @input="form.file = $event.target.files[0]" />
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                    </div>
                    <div class="col-12 col-md-2 mb-3 d-grid gap-2">
                        <button class="btn btn-primary" @click.prevent="preview = !preview">Preview</button>
                    </div>
                    <div class="col-12 col-md-2 mb-3 d-grid gap-2">
                        <button class="btn btn-primary" type="submit" :disabled="form.processing">Publish</button>
                    </div>
                </div>
            </form>

        </div>

    </AppLayout>
</template>
