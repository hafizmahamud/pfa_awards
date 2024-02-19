<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { ref } from 'vue';
import moment from 'moment';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    post: {
        type: Object,
        default: () => ({}),
    },
    selected_jurisdictions: Array,
    selected_tags: Array,
    file: String,
    jurisdictions: Object,
    tags: Object
});

let form = useForm({
    _method: 'put',
    title: props.post.title,
    jurisdictions: props.selected_jurisdictions,
    tags: props.selected_tags,
    file: props.file,
    content: props.post.content
});

// console.log(form.file);
let preview = ref(false);
let content = ref(props.post.content);

let submit = () => {
    form.content = content.value;
    console.log(form);
    form.post(route("posts.update", props.post.id));
}

const current_date = moment().format('Do-MM-YYYY');

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}

</script>
<template>
    <AppLayout title="Create Post">
        <template #header>
            <AddOnHeader prefix="posts" title="Edit Post" @jump-to="jumpTo" />
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
                        <p>Jurisdictions :</p>
                    </div>
                    <div class="col-12 col-md-10 my-3 d-flex flex-column flex-lg-row justify-content-between flex-wrap">
                        <div class="form-check form-check-inline" v-for="jurisdiction in jurisdictions"
                            :key="jurisdiction.id">
                            <input v-model="form.jurisdictions" class="form-check-input" type="checkbox"
                                v-bind:id="'inlineCheckbox' + jurisdiction.id" :value="jurisdiction.id">
                            <label class="form-check-label" v-bind:for="'inlineCheckbox' + jurisdiction.id">{{
                                    jurisdiction.name
                            }}</label>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    <div class="col-12 col-md-2 my-3">
                        <p>Topic Tags :</p>
                    </div>
                    <div class="col-12 col-md-10 my-3 d-flex flex-column flex-lg-row justify-content-between flex-wrap">
                        <div v-for="tag in tags" class="form-check form-check-inline" :key="tag.id">
                            <input v-model="form.tags" class="form-check-input" type="checkbox"
                                v-bind:id="'inlineCheckbox' + tag.id" :value="tag.id">
                            <label class="form-check-label" v-bind:for="'inlineCheckbox' + tag.id">{{ tag.name
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
                        <button class="btn btn-primary" type="submit" :disabled="form.processing">Update</button>
                    </div>
                </div>
            </form>

        </div>

    </AppLayout>
</template>
