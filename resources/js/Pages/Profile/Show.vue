<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    user: Object
});
</script>

<script>
export default {
    data() {
        return {
            canUpdateProfileInformation: false,
            seen: true
        }
    }
}
</script>


<template>
    <AppLayout title="Profile">
        <template #header>
            <div class="row g-0">
                <div class="col 12 col-md-8 my-5">
                    <h2>Profile</h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="main">
                        <div class="container pb-5">
                            <div class="row">
                                <div class="col-lg-6">
                                    <!-- <Transition> -->
                                    <div v-show="canUpdateProfileInformation" class="col-12 col-md-12">
                                        <UpdateProfileInformationForm :user="$page.props.user" />

                                        <SectionBorder />
                                    </div>
                                    <!-- </Transition> -->
                                    <!-- <Transition> -->
                                    <div v-show="!canUpdateProfileInformation" id="hide" class="col-12 col-md-6">
                                        <h3 class="mb-4">{{ user.name }}</h3>
                                        <p class="email mb-4"><i class="bi bi-at me-2"></i>{{ user.email }}</p>
                                        <p class="phone mb-4"><i class="bi bi-telephone me-2"></i>{{ user.telephone
}}</p>
                                        <p class="mobile mb-4"><i class="bi bi-phone me-2"></i>{{ user.mobile }}</p>
                                        <p class="jurisdiction mb-4"><i class="bi bi-geo-alt me-2"></i>{{
        user.jurisdiction
}}</p>
                                        <p class="border-top border-color-third pt-3">
                                            <button @click="canUpdateProfileInformation = !canUpdateProfileInformation"
                                                class="btn btn-primary">Edit User Info</button>
                                        </p>

                                    </div>
                                    <!-- </Transition> -->
                                </div>

                                <div v-if="$page.props.jetstream.canUpdatePassword" style="flex-grow; 1"
                                    class="col-lg-6">
                                    <UpdatePasswordForm />

                                    <SectionBorder />

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>





    </AppLayout>
</template>


