<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    project: {
        type: Object,
        default: () => ({}),
    },
    isMember: Boolean,
    isOwner: Boolean
});
</script>

<template>
    <AppLayout :title="project.title">
        <template #header>
            <div class="banner title mb-4">
                <div class="container">
                    <div class="row g-0">
                        <div class="col-8 my-5">
                            <h2>{{ project.title }}</h2>
                        </div>
                        <div v-if="$page.props.auth.admin" class="col-4 my-5 position-relative">
                            <div class="btn btn-add position-absolute end-0">
                                <Link :href="route('projects.edit', project)"
                                    class="d-flex align-items-center btn btn-outline-secondary">
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4 20.8129H8L18.5 10.3129C19.0304 9.78246 19.3284 9.06304 19.3284 8.31289C19.3284 7.56275 19.0304 6.84333 18.5 6.31289C17.9696 5.78246 17.2501 5.48447 16.5 5.48447C15.7499 5.48447 15.0304 5.78246 14.5 6.31289L4 16.8129V20.8129Z"
                                        stroke="#032262" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M13.5 7.3129L17.5 11.3129" stroke="#032262" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                Edit project
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="my-5 pb-5 app-cont">
            <div class="container">
                <div class="col-12 my-5 pt-3">
                    <h3>Description</h3>
                    <p>{{ project.content }}</p>
                </div>

                <div class="col-12 my-5">
                    <h3>Project Owner</h3>
                    <p>{{ project.owner.name }}</p>
                </div>

                <div class="col-12 my-5">
                    <h3>Project Members</h3>
                    <p><template v-for="(member, key) in project.members">{{ (key == 0) ? member.name : ', ' +
        member.name
}}</template></p>
                </div>
                <div v-if="$page.props.auth.admin || isOwner || isMember" class="col-12 my-3">
                    <Link class="btn btn-outline-secondary" :href="route('projectposts.create', project)"><i
                        class="bi bi-plus-circle me-2"></i>
                    Add posts</Link>
                </div>

                <div v-for="post in project.posts" class="col-12 my-3 border-bottom border-color-third" :key="post.id">
                    <div class="project">
                        <p class="card-date">{{ post.updated_at }}</p>
                        <div class="row g-0">
                            <div class="col-12 col-md-6">
                                <h5 class="project-title">{{ post.title }}</h5>
                            </div>
                            <div class="col-12 col-md-6">
                                <p class="text-end"> Posted by: <strong>{{ post.author.name }}</strong></p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 mb-3" v-html="post.content">
                        </div>
                        <div class="col-12 col-sm-12 mb-3">
                            <button v-for="tag in post.tags" type="button" class="tags">{{ tag.name }}</button>
                        </div>
                        <div v-if="post.attachment" class="col-12 mb-3">
                            <div class="btn btn-download p-0">
                                <button type="button" class="btn btn-outline-secondary"><i
                                        class="bi bi-file-earmark-text"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
