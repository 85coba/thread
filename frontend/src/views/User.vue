<template>
    <div class="container desktop">
        <UserContainer v-if="$route.query.type === 'myTweets'"/>
        <UserContainerLiked v-if="$route.query.type === 'likedTweets'"/>
    </div>
</template>

<script>
import UserContainer from '@/components/view/user/UserContainer.vue';
import UserContainerLiked from '@/components/view/user/UserContainerLiked.vue';

export default {
    name: 'UserPage',
    components: {
        UserContainer,
        UserContainerLiked,
    },
};
</script>

<style scoped>

</style>
