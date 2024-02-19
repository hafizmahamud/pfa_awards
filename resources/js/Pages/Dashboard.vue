<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    posts_documents: {
        type: Object,
        default: () => ({}),
    },
    news_announcements: {
        type: Object,
        default: () => ({}),
    },
});

let s = ref("");

let search = () => {
    Inertia.get('/search', {
        's': s.value,
        'in': ['all']
    });
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="row g-0">
                <div class="col-8 my-5">
                    <h2>Dashboard</h2>
                </div>
                <div class="col-4 my-5">
                    <div class="row height">
                        <div>
                            <div class="input-group position-relative">
                                <input v-model="s" placeholder="Search" class="form-control border-0 rounded-pill"
                                    type="search" id="search-input" />
                                <span class="input-group-append position-absolute">
                                    <button
                                        class="btn btn-outline-secondary bg-white border-0 ms-n5 rounded-pill font-secondary"
                                        type="button">
                                        <i class="bi bi-search" @click.prevent="search"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- <Welcome /> -->
                    <div class="main">
                        <div class="container pb-5">
                            <div class="row g-0">
                                <div class="col-sm-6 my-5 pe-3 ps-3">
                                    <div class="row">
                                        <div class="col-lg-6 col-12 text-center text-lg-start">
                                            <h3 class="mb-4">
                                                News & announcements
                                            </h3>
                                        </div>
                                        <div class="col-lg-6 col-12 mb-4 text-center text-lg-end">
                                            <Link class="btn btn-outline-secondary" :href="
                                                route(
                                                    'dashboard-posts.create'
                                                )
                                            " v-if="$page.props.auth.admin"><i class="bi bi-plus-circle me-2"></i>
                                            Add news / announcements</Link>
                                        </div>
                                    </div>

                                    <template v-for="news in news_announcements">
                                        <div class="card-body">
                                            <p class="card-date">
                                                {{ news.updated_at }}
                                            </p>
                                            <h4 class="card-title">
                                                {{ news.title }}
                                            </h4>
                                            <p class="card-text" v-html="news.content"></p>
                                        </div>
                                        <hr class="my-4" />
                                    </template>


                                </div>

                                <div class="col-sm-6 my-5 pe-3 ps-3">
                                    <div class="row mb-4">
                                        <div class="col-lg-6 col-12 text-center text-lg-start">
                                            <h3 class="mb-4">What's New</h3>
                                        </div>

                                        <div class="col-lg-6 col-12 text-center text-lg-end">
                                            <Link class="btn btn-outline-secondary" :href="route('posts.create')"
                                                v-if="$page.props.auth.admin">
                                            <i class="bi bi-plus-circle me-2"></i>
                                            Add new post</Link>
                                        </div>
                                    </div>

                                    <div class="new-dashboard">
                                        <template v-for="card in posts_documents">
                                            <div class="card-news">
                                                <div class="row g-0">
                                                    <div class="card-body">
                                                        <p class="card-date">
                                                            {{
                                                                card.updated_at
                                                            }}
                                                        </p>
                                                        <h4 class="card-title">
                                                            {{ card.title }}
                                                        </h4>
                                                        <p class="card-text" v-html="
                                                            card.content
                                                        "></p>
                                                        <button v-for="tag in card.tags" type="button" class="tags">
                                                            {{ tag.name }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="my-4" />
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
