<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import Checkbox from '@/Components/Checkbox.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onSuccess: () => form.reset('password'),
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

                <div class="">
                    <div class="register-tab position-relative">
                        <nav>
                            <div class="nav navbar nav-tabs pb-3 pt-5" id="nav-tab" role="tablist">
                                <h2 class="w-100 text-center">Please Sign In</h2>
                                <Link :href="route('register')" class="nav-link" as="button"
                                    :class="{ 'active': route().current('register') }">NEW</Link>
                                <Link :href="route('login')" class="nav-link" as="button"
                                    :class="{ 'active': route().current('login') }">RETURNING</Link>
                            </div>
                        </nav>

                        <div class="tab-content d-flex justify-content-center" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-return" role="tabpanel"
                                aria-labelledby="nav-return-tab">
                                <form @submit.prevent="submit" class="row g-1 col-sm-12 pt-3">
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="inputEmail" class="form-label">Email</label>
                                        <input v-model="form.email" type="email" class="form-control" id="inputEmail"
                                            required autofocus>
                                            <div v-if="form.errors.email" v-text="form.errors.email" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="inputPassword" class="for-label">Password</label>
                                        <input v-model="form.password" type="password" class="form-control" autocomplete="password"
                                            id="inputPassword" required auto-complete="current-password">
                                            <div v-if="form.errors.password" v-text="form.errors.password" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <Link v-if="canResetPassword" :href="route('password.request')">
                                        Forgot your password?
                                        </Link>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-check-inline justify-content-start">
                                            <Checkbox v-model:checked="form.remember" class="form-check-input"
                                                type="checkbox" id="remember-me" name="remember" />
                                            <label class="form-check-label" for="remember-me">Remember me</label>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-12 p-3 text-center d-grid">
                                        <button class="btn btn-primary" type="submit">Sign In</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 position-relative">
                        <div class="position-absolute bottom-0">
                            <img class="register-star-img" src="/storage/images/footer-register.svg" alt="image">
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
