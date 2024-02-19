<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { ref } from 'vue';
import moment from 'moment';
import AddOnHeader from '../../Components/AddOnHeader.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    post: {
        type: Object,
        default: () => ({}),
    },
    project: {
        type: Object,
        default: () => ({}),
    },
    tags: Object
});

console.log(props.post);

let form = useForm({
    title: props.post.title,
    tags: props.post.selected_tags,
    file: props.post.attachment,
    content: props.post.content,
});

// console.log(form.file);
let preview = ref(false);
let content = ref(props.post.content);

let submit = () => {
    form.content = content.value;
    // console.log(form);
    form.put(route("projectposts.update", [props.project.id, props.post.id]));
}

const current_date = moment().format('Do-MM-YYYY');

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}

</script>
<template>
    <AppLayout title="Create Post">
        <template #header>
            <AddOnHeader prefix="projectposts" title="Edit Post" @jump-to="jumpTo" />
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
                        <p>Tags :</p>
                    </div>
                    <div class="col-12 col-md-10 my-3 d-flex justify-content-between">
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
                    <div class="col-12 col-md-10 mb-3">
                        <input type="file" @input="form.file = $event.target.files[0]" />
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                    </div>
                    <div class="col-12 col-md-1 mb-3 d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-primary" @click.prevent="preview = !preview">Preview</button>
                    </div>
                    <div class="col-12 col-md-1 mb-3 d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-primary" type="submit" :disabled="form.processing">Update</button>
                    </div>
                </div>
            </form>

        </div>

    </AppLayout>
</template>
