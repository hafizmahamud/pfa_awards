<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import PaginationLink from '@/Components/PaginationLink.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import TableFooter from '@/Components/TableFooter.vue';

const props = defineProps({
    users: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

console.log(props.filters);

// const form = useForm({
// let search = ref(props.filters.search);
// })

const form = useForm();

let search = ref(props.filters.search);

function filterUser() {
    Inertia.get(route('user.index'), {
        search: search.value,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}

watch(search, value => {
    filterUser();
});

const formDelete = useForm({})
function destroy(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        formDelete.delete(route('user.destroy', id));
    }
}

function accept(id) {
    // console.log(id);
    Inertia.post(route('user.approve', id));
}

function decline(id) {
    Inertia.post(route('user.decline', id));
}

</script>


<template>
    <AppLayout title="Manage User">
        <template #header>
            <AddOnHeader prefix="user" title="Manage User" />
        </template>


        <div class="mb-5 pb-3 app-cont">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- <Welcome /> -->

                    <!-- Searching Box Column -->
                    <div class="container-fluid pb-4 mb-5 px-0 border-bottom border-color-third">

                        <div class="row align-items-center g-0 c-width">
                            <div class="col-6 my-2 d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                </div>
                            </div>

                            <div class="col-6 banner fil-search">
                                <div class="row height d-flex justify-content-end align-items-end">
                                    <div class="input-group position-relative">
                                        <input v-model="search" placeholder="Search"
                                            class="form-control border-color-third rounded-pill" type="search"
                                            id="search-input">
                                        <span class="input-group-append position-absolute">
                                            <button
                                                class="btn btn-outline-secondary bg-white border-0 ms-n5 rounded-pill font-secondary"
                                                type="button"><i class="bi bi-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="main">
                        <div class="container pb-5">
                            <div class="row">
                                <table class="table table-user">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Telephone</th>
                                            <th scope="col">Mobile</th>
                                            <th scope="col">Jurisdiction</th>
                                            <th scope="col" class="py-3 px-6">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users.data" :key="user.id"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td data-label="Name" class="py-4 px-6">
                                                {{ user.name }}
                                            </td>
                                            <td data-label="Email" class="py-4 px-6">
                                                {{ user.email }}
                                            </td>
                                            <td data-label="Telephone" class="py-4 px-6">
                                                {{ user.telephone }}
                                            </td>
                                            <td data-label="Mobile" class="py-4 px-6">
                                                {{ user.mobile }}
                                            </td>
                                            <td data-label="Jurisdiction" class="py-4 px-6">
                                                {{ user.jurisdiction }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <div>
                                                    <Link class="btn btn-info me-2" :href="route('user.edit', user.id)">
                                                    Edit
                                                    </Link>
                                                    <Link class="btn btn-danger me-2" as="button" @click="destroy(user.id)">
                                                    Delete</Link>
                                                    <Link v-if="user.approved == null" class="btn btn-success me-2" as="button"
                                                        @click="accept(user.id)">
                                                    Accept</Link>
                                                    <Link v-if="user.approved == null" class="btn btn-danger" as="button" @click="decline(user.id)">
                                                    Decline</Link>
                                                </div>
                                                <!-- <div v-if="user.approved == null" class="mt-2">
                                                    <Link class="btn btn-success me-2" as="button"
                                                        @click="accept(user.id)">
                                                    Accept</Link>
                                                    <Link class="btn btn-danger" as="button" @click="decline(user.id)">
                                                    Decline</Link>
                                                </div> -->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <!-- <div class="col-12 col-md-12">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end d-flex align-items-center">
                                        <PaginationLink v-for="link in users.links" :href="link.url"
                                            :active="link.active" :label="link.label"></PaginationLink>
                                    </ul>
                                </nav>
                            </div> -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template #footer>
            <TableFooter :items="users" v-model="per_page" />
        </template>

    </AppLayout>
</template>
