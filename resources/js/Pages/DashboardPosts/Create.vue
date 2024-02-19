<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import moment from 'moment';
import { ref, watch } from 'vue';

let form = useForm({
    title: '',
    content: '',
    type: 'news',
    status: 'draft'
});

let submit = () => {
    form.content = content.value;
    form.post(route('dashboard-posts.store'));
}

const props = defineProps({
    types: Array,
    statuses: Array
});

let preview = ref(false);
let content = ref(null);

const current_date = moment().format('Do-MM-YYYY');

</script>
<template>
    <AppLayout title="Create Post">
        <template #header>
            <div class="container">
                <div class="row g-0">
                    <div class="col-12 col-md-8 my-5">
                        <h2>New news or announcements</h2>
                    </div>
                </div>
            </div>
        </template>

        <div class="container">
            <form @submit.prevent="submit">
                <div class="pt-3">
                    <div class="row g-0">
                        <div class="col-12 col-md-1 my-3">
                            <label for="title" class="form-label">Title :</label>
                        </div>
                        <div class="col-12 col-md-11 my-3">
                            <input v-model="form.title" type="title" class="form-control" id="title">
                            <div v-if="form.errors.title" v-text="form.errors.title" class="text-danger text-xs mt-1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-12 col-md-1 my-3">
                        <p>Type:</p>
                    </div>
                    <div class="col-12 col-md-2 my-3 d-flex justify-content-between">
                        <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                            v-model="form.type">
                            <option v-for="post_type in types" :value="post_type">{{ post_type }}</option>
                        </select>
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
                <div class="row g-0">
                    <div class="col-12 col-md-1 my-3">
                        <p>Status:</p>
                    </div>
                    <div class="col-12 col-md-3 my-3 d-flex justify-content-between">
                        <select class="form-select form-select-sm form-inline" aria-label=".form-select-sm"
                            v-model="form.status">
                            <option v-for="status in statuses" :value="status">{{ status }}</option>
                        </select>
                    </div>
                </div>

                <div class="row g-0 mb-3">
                    <div class="col-12 col-md-8 mb-3">
                    </div>

                    <div class="col-12 col-md-2 mb-3 d-grid gap-2">
                        <button class="btn btn-primary" @click.prevent="preview = !preview">Preview</button>
                    </div>
                    <div class="col-12 col-md-2 mb-3 d-grid gap-2">
                        <button class="btn btn-primary" type="submit" :disabled="form.processing">Save</button>
                    </div>
                </div>
            </form>

        </div>

    </AppLayout>
</template>
