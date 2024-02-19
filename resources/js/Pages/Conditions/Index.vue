<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AddOnHeader from '@/Components/AddOnHeader.vue';

const props = defineProps({
    categories: Object,
    author: Object
});

// console.log(props.categories);

</script>

<template>
    <AppLayout title="Matrixes">
        <template #header>
            <AddOnHeader prefix="conditions" title="Matrixes" />
        </template>
        <div class="mb-5 pb-5">
            <div class="container">
                <h3 class="text-lg-start text-center my-5">Select your category</h3>
            </div>
            <div class="app-cont cat-list">
                <div class="container">
                    <div class="row g-0">

                        <div class="col-12 col-md-3 mt-3 mb-5" v-for="category in props.categories" :key="category.id">

                            <a :href="route('conditions.category', category.id)">

                                <div class="matrix"> <img :src="'storage/images/' + category.image"
                                        class="mx-auto d-block" :alt="category.name"></div>
                                <p class="text-center">{{ category.name }}</p>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
