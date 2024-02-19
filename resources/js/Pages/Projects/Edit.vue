<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Multiselect from '@vueform/multiselect';
import AddOnHeader from '@/Components/AddOnHeader.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    project: {
        type: Object,
        default: () => ({}),
    },
    selected_jurisdictions: Array,
    selected_tags: Array,
    selected_users: Array,
    jurisdictions: Object,
    tags: Object,
    members: Object,
    users: Object,
});

// console.log(props.members);

const form = useForm({
    title: props.project.title,
    owner: props.project.user_id,
    description: props.project.content,
    jurisdictions: props.selected_jurisdictions,
    members: props.selected_users,
    tags: props.selected_tags,
});


let submit = () => {
    form.put(route("projects.update", props.project.id));
}

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}
</script>

<template>
    <AppLayout title="Documents">
        <template #header>
            <AddOnHeader prefix="projects" title="Add or edit Project" @jump-to="jumpTo" />
        </template>

        <div class="container">
            <form @submit.prevent="submit">
                <div class="row g-0 mt-5">
                    <div class="col-12 col-lg-6 mb-4">
                        <div class="form-group row">
                            <label for="project-title" class="col-sm-4 mb-4 col-form-label">Project Title : </label>
                            <div class="col-sm-8">
                                <input type="text" id="project-title" class="form-control" v-model="form.title">
                                <div v-if="form.errors.title" v-text="form.errors.title"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="project-title" class="col-sm-4 mb-4 col-form-label">Owner : </label>
                            <div class="col-sm-8">
                                <Multiselect v-model="form.owner" :options="members" />
                                <div v-if="form.errors.owner" v-text="form.errors.owner"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="project-description" class="col-sm-4 mb-4 col-form-label">Description : </label>
                            <div class="col-sm-8 mb-4">
                                <textarea type="text" id="project-description" class="form-control"
                                    v-model="form.description"></textarea>
                                <div v-if="form.errors.description" v-text="form.errors.description"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="add-member" class="col-sm-4 mb-4 col-form-label">Add Members : </label>
                            <div class="col-sm-8 mb-4">
                                <Multiselect v-model="form.members" :options="members" mode="tags"
                                    :close-on-select="false" :clear-on-select="false" :preserve-search="true"
                                    :hide-selected="true" :show-labels="false" :placeholder="'Select Members'"
                                    :searchable="true" />
                                <div v-if="form.errors.members" v-text="form.errors.members"
                                    class="text-danger text-xs mt-1"></div>
                            </div>
                        </div>
                        <div class="form-group row" v-show="form.members.length > 0">
                            <label for="list-members" class="col-sm-4 mb-4 col-form-label">Members : </label>
                            <div class="col-sm-8 mt-3">
                                <p type="button" v-for="(member, $index) in form.members" :key="$index">
                                    <i class="bi bi-x-lg me-2" @click.prevent="form.members.splice($index, 1)"></i>
                                    {{ members.filter((m) => m.value == member)[0]?.label }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 mb-4 ps-lg-5">
                        <div class="Jurisdiction mb-4">
                            <legend class="mb-3"><b>Jurisdiction</b></legend>
                            <div class="row">
                                <div class="col-12 my-3 d-flex flex-wrap">
                                    <div class="form-check form-check-inline" v-for="jurisdiction in jurisdictions"
                                        :key="jurisdiction.id">
                                        <input v-model="form.jurisdictions" class="form-check-input" type="checkbox"
                                            v-bind:id="'inlineCheckbox' + jurisdiction.id" :value="jurisdiction.id">
                                        <label class="form-check-label" v-bind:for="'inlineCheckbox' + jurisdiction.id">
                                            {{ jurisdiction.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="topic-tags mb-4">
                            <legend class="mb-3"><b>Topic tags</b></legend>
                            <div class="row">
                                <div class="col-12  my-3 d-flex flex-wrap">
                                    <div v-for="tag in tags" class="form-check form-check-inline" :key="tag.id">
                                        <input v-model="form.tags" class="form-check-input" type="checkbox"
                                            v-bind:id="'inlineCheckbox' + tag.id" :value="tag.id">
                                        <label class="form-check-label" v-bind:for="'inlineCheckbox' + tag.id">{{
                                            tag.name
                                        }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center text-lg-end d-grid gap-2 d-lg-block mb-5">
                    <button id="publish" class="btn-primary">Publish</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style src="@vueform/multiselect/themes/default.css">

</style>
