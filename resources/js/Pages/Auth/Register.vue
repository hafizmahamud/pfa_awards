<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    role: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<!--<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="col-md-12 mb-3">
                <label for="inputPhone" class="form-label">Phone</label>
                <TextInput
                                                id="phone"
                                                v-model="form.phone"
                                                type="text"
                                                class="form-control"
                                                autocomplete="phone"
                                            />
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <div class="col-md-12 mb-3">
                                        <label for="inputRole" class="form-label">Select Role</label>
                                        <select id="role" class="form-select form-select-md mb-3" aria-label=".form-select-lg"  v-model="form.role">
                                            <option selected="" value="2">Industrial Officer</option>
                                            <option value="3">Researcher</option>
                                        </select>
                                        <InputError :message="form.errors.role" class="mt-2" />
            </div>


            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ml-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Already registered?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>-->

<template>

    <Head title="Register" />
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
                                <h2 class="w-100 text-center">Register</h2>
                                <Link :href="route('register')" class="nav-link" as="button"
                                    :class="{ 'active': route().current('register') }">NEW</Link>
                                <Link :href="route('login')" class="nav-link" as="button"
                                    :class="{ 'active': route().current('login') }">RETURNING</Link>
                            </div>
                        </nav>


                        <div class="tab-content d-flex justify-content-center" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-new" role="tabpanel"
                                aria-labelledby="nav-new-tab">
                                <form @submit.prevent="submit" class="row g-1 col-sm-12 pt-3">
                                    <div class="col-md-12 mb-3">
                                        <label for="inputName" class="form-label">Full name</label>
                                        
                                        <TextInput
                                                id="name"
                                                v-model="form.name"
                                                type="text"
                                                class="form-control"
                                                autocomplete="name"
                                            />
                                            <div v-if="form.errors.name" v-text="form.errors.name" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="inputEmail4" class="form-label">Email</label>
                                        <TextInput
                                                id="email"
                                                v-model="form.email"
                                                type="text"
                                                class="form-control"
                                                autocomplete="email"
                                            />
                                            <div v-if="form.errors.email" v-text="form.errors.email" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="inputPhone" class="form-label">Phone</label>
                                        <TextInput
                                                id="phone"
                                                v-model="form.phone"
                                                type="text"
                                                class="form-control"
                                                autocomplete="phone"
                                            />
                                            <div v-if="form.errors.phone" v-text="form.errors.phone" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="inputRole" class="form-label">Select Role</label>
                                        <select id="role" class="form-select form-select-md mb-3" aria-label=".form-select-lg"  v-model="form.role">
                                            <option selected="" value="Industrial Officer">Industrial Officer</option>
                                            <option value="Researcher">Researcher</option>
                                        </select>
                                        <div v-if="form.errors.role" v-text="form.errors.role" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="inputPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="inputPassword"
                                            autocomplete="password" v-model="form.password">
                                            <div v-if="form.errors.password" v-text="form.errors.password" class="text-danger text-xs mt-1"></div>
                                    </div>
                                   

                                    <div class="col-md-12 mb-3">
                                        <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="inputConfirmPassword"
                                            autocomplete="confirm-password" v-model="form.password_confirmation">
                                            <div v-if="form.errors.password_confirmation" v-text="form.errors.password_confirmation" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class=" col-sm-12 text-center d-grid">
                                        <!--<button class="btn btn-primary p-2" type="submit">Register</button>-->
                                        <button class="{ 'opacity-25': form.processing } .btn btn-primary "
                                            :disabled="form.processing" type="submit">Submit</button>
                                        <!-- <Link :href="route('login')"
                                            class="underline text-sm text-gray-600 hover:text-gray-900">
                                        Already registered?
                                        </Link> -->
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

