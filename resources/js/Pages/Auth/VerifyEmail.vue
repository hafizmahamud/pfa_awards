<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />
    <img class="img-fluid position-absolute tp-light" src="/storage/images/transparent-light.png">
    <div class="register bg-primary py-5">
        <div class="position-relative text-center py-4">
            <a href="#">
                <img class="text-center img-fluid main-logo" alt="PFA Logo" src="/storage/images/logo-alt.svg">
            </a>
        </div>
        <div class="container">
            <div class="d-flex justify-content-center">

                <div class="col-md-5">
                    <div class="register-tab position-relative">
                        <div class="nav navbar nav-tabs pb-3 pt-5" id="nav-tab" role="tablist">
                            </div>

                        <div class="tab-content d-flex justify-content-center" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-return" role="tabpanel" aria-labelledby="nav-return-tab">
                                
                                    <div class="col-md-12 mb-3">
                                          Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                                    </div>
                                    <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600">
                                            A new verification link has been sent to the email address you provided in your profile settings.
                                    </div>

                                    
                                <form @submit.prevent="submit">
                                    <div class="flex d-flex justify-content-center mt-4">
                                        <button class="{ 'opacity-25': form.processing } .btn btn-primary "
                                            :disabled="form.processing" type="submit">Resend Verification Email</button>
                                    </div>
                                    <div class="flex d-flex justify-content-center mt-4">
                                    <Link
                                        :href="route('profile.show')"
                                        class="btn btn-light btn-sm text-sm text-gray-600 hover:text-gray-900">
                                        Edit Profile
                                    </Link>
                                    <Link
                                        :href="route('logout')"
                                        method="post" as="button"
                                        class="btn pl-5 btn-light btn-sm text-sm text-gray-600 hover:text-gray-900 ml-2">
                                        Log Out
                                    </Link>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 position-relative">
                        <div class="position-absolute bottom-0">
                            <img src="/storage/images/footer-register.svg" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-register mt-2">
            <div class="col-12 col-md-12 py-4 text-center">
                <p>{{ new Date().getFullYear() }} © Police Federation of Australia</p>
                <p>Developed by OSKY</p>
            </div>
        </div>
    </div>
    <img src="/storage/images/pfa-logo.png" class="img-fluid ft-logo position-absolute">
</template>
