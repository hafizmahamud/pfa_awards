<script setup>
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

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    telephone: props.user.telephone,
    mobile: props.user.mobile,
    jurisdiction: props.user.jurisdiction,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    Inertia.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
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
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form class="w-50 p-3">
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-4 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">

                <InputLabel for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div v-show="!photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'" />
                </div>

                <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
                    Select A New Photo
                </SecondaryButton>

                <SecondaryButton v-if="user.profile_photo_path" type="button" class="mt-2" @click.prevent="deletePhoto">
                    Remove Photo
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->

            <div class="details">
                <InputLabel for="name" value="Name" class="form-label" />
                <TextInput id="name" v-model="form.name" type="text" class="form-control" autocomplete="name" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="details pt-3">
                <InputLabel for="email" value="Email" class="form-label" />
                <TextInput id="email" v-model="form.email" type="email" class="form-control" />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2">
                        Your email address is unverified.

                        <Link :href="route('verification.send')" method="post" as="button"
                            class="underline text-gray-600 hover:text-gray-900" @click.prevent="sendEmailVerification">
                        Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        A new verification link has been sent to your email address.
                    </div>
                </div>
            </div>


            <!-- Telephone -->
            <div class="details pt-3">
                <InputLabel for="telephone" value="Telephone" class="form-label" />
                <TextInput id="telephone" v-model="form.telephone" type="text" class="form-control"
                    autocomplete="telephone" />
                <InputError :message="form.errors.telephone" class="mt-2" />
            </div>


            <!-- Mobile -->
            <div class="details pt-3">
                <InputLabel for="mobile" value="Mobile" class="form-label" />
                <TextInput id="mobile" v-model="form.mobile" type="text" class="form-control" autocomplete="mobile" />
                <InputError :message="form.errors.mobile" class="mt-2" />
            </div>


            <!-- jurisdiction -->
            <div class="details pt-3 ">
                <InputLabel for="jurisdiction" value="Jurisdiction" class="form-label" />
                <!--<TextInput
                    id="jurisdiction"
                    v-model="form.jurisdiction"
                    type="text"
                    class="form-control"
                    autocomplete="jurisdiction"
                />-->
                <select class="form-select form-inline mw-s" aria-label=".form-select-sm example"
                    v-model="form.jurisdiction">
                    <option value="Federal">Federal</option>
                    <option value="NSW">NSW</option>
                    <option value="VIC">VIC</option>
                    <option value="QLD">QLD</option>
                    <option value="SA">SA</option>
                    <option value="WA">WA</option>
                    <option value="TAS">TAS</option>
                    <option value="NT">NT</option>
                    <option value="NZ">NZ</option>
                </select>
                <InputError :message="form.errors.jurisdiction" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <div>
                <button class="{ 'opacity-25': form.processing } .btn btn-primary " :disabled="form.processing"
                    type="submit">Update Profile</button>
            </div>
            <!--<PrimaryButton :class="{ 'opacity-25': form.processing  } " :disabled="form.processing">
                Save
            </PrimaryButton>-->
        </template>
    </FormSection>
</template>
