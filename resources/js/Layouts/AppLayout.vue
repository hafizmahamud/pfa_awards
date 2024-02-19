<script setup>
import { ref, VueElement } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Head, Link } from '@inertiajs/inertia-vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
let isIndexRoute =
    // route().current('dashboard')
    route().current('awards.index')
    || route().current('posts.index')
    || route().current('document-collections.index')
// || route().current('awards_comparison')
// || route().current('projects.index');
const switchToTeam = (team) => {
    Inertia.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    Inertia.post(route('logout'));
};
</script>

<template>
    <header>
        <div class="container">
            <div class="my-3">
                <div class="row align-center ">
                    <div class="col-6 col-lg-8 d-flex align-items-center">
                        <Link :href="route('dashboard')">
                        <img class="main-logo img-fluid" :src="$page.props.global.logo">
                        </Link>
                    </div>
                    <div class="col-6 col-lg-4 my-4 ">
                                        <div class="d-flex align-items-center fw-500 justify-content-end text-center">
                                            <a :href="route('profile.show')" class="pe-2" id="navbarDropdown"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <img class='img-fluid' src="/storage/images/user-alt.svg">
                                                {{ $page.props.global.greeting }},
                                                {{ $page.props.user.name }}!
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end text-small rounded py-0 mt-2 border-0 shadow"
                                                aria-labelledby="navbarDropdown">
                                                <div class="card border-0">
                                                    <div class="card-header p-4">
                                                        <div class="d-flex align-items-center">
                                                            <a :href="route('profile.show')" class="pe-2"
                                                                id="navbarDropdown" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <img class='img-fluid'
                                                                    src="/storage/images/user-alt.svg">
                                                                {{ $page.props.global.greeting }},
                                                                {{ $page.props.user.name }}!
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <Link :href="route('profile.show')"
                                                            class="dropdown-item px-4 py-3 border-bottom">Profile</Link>
                                                        <Link class="dropdown-item px-4 py-3 border-bottom text-danger"
                                                            @click.prevent="logout"> Logout</Link>
                                                        <Link :href="route('admin.posts')" v-if="$page.props.auth.admin"
                                                            class="dropdown-item px-4 py-3 border-bottom text-danger">
                                                        Admin</Link>
                                                    </div>
                                                </div>
                                            </ul>
                                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
        <hr class=" my-1">
        <div class="border-bottom border-3">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar menu justify-content-end">
                    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 w-100 justify-content-between">
                            <li class="nav-item">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard
                                </NavLink>
                            </li>
                            <li class="nav-item">
                                <NavLink :href="route('awards.index')" :active="route().current('awards.*')">Awards
                                </NavLink>
                            </li>
                            <li class="nav-item">
                                <NavLink :href="route('conditions.index')" :active="route().current('conditions.*')">
                                    Matrixes</NavLink>
                            </li>
                            <li class="nav-item">
                                <NavLink :href="route('posts.index')" :active="route().current('posts.*')">Posts
                                </NavLink>
                            </li>
                            <li class="nav-item">
                                <NavLink :href="route('document-collections.index')"
                                    :active="route().current('document-collections.*')">Documents
                                </NavLink>
                            </li>
                            <li class="nav-item">
                                <NavLink :href="route('awards-comparison.index')"
                                    :active="route().current('awards_comparison.*')">
                                    Awards
                                    Comparison
                                </NavLink>
                            </li>
                            <li class="nav-item">
                                <NavLink :href="route('projects.index')" :active="route().current('projects.*')">
                                    Projects</NavLink>
                            </li>
                            <li v-if="$page.props.auth.admin" class="nav-item">
                                <NavLink :href="route('user.index')" :active="route().current('user.*')">Manage User
                                </NavLink>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>


    <!-- Page Heading -->
    <!-- <header v-if="$slots.header" class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        </div>
    </header> -->

    <div class="banner title" :class="[isIndexRoute ? 'border-bottom border-3 mb-4' : '']">
        <div class="container" :class="[!isIndexRoute ? 'mb-4' : '']">
            <slot name="header" />
        </div>
    </div>

    <!-- Page Content -->
    <main>
        <slot />
        <slot name="footer" />
    </main>

    <footer>
        <div class="container">
            <div class="footer-menu row align-center pt-4">
                <div class=" col-6 my-3">
                    <img alt="PFA AWARDS" class="img-fluid" src="/storage/images/PFA_Logo_inline_PMS2 1.svg">
                </div>
                <div class=" col-6 my-3 pe-4">
                    <div class="align-center text-end">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a target="_blank" href="https://osky.com.au">
                                    <img src="/storage/images/question.svg" class="img-fluid me-2">
                                    <div class="wrap">
                                        About
                                    </div>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a target="_blank" href="https://osky.com.au">
                                    <img src="/storage/images/support-alt.svg" class="img-fluid me-2">
                                    <div class="wrap">
                                        Support
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a target="_blank" href="https://osky.com.au">
                                    <img src="/storage/images/contact-alt.svg" class="img-fluid me-2">
                                    <div class="wrap">
                                        Contact
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-sm-12 mb-3">
                    <div class="text-center">
                        <p class="copyright">{{ new Date().getFullYear() }} © Police Federation of Australia. Developed by <a
                                class="text-decoration-underline" target="_blank" href="https://osky.com.au">Osky</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
