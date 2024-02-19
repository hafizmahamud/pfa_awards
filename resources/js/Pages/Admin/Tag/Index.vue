<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AddOnHeader from '../../../Components/AddOnHeader.vue';
import Welcome from '@/Components/Welcome.vue';
import FormSection from '@/Components/FormSection.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import draggable from 'vuedraggable'


const props = defineProps({
    tags: {
        type: Object,
        default: () => ({}),
    },
});

console.log(props.tags)

const form = useForm({
    _method: 'POST',
    name: props.tags.name,
    parenttag: props.tags.parent_id
});

const submit = () => {
    form.post(route("tag.store"), {
        onSuccess: () => {
            form.reset('name');
        },
    });
};

const formDelete = useForm({})
function destroy(id) {
    if (confirm("Are you sure you want to delete this tag?")) {
        formDelete.delete(route('tag.destroy', id));
    }
};

let jumpTo = (selected) => {
    Inertia.get(route(selected));
}

</script>

<script>
export default {
    data() {
        return {
            tagsNew: this.tags,
            //csrf: document.head.querySelector('meta[name="csrf-token"]').content
        }
    },

    methods: {
        update() {
            this.tagsNew.map((tag, index) => {
                tag.order = index + 1;
            })

            axios.post(route('tag.updateAll'), {
                tags: this.tagsNew
            }).then((response) => {
                //success
            })
        }
    },
}
</script>

<template>
    <AppLayout title="Manage Tag">
        <template #header>
            <AddOnHeader prefix="admin" title="Add or Edit Tags" selected="tag.index" @jump-to="jumpTo">
            </AddOnHeader>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="main">
                        <div class="container pb-5">
                            <div class="row">
                                <div class="col-lg-6">
                                    <!--<ul class="list-group">
                                                <li class="list-group-item" v-for="tag in tags" :key="tag.id">
                                                    <div>
                                                     <button type="button" class="updownbtn moveup fa fa-angle-up"></button>
                                                     <button type="button" class="updownbtn movedown fa fa-angle-down"></button>
                                                     <Link :href="route('orderup',tag.id)">UP</Link>

                                                    {{ tag.name }}</div>
                                                    <div>
                                                    <Link :href="route('tag.edit',tag.id)">Edit</Link>
                                                    <Link  @click="destroy(tag.id)"> Delete </Link>
                                                    </div>
                                                </li>
                                            </ul>-->

                                    <draggable :list="tags" item-key="name" @change="update">
                                        >
                                        <template #item="{ element }">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <div>{{ element.name }}</div>
                                                    <div>
                                                        <Link :href="route('tag.edit', element.id)"> Edit </Link>
                                                        <Link @click="destroy(element.id)"> Delete </Link>
                                                    </div>
                                                </li>
                                            </ul>
                                        </template>
                                    </draggable>
                                </div>
                                <div class="col-lg-6">
                                    <form @submit.prevent="submit">
                                        <div class="details p-2">
                                            <InputLabel for="name" value="Name" class="form-label" />
                                            <TextInput id="name" v-model="form.name" type="text" class="form-control"
                                                autocomplete="name" />
                                            <div v-if="form.errors.name" v-text="form.errors.name"
                                                class="text-danger text-xs mt-1"></div>
                                        </div>


                                        <!--<div class="details p-2">
                                                    <label class="me-3" for="tag">Parent Tag</label>
                                                        <select class="form-select form-inline mw-s" aria-label=".form-select-sm example"
                                                            v-model="form.parenttag">
                                                            <option value="" selected>Select Parent Tag</option>
                                                            <option v-for="tag in props.tags" :key="tag.id"
                                                                :value="tag.id">
                                                                {{ tag.name }}
                                                            </option>
                                                        </select>
                                            </div>-->
                                        <div class="col-12 p-2">
                                            <button class="{ 'opacity-25': form.processing } .btn btn-primary "
                                                :disabled="form.processing" type="submit">Add Tag</button>
                                        </div>

                                    </form>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </AppLayout>
</template>
