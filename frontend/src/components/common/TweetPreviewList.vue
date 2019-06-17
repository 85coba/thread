<template>
  <div class="tweets-container">
    <transition-group name="slide-prev" tag="div">
      <template v-for="tweet in tweets">
        <TweetPreview v-if="!isCardPriview" :key="tweet.id" 
            :tweet="tweet" 
            :user="user" 
            @click="onTweetClick"/>
        
        <TweetPreviewCard v-else :key="tweet.id" 
            :tweet="tweet" 
            :user="user" 
            @click="onTweetClick"/>
      </template>
    </transition-group>
    <infinite-loading @infinite="infiniteHandler">
      <div slot="no-more"/>
      <div slot="no-results"/>
      <div slot="spinner"/>
    </infinite-loading>
  </div>
</template>

<script>
import InfiniteLoading from "vue-infinite-loading";
import TweetPreview from "./TweetPreview.vue";
import TweetPreviewCard from "./TweetPreviewCard";
import { mapGetters } from "vuex";

export default {
  name: "TweetPreviewList",

  props: {
    tweets: {
      type: Array,
      required: true
    }
  },

  components: {
    TweetPreview,
    InfiniteLoading,
    TweetPreviewCard
  },

  computed: {
    ...mapGetters("auth", {
      user: "getAuthenticatedUser"
    }),

    ...mapGetters("tweet", {
      isCardPriview : "isCardPriview"
    })
  },

  methods: {
    onTweetClick(tweet) {
      this.$router.push({ name: "tweet-page", params: { id: tweet.id } });
    },

    infiniteHandler($state) {
      this.$emit("infinite", $state);
    }
  }
};
</script>

<style scoped lang="scss">
.tweets-container {
  padding-bottom: 20px;
}
</style>
