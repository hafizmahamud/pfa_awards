<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
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
                                        <h2 class="position-absolute top-0 py-4">Reset Your Password</h2>
                                    </div>

                                    <div>
                                    <label for="email" class="form-label">Email</label>
                                    <input v-model="form.email" type="email" class="form-control" id="email"
                                            required autofocus>
                                    <div v-if="form.errors.email" v-text="form.errors.email" class="text-danger text-xs mt-1"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input v-model="form.password" type="password" class="form-control" autocomplete="new-password"
                                            id="password" required auto-complete="new-password">
                                    <div v-if="form.errors.password" v-text="form.errors.password" class="text-danger text-xs mt-1"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="password_confirmation" class="form-label">Password Confirmation</label>
                                    <input v-model="form.password_confirmation" type="password" class="form-control" autocomplete="new-password"
                                            id="password_confirmation" required auto-complete="new-password">
                                    <div v-if="form.errors.password_confirmation" v-text="form.errors.password_confirmation" class="text-danger text-xs mt-1"></div>
                                </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <button class="{ 'opacity-25': form.processing } .btn btn-primary "
                                            :disabled="form.processing" type="submit">Reset Password</button>
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
                <p>{{ new Date().getFullYear() }}© Police Federation of Australia</p>
                <p>Developed by OSKY</p>
            </div>
        </div>
    </div>
    <img src="/storage/images/pfa-logo.png" class="img-fluid ft-logo position-absolute">
</template>
