<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>

    <Head title="Log in" />
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
                            <div class="tab-pane fade show active" id="nav-return" role="tabpanel"
                                aria-labelledby="nav-return-tab">
                                <form @submit.prevent="submit">
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="inputEmail" class="form-label">
                                         Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                                        </label>
                                    </div>

                                    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                                    {{ status }}
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="inputEmail" class="for-label">Email Address:</label>
                                        <input v-model="form.email" type="email" class="form-control" autocomplete="email"
                                            id="inputEmail" required autofocus>
                                            <div v-if="form.errors.email" v-text="form.errors.email" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    

                                    <div class="flex items-center justify-end mt-4">
                                        <button class="{ 'opacity-25': form.processing } .btn btn-primary "
                                            :disabled="form.processing" type="submit">Email Password Reset Link</button>
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
