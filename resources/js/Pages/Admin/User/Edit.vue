<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';


import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    userdata: {
        type: Object,
        default: () => ({}),
    },
    roles: {
        type: Object,
        default: () => ({}),
    },
    jurisdictions: Object
});


const form = useForm({
    _method: 'PUT',
    password: '',
    name: props.userdata.name,
    email: props.userdata.email,
    telephone: props.userdata.telephone,
    mobile: props.userdata.mobile,
    jurisdiction: props.userdata.jurisdiction,
    role: props.userdata.role,
    photo: null,
});

const submit = () => {
    form.put(route("user.update", props.userdata.id));
};

</script>
<script>

</script>
<template>
    <AppLayout title="Edit User">
        <template #header>
            <div class="col-sm-8 my-5">
                <h2>Edit Users</h2>
            </div>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- <Welcome /> -->
                    <div class="main">
                        <div class="container pb-5">
                            <div class="row g-0">
                                <!-- Edit User FORM Here -->
                                <form @submit.prevent="submit">
                                    <div class="details">
                                        <InputLabel for="name" value="Name" class="form-label" />
                                        <TextInput id="name" v-model="form.name" type="text" class="form-control"
                                            autocomplete="name" />
                                        <div v-if="form.errors.name" v-text="form.errors.name" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="details">
                                        <InputLabel for="email" value="Email" class="form-label" />
                                        <TextInput id="email" v-model="form.email" type="email" class="form-control" />
                                        <div v-if="form.errors.email" v-text="form.errors.email" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="details">
                                        <InputLabel for="telephone" value="Telephone" class="form-label" />
                                        <TextInput id="telephone" v-model="form.telephone" type="text"
                                            class="form-control" autocomplete="telephone" />
                                        <div v-if="form.errors.telephone" v-text="form.errors.telephone" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="details">
                                        <InputLabel for="mobile" value="Mobile" class="form-label" />
                                        <TextInput id="mobile" v-model="form.mobile" type="text" class="form-control"
                                            autocomplete="mobile" />
                                       <div v-if="form.errors.mobile" v-text="form.errors.mobile" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <!-- <div class="details ">
                                        <InputLabel for="jurisdiction" value="Jurisdiction" class="form-label" />
                                        <TextInput
                                            id="jurisdiction"
                                            v-model="form.jurisdiction"
                                            type="text"
                                            class="form-control"
                                            autocomplete="jurisdiction"
                                        />
                                        <InputError :message="form.errors.jurisdiction" class="mt-2" />
                                    </div>-->
                                    <div class="details">
                                        <label class="me-3" for="tag">Jurisdiction</label>
                                            <select class="form-select form-inline mw-s" aria-label=".form-select-sm example"
                                                v-model="form.jurisdiction">
                                                <option value="" selected>Select Jurisdiction</option>
                                                <option v-for="jurisdiction in props.jurisdictions" :key="jurisdiction.id"
                                                    :value="jurisdiction.name">
                                                    {{ jurisdiction.name }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.jurisdiction" v-text="form.errors.jurisdiction" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <div class="details">
                                        <label class="me-3" for="tag">Role</label>
                                            <select class="form-select form-inline mw-s" aria-label=".form-select-sm example"
                                                v-model="form.role">
                                                <option value="" selected>Select Role</option>
                                                <option v-for="role in props.roles" :key="role.id"
                                                    :value="role.name">
                                                    {{ role.name }}
                                                </option>
                                            </select>
                                            <div v-if="form.errors.role" v-text="form.errors.role" class="text-danger text-xs mt-1"></div>
                                    </div>

                                    <!--<div class="details" id="checkbox-password">
                                        <InputLabel for="password" value="Password" class="form-label" />
                                        <TextInput
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            class="form-control"
                                            autocomplete="jurisdiction"
                                        />
                                       <div v-if="form.errors.password" v-text="form.errors.password" class="text-danger text-xs mt-1"></div>
                                    </div>-->

                                    <!--<div class="details">
                                        <InputLabel for="password" value="Password" class="form-label" />
                                        <TextInput
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            class="form-control"
                                            autocomplete="jurisdiction"
                                        />
                                       <div v-if="form.errors.password" v-text="form.errors.password" class="text-danger text-xs mt-1"></div>
                                    </div>-->

                                    <div class="details">
                                       <div class="col-12 col-md-2 my-3">
                                            <p>Generate New Password :</p>
                                            <input class="form-check-input" type="checkbox" name="password" id="password" value="password" v-model="form.password">
                                        </div>
                                        
                                    </div>

                                    <div class="p-3">
                                        <button class="{ 'opacity-25': form.processing } .btn btn-primary "
                                            :disabled="form.processing" type="submit">Update Profile</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
